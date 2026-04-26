<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Services\MarketplaceFinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function approve(Request $request, RefundRequest $refundRequest, MarketplaceFinanceService $finance): RedirectResponse
    {
        $data = $request->validate([
            'admin_decision_note' => ['nullable', 'string', 'max:3000'],
            'target_status' => ['required', 'in:cancelled,refunded'],
        ]);

        $finance->cancelOrRefundItem(
            $refundRequest->orderItem,
            auth()->user(),
            $data['admin_decision_note'] ?: $refundRequest->client_reason,
            $data['target_status']
        );

        $refundRequest->forceFill([
            'status' => 'approved',
            'admin_decision_note' => $data['admin_decision_note'] ?? null,
            'resolved_financial_status' => $data['target_status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        return back()->with('success', 'Solicitarea a fost aprobata si aplicata financiar pe item.');
    }

    public function reject(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $data = $request->validate([
            'admin_decision_note' => ['required', 'string', 'max:3000'],
        ]);

        $refundRequest->forceFill([
            'status' => 'rejected',
            'admin_decision_note' => $data['admin_decision_note'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        return back()->with('success', 'Solicitarea de refund a fost respinsa de admin.');
    }
}
