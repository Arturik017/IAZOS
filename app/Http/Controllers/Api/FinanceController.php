<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedgerEntry;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Services\MarketplaceFinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function sellerSummary(Request $request, MarketplaceFinanceService $finance): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);

        $balance = $finance->ensureBalance($seller->id);

        return response()->json([
            'ok' => true,
            'summary' => [
                'pending_amount' => (float) $balance->pending_amount,
                'available_amount' => (float) $balance->available_amount,
                'paid_amount' => (float) $balance->paid_amount,
                'requestable_amount' => $finance->requestableAmount($seller),
            ],
        ]);
    }

    public function sellerTransactions(Request $request): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);

        $transactions = FinancialLedgerEntry::query()
            ->where('seller_id', $seller->id)
            ->latest('happened_at')
            ->latest('id')
            ->limit(100)
            ->get()
            ->map(fn ($entry) => [
                'id' => $entry->id,
                'type' => $entry->type,
                'bucket' => $entry->bucket,
                'amount' => (float) $entry->amount,
                'currency' => $entry->currency,
                'description' => $entry->description,
                'order_id' => $entry->order_id,
                'order_item_id' => $entry->order_item_id,
                'happened_at' => optional($entry->happened_at)->toISOString(),
            ])->values();

        return response()->json(['ok' => true, 'transactions' => $transactions]);
    }

    public function sellerPayoutRequests(Request $request, MarketplaceFinanceService $finance): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);

        $requests = PayoutRequest::query()
            ->where('seller_id', $seller->id)
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'amount' => (float) $item->amount,
                'status' => $item->status,
                'beneficiary_name' => $item->beneficiary_name,
                'iban' => $item->iban,
                'bank_name' => $item->bank_name,
                'seller_note' => $item->seller_note,
                'admin_note' => $item->admin_note,
                'reviewed_at' => optional($item->reviewed_at)->toISOString(),
                'paid_at' => optional($item->paid_at)->toISOString(),
                'created_at' => optional($item->created_at)->toISOString(),
            ]);

        return response()->json([
            'ok' => true,
            'requestable_amount' => $finance->requestableAmount($seller),
            'payout_requests' => $requests->values(),
        ]);
    }

    public function storeSellerPayoutRequest(Request $request, MarketplaceFinanceService $finance): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'seller_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$seller->sellerProfile?->payout_iban || !$seller->sellerProfile?->payout_beneficiary_name) {
            return response()->json(['ok' => false, 'message' => 'Datele de payout lipsesc din profilul seller.'], 422);
        }

        try {
            $payoutRequest = $finance->createPayoutRequest($seller, (float) $data['amount'], $data['seller_note'] ?? null);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Cererea de payout a fost creata.',
            'payout_request' => [
                'id' => $payoutRequest->id,
                'amount' => (float) $payoutRequest->amount,
                'status' => $payoutRequest->status,
            ],
        ], 201);
    }

    public function sellerOrderItems(Request $request): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);

        $items = OrderItem::query()
            ->with('order')
            ->where('seller_id', $seller->id)
            ->latest('id')
            ->limit(200)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'product_name' => $item->product_name,
                'variant_label' => $item->variant_label,
                'qty' => (int) $item->qty,
                'price' => (float) $item->price,
                'gross_amount' => (float) $item->gross_amount,
                'platform_commission_amount' => (float) $item->platform_commission_amount,
                'seller_net_amount' => (float) $item->seller_net_amount,
                'seller_status' => $item->seller_status,
                'financial_status' => $item->financial_status,
                'admin_release_status' => $item->admin_release_status,
                'paid_order' => $item->order?->payment_status === 'paid',
            ]);

        return response()->json(['ok' => true, 'items' => $items->values()]);
    }

    public function updateSellerOrderItemStatus(Request $request, OrderItem $item, MarketplaceFinanceService $finance): JsonResponse
    {
        $seller = $request->user();
        abort_unless(($seller->role ?? null) === 'seller', 403);
        abort_unless((int) $item->seller_id === (int) $seller->id, 403);

        $data = $request->validate([
            'seller_status' => ['required', 'in:pending,confirmed,processing,shipped,delivered_pending_review,cancelled'],
        ]);

        $item->forceFill([
            'seller_status' => $data['seller_status'],
            'seller_status_updated_at' => now(),
        ])->save();

        if ($data['seller_status'] === 'delivered_pending_review') {
            $finance->markDeliveredBySeller($item);
        }

        return response()->json(['ok' => true, 'message' => 'Statusul itemului a fost actualizat.']);
    }

    public function adminPendingReviews(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless(($user->role ?? null) === 'admin', 403);

        $items = OrderItem::query()
            ->with(['order', 'seller.sellerProfile'])
            ->where('admin_release_status', 'pending_review')
            ->latest('delivered_reported_at')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'product_name' => $item->product_name,
                'seller' => [
                    'id' => $item->seller_id,
                    'name' => $item->seller?->sellerProfile?->shop_name ?? $item->seller?->name,
                ],
                'seller_net_amount' => (float) $item->seller_net_amount,
                'delivered_reported_at' => optional($item->delivered_reported_at)->toISOString(),
                'financial_status' => $item->financial_status,
            ]);

        return response()->json(['ok' => true, 'items' => $items->values()]);
    }

    public function adminApproveRelease(Request $request, OrderItem $item, MarketplaceFinanceService $finance): JsonResponse
    {
        $user = $request->user();
        abort_unless(($user->role ?? null) === 'admin', 403);

        $finance->approveRelease($item, $user, $request->input('admin_release_note'));

        return response()->json(['ok' => true, 'message' => 'Suma sellerului a fost eliberata.']);
    }
}
