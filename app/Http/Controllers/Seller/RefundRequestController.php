<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function respond(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $user = auth()->user();

        if (!$user || (int) $refundRequest->seller_id !== (int) $user->id) {
            abort(403, 'Acces interzis.');
        }

        $data = $request->validate([
            'seller_response' => ['required', 'string', 'max:3000'],
            'decision' => ['required', 'in:approve,reject'],
            'resolved_financial_status' => ['nullable', 'in:cancelled,refunded'],
        ]);

        $item = $refundRequest->orderItem;
        if (!$item || (int) $item->seller_id !== (int) $user->id) {
            abort(404);
        }

        if ($data['decision'] === 'approve') {
            $targetStatus = $data['resolved_financial_status'] ?: $refundRequest->target_status;

            $item->forceFill([
                'seller_status' => $targetStatus === 'cancelled' ? 'cancelled' : $item->seller_status,
                'seller_status_updated_at' => now(),
                'financial_status' => $targetStatus,
                'financial_status_updated_at' => now(),
                'refunded_at' => now(),
                'refunded_by' => $user->id,
                'refund_reason' => $data['seller_response'],
            ])->save();

            $refundRequest->forceFill([
                'seller_response' => $data['seller_response'],
                'seller_recommended_status' => $targetStatus,
                'seller_responded_at' => now(),
                'status' => 'approved',
                'resolved_financial_status' => $targetStatus,
                'reviewed_at' => now(),
            ])->save();

            $this->refreshOrderAfterSellerDecision($refundRequest->order);

            return back()->with('success', 'Ai aprobat cererea clientului. Statusul comenzii a fost actualizat.');
        }

        $refundRequest->forceFill([
            'seller_response' => $data['seller_response'],
            'seller_recommended_status' => null,
            'seller_responded_at' => now(),
            'status' => 'rejected',
            'resolved_financial_status' => null,
            'reviewed_at' => now(),
        ])->save();

        return back()->with('success', 'Ai respins cererea clientului. Adminul poate doar monitoriza acest istoric.');
    }

    private function refreshOrderAfterSellerDecision(?Order $order): void
    {
        if (!$order) {
            return;
        }

        $order->load('items.refundRequest');

        $paymentStatuses = $order->items
            ->pluck('financial_status')
            ->filter()
            ->values();

        if ($paymentStatuses->isNotEmpty() && $paymentStatuses->every(fn ($status) => in_array($status, ['cancelled', 'refunded'], true))) {
            $order->payment_status = 'refunded';
            $order->status = 'cancelled';
            $order->save();
        }
    }
}
