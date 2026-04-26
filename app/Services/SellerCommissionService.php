<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SellerCommissionPeriod;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SellerCommissionService
{
    private const PERIOD_DAYS = 21;
    private const DEADLINE_DAYS = 7;

    public function currentPeriodForSeller(User $seller): SellerCommissionPeriod
    {
        [$start, $end] = $this->resolveCurrentWindow(now());

        return $this->syncPeriod($seller, $start, $end);
    }

    public function historyForSeller(User $seller): Collection
    {
        $this->currentPeriodForSeller($seller);

        return SellerCommissionPeriod::query()
            ->where('seller_id', $seller->id)
            ->latest('period_start')
            ->get()
            ->map(fn (SellerCommissionPeriod $period) => $this->applyDerivedStatus($period));
    }

    public function submitCurrentPeriod(User $seller, ?string $note = null): SellerCommissionPeriod
    {
        $period = $this->currentPeriodForSeller($seller);

        if ($period->status === 'paid') {
            throw new \RuntimeException('Perioada este deja marcata ca platita.');
        }

        if ((float) $period->commission_amount <= 0) {
            throw new \RuntimeException('Nu exista comision de confirmat pentru perioada curenta.');
        }

        $period->update([
            'status' => 'awaiting_admin_review',
            'seller_note' => $note,
            'submitted_at' => now(),
        ]);

        return $period->fresh();
    }

    public function markAdminStatus(SellerCommissionPeriod $period, string $status, ?string $adminNote = null): SellerCommissionPeriod
    {
        $allowed = ['unpaid', 'awaiting_admin_review', 'paid', 'overdue'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Statusul perioadei de comision nu este valid.');
        }

        $period->status = $status;
        $period->admin_note = $adminNote;
        $period->reviewed_at = now();

        if ($status === 'paid') {
            $period->paid_at = now();
        } elseif ($period->paid_at && $status !== 'paid') {
            $period->paid_at = null;
        }

        $period->save();

        return $period->fresh();
    }

    public function syncAllActiveSellers(): void
    {
        User::query()
            ->where('role', 'seller')
            ->where('seller_status', 'approved')
            ->with('sellerProfile')
            ->each(function (User $seller) {
                $this->currentPeriodForSeller($seller);
                $this->syncPreviousWindow($seller);
            });
    }

    public function ordersForPeriod(SellerCommissionPeriod $period): Collection
    {
        return $this->sellerPaidOrdersQuery($period->seller_id, $period->period_start, $period->period_end)
            ->with('items')
            ->orderBy('paid_at')
            ->get()
            ->map(function (Order $order) use ($period) {
                $gross = $this->effectiveGrossForOrder($order);
                $commission = round($gross * ((float) $period->commission_percent / 100), 2);

                return (object) [
                    'order' => $order,
                    'gross' => $gross,
                    'commission_percent' => (float) $period->commission_percent,
                    'commission' => $commission,
                ];
            });
    }

    private function syncPreviousWindow(User $seller): void
    {
        [$start] = $this->resolveCurrentWindow(now());
        $previousEnd = $start->copy()->subDay();
        $previousStart = $previousEnd->copy()->subDays(self::PERIOD_DAYS - 1);

        $this->syncPeriod($seller, $previousStart, $previousEnd);
    }

    private function syncPeriod(User $seller, CarbonInterface $start, CarbonInterface $end): SellerCommissionPeriod
    {
        $deadline = Carbon::parse($end)->addDays(self::DEADLINE_DAYS);
        $commissionPercent = (float) ($seller->sellerProfile?->commission_percent ?? 10);

        $gross = $this->sellerPaidOrdersQuery($seller->id, $start, $end)
            ->with('items')
            ->get()
            ->sum(fn (Order $order) => $this->effectiveGrossForOrder($order));

        $period = SellerCommissionPeriod::query()->firstOrNew([
            'seller_id' => $seller->id,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
        ]);

        $period->deadline_at = $deadline->toDateString();
        $period->gross_sales_amount = round($gross, 2);
        $period->commission_percent = $commissionPercent;
        $period->commission_amount = round($gross * ($commissionPercent / 100), 2);
        $period->status = $this->deriveStatus($period, $end, $deadline);
        $period->save();

        return $period->fresh();
    }

    private function sellerPaidOrdersQuery(int $sellerId, CarbonInterface $start, CarbonInterface $end)
    {
        return Order::query()
            ->where('seller_id', $sellerId)
            ->where('payment_flow', 'seller_direct')
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $start->toDateString())
            ->whereDate('paid_at', '<=', $end->toDateString());
    }

    private function effectiveGrossForOrder(Order $order): float
    {
        $items = $order->relationLoaded('items')
            ? $order->items
            : $order->items()->get();

        return round((float) $items
            ->reject(fn ($item) => in_array($item->financial_status, ['cancelled', 'refunded'], true))
            ->sum(fn ($item) => (float) $item->price * (int) $item->qty), 2);
    }

    private function resolveCurrentWindow(CarbonInterface $date): array
    {
        $anchor = Carbon::create(2026, 1, 5, 0, 0, 0, config('app.timezone'));
        $daysDiff = $anchor->diffInDays($date, false);
        $periodIndex = (int) floor($daysDiff / self::PERIOD_DAYS);
        $periodStart = $anchor->copy()->addDays($periodIndex * self::PERIOD_DAYS)->startOfDay();
        $periodEnd = $periodStart->copy()->addDays(self::PERIOD_DAYS - 1)->endOfDay();

        return [$periodStart, $periodEnd];
    }

    private function deriveStatus(SellerCommissionPeriod $period, CarbonInterface $periodEnd, CarbonInterface $deadline): string
    {
        if ($period->status === 'paid' && $period->paid_at) {
            return 'paid';
        }

        if ($period->status === 'awaiting_admin_review' && $period->submitted_at) {
            return 'awaiting_admin_review';
        }

        if (now()->lt($periodEnd)) {
            return 'in_progress';
        }

        if ((float) $period->commission_amount <= 0) {
            return 'paid';
        }

        return now()->gt($deadline) ? 'overdue' : 'unpaid';
    }

    private function applyDerivedStatus(SellerCommissionPeriod $period): SellerCommissionPeriod
    {
        $period->status = $this->deriveStatus($period, Carbon::parse($period->period_end), Carbon::parse($period->deadline_at));
        return $period;
    }
}
