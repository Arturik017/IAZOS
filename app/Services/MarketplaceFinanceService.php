<?php

namespace App\Services;

use App\Models\FinancialLedgerEntry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutBatch;
use App\Models\PayoutRequest;
use App\Models\SellerBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MarketplaceFinanceService
{
    public function allocateOrderPayment(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->loadMissing('items.seller.sellerProfile');

            foreach ($order->items as $item) {
                $lockedItem = OrderItem::query()->lockForUpdate()->find($item->id);
                if (!$lockedItem || $lockedItem->financial_status !== 'unpaid') {
                    continue;
                }

                $gross = round((float) $lockedItem->price * (int) $lockedItem->qty, 2);
                $commissionPercent = round((float) ($lockedItem->seller?->sellerProfile?->commission_percent ?? 10), 2);
                $commissionAmount = round($gross * ($commissionPercent / 100), 2);
                $sellerNet = round($gross - $commissionAmount, 2);

                $lockedItem->forceFill([
                    'gross_amount' => $gross,
                    'platform_commission_percent' => $commissionPercent,
                    'platform_commission_amount' => $commissionAmount,
                    'seller_net_amount' => $sellerNet,
                    'financial_status' => 'pending',
                    'financial_status_updated_at' => now(),
                    'admin_release_status' => 'not_requested',
                ])->save();

                $this->adjustBalance($lockedItem->seller_id, $sellerNet, 0, 0);
                $this->recordEntry([
                    'seller_id' => $lockedItem->seller_id,
                    'order_id' => $lockedItem->order_id,
                    'order_item_id' => $lockedItem->id,
                    'type' => 'seller_pending_credit',
                    'bucket' => 'pending',
                    'amount' => $sellerNet,
                    'description' => 'Suma seller blocata dupa payment success.',
                ]);

                $this->recordEntry([
                    'order_id' => $lockedItem->order_id,
                    'order_item_id' => $lockedItem->id,
                    'type' => 'platform_commission_credit',
                    'bucket' => 'platform_revenue',
                    'amount' => $commissionAmount,
                    'description' => 'Comision platforma calculat la incasare.',
                    'meta' => [
                        'seller_id' => $lockedItem->seller_id,
                        'commission_percent' => $commissionPercent,
                    ],
                ]);
            }
        });
    }

    public function markDeliveredBySeller(OrderItem $item): void
    {
        if ($item->financial_status !== 'pending') {
            return;
        }

        $item->forceFill([
            'seller_status' => 'delivered_pending_review',
            'seller_status_updated_at' => now(),
            'delivered_reported_at' => now(),
            'admin_release_status' => 'pending_review',
        ])->save();
    }

    public function approveRelease(OrderItem $item, User $admin, ?string $note = null): void
    {
        DB::transaction(function () use ($item, $admin, $note) {
            $item = OrderItem::query()->lockForUpdate()->findOrFail($item->id);

            if ($item->financial_status !== 'pending') {
                return;
            }

            $net = (float) $item->seller_net_amount;

            $this->adjustBalance($item->seller_id, -$net, $net, 0);

            $item->forceFill([
                'financial_status' => 'available',
                'financial_status_updated_at' => now(),
                'admin_release_status' => 'approved',
                'admin_released_at' => now(),
                'admin_released_by' => $admin->id,
                'admin_release_note' => $note,
            ])->save();

            $this->recordEntry([
                'seller_id' => $item->seller_id,
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'type' => 'seller_pending_debit',
                'bucket' => 'pending',
                'amount' => -$net,
                'description' => 'Suma scoasa din pending dupa aprobarea adminului.',
            ]);

            $this->recordEntry([
                'seller_id' => $item->seller_id,
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'type' => 'seller_available_credit',
                'bucket' => 'available',
                'amount' => $net,
                'description' => 'Suma disponibila pentru payout dupa aprobarea adminului.',
            ]);
        });
    }

    public function rejectRelease(OrderItem $item, User $admin, ?string $note = null): void
    {
        $item->forceFill([
            'admin_release_status' => 'rejected',
            'admin_released_by' => $admin->id,
            'admin_release_note' => $note,
            'seller_status_updated_at' => now(),
        ])->save();
    }

    public function cancelOrRefundItem(OrderItem $item, User $admin, string $reason, string $targetStatus = 'refunded'): void
    {
        DB::transaction(function () use ($item, $admin, $reason, $targetStatus) {
            $item = OrderItem::query()->lockForUpdate()->findOrFail($item->id);

            if (in_array($item->financial_status, ['cancelled', 'refunded'], true)) {
                return;
            }

            $net = (float) $item->seller_net_amount;
            $commission = (float) $item->platform_commission_amount;

            if ($item->financial_status === 'pending') {
                $this->adjustBalance($item->seller_id, -$net, 0, 0);
                $this->recordEntry([
                    'seller_id' => $item->seller_id,
                    'order_id' => $item->order_id,
                    'order_item_id' => $item->id,
                    'type' => 'seller_pending_reversal',
                    'bucket' => 'pending',
                    'amount' => -$net,
                    'description' => 'Reversare pending pentru item anulat sau rambursat.',
                ]);
            } elseif ($item->financial_status === 'available') {
                $this->adjustBalance($item->seller_id, 0, -$net, 0);
                $this->recordEntry([
                    'seller_id' => $item->seller_id,
                    'order_id' => $item->order_id,
                    'order_item_id' => $item->id,
                    'type' => 'seller_available_reversal',
                    'bucket' => 'available',
                    'amount' => -$net,
                    'description' => 'Reversare available pentru item anulat sau rambursat.',
                ]);
            } elseif ($item->financial_status === 'paid') {
                $this->adjustBalance($item->seller_id, 0, -$net, 0);
                $this->recordEntry([
                    'seller_id' => $item->seller_id,
                    'order_id' => $item->order_id,
                    'order_item_id' => $item->id,
                    'type' => 'seller_post_payout_refund_recovery',
                    'bucket' => 'available',
                    'amount' => -$net,
                    'description' => 'Recuperare dupa refund pentru item deja platit sellerului.',
                ]);
            }

            $this->recordEntry([
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'type' => 'platform_commission_reversal',
                'bucket' => 'platform_revenue',
                'amount' => -$commission,
                'description' => 'Reversare comision platforma pentru item anulat sau rambursat.',
                'meta' => ['seller_id' => $item->seller_id],
            ]);

            $item->forceFill([
                'seller_status' => $targetStatus === 'cancelled' ? 'cancelled' : $item->seller_status,
                'seller_status_updated_at' => now(),
                'financial_status' => $targetStatus,
                'financial_status_updated_at' => now(),
                'refunded_at' => now(),
                'refunded_by' => $admin->id,
                'refund_reason' => $reason,
            ])->save();
        });
    }

    public function createPayoutRequest(User $seller, float $amount, ?string $note = null): PayoutRequest
    {
        return DB::transaction(function () use ($seller, $amount, $note) {
            $balance = $this->ensureBalance($seller->id);
            $requestedTotal = (float) PayoutRequest::query()
                ->where('seller_id', $seller->id)
                ->whereIn('status', ['requested', 'approved', 'batched'])
                ->sum('amount');

            $requestable = round((float) $balance->available_amount - $requestedTotal, 2);
            if ($amount <= 0 || $amount > $requestable) {
                throw new \RuntimeException('Suma ceruta depaseste soldul disponibil pentru retragere.');
            }

            $profile = $seller->sellerProfile;

            return PayoutRequest::create([
                'seller_id' => $seller->id,
                'amount' => round($amount, 2),
                'beneficiary_name' => $profile?->payout_beneficiary_name ?: ($profile?->legal_name ?: $seller->name),
                'iban' => $profile?->payout_iban,
                'bank_name' => $profile?->payout_bank_name,
                'seller_note' => $note,
            ]);
        });
    }

    public function createBatchFromRequested(User $admin, array $requestIds, ?string $notes = null): PayoutBatch
    {
        return DB::transaction(function () use ($admin, $requestIds, $notes) {
            $requests = PayoutRequest::query()
                ->whereIn('id', $requestIds)
                ->whereIn('status', ['requested', 'approved'])
                ->lockForUpdate()
                ->get();

            if ($requests->isEmpty()) {
                throw new \RuntimeException('Nu exista payout requests eligibile pentru batch.');
            }

            $batch = PayoutBatch::create([
                'status' => 'batched',
                'created_by' => $admin->id,
                'notes' => $notes,
                'total_amount' => round((float) $requests->sum('amount'), 2),
                'items_count' => $requests->count(),
            ]);

            foreach ($requests as $request) {
                $request->forceFill([
                    'status' => 'batched',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now(),
                ])->save();

                $batch->items()->create([
                    'payout_request_id' => $request->id,
                    'seller_id' => $request->seller_id,
                    'amount' => $request->amount,
                    'beneficiary_name' => $request->beneficiary_name,
                    'iban' => $request->iban,
                    'bank_name' => $request->bank_name,
                    'status' => 'pending',
                ]);
            }

            return $batch->load('items.seller.sellerProfile', 'items.payoutRequest');
        });
    }

    public function markBatchPaid(PayoutBatch $batch, User $admin): void
    {
        DB::transaction(function () use ($batch, $admin) {
            $batch = PayoutBatch::query()->lockForUpdate()->findOrFail($batch->id);
            $batch->load('items.payoutRequest');

            foreach ($batch->items as $item) {
                if ($item->status === 'paid') {
                    continue;
                }

                $amount = (float) $item->amount;
                $this->adjustBalance($item->seller_id, 0, -$amount, $amount);

                $this->recordEntry([
                    'seller_id' => $item->seller_id,
                    'payout_request_id' => $item->payout_request_id,
                    'payout_batch_id' => $batch->id,
                    'type' => 'seller_available_debit',
                    'bucket' => 'available',
                    'amount' => -$amount,
                    'description' => 'Suma scoasa din available la payout.',
                ]);

                $this->recordEntry([
                    'seller_id' => $item->seller_id,
                    'payout_request_id' => $item->payout_request_id,
                    'payout_batch_id' => $batch->id,
                    'type' => 'seller_paid_credit',
                    'bucket' => 'paid',
                    'amount' => $amount,
                    'description' => 'Suma marcata ca platita sellerului.',
                ]);

                $item->forceFill([
                    'status' => 'paid',
                    'paid_at' => now(),
                ])->save();

                if ($item->payoutRequest) {
                    $item->payoutRequest->forceFill([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'reviewed_by' => $admin->id,
                        'reviewed_at' => now(),
                    ])->save();
                }
            }

            $batch->forceFill([
                'status' => 'paid',
                'paid_by' => $admin->id,
                'paid_at' => now(),
            ])->save();
        });
    }

    public function requestableAmount(User $seller): float
    {
        $balance = $this->ensureBalance($seller->id);
        $requestedTotal = (float) PayoutRequest::query()
            ->where('seller_id', $seller->id)
            ->whereIn('status', ['requested', 'approved', 'batched'])
            ->sum('amount');

        return round((float) $balance->available_amount - $requestedTotal, 2);
    }

    public function ensureBalance(int $sellerId): SellerBalance
    {
        return SellerBalance::firstOrCreate(
            ['seller_id' => $sellerId],
            ['pending_amount' => 0, 'available_amount' => 0, 'paid_amount' => 0]
        );
    }

    private function adjustBalance(int $sellerId, float $pendingDelta, float $availableDelta, float $paidDelta): void
    {
        $balance = SellerBalance::query()->lockForUpdate()->firstOrCreate(
            ['seller_id' => $sellerId],
            ['pending_amount' => 0, 'available_amount' => 0, 'paid_amount' => 0]
        );

        $balance->pending_amount = round((float) $balance->pending_amount + $pendingDelta, 2);
        $balance->available_amount = round((float) $balance->available_amount + $availableDelta, 2);
        $balance->paid_amount = round((float) $balance->paid_amount + $paidDelta, 2);
        $balance->save();
    }

    private function recordEntry(array $data): FinancialLedgerEntry
    {
        return FinancialLedgerEntry::create([
            'currency' => 'MDL',
            'happened_at' => now(),
            'meta' => [],
            ...$data,
        ]);
    }
}
