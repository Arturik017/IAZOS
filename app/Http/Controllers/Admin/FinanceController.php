<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerCommissionPeriod;
use App\Models\SellerPaymentAccount;
use App\Services\SellerCommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(SellerCommissionService $commissionService): View
    {
        $commissionService->syncAllActiveSellers();

        $periods = SellerCommissionPeriod::query()
            ->with('seller.sellerProfile')
            ->latest('period_end')
            ->latest('id')
            ->get();

        $paymentAccounts = SellerPaymentAccount::query()
            ->with('sellerProfile.user')
            ->latest('updated_at')
            ->get();

        $summary = [
            'awaiting_admin_review' => $periods->where('status', 'awaiting_admin_review')->count(),
            'overdue' => $periods->where('status', 'overdue')->count(),
            'unpaid_total' => (float) $periods->whereIn('status', ['unpaid', 'overdue', 'awaiting_admin_review'])->sum('commission_amount'),
            'payment_accounts_pending' => $paymentAccounts->whereIn('status', ['missing', 'pending', 'rejected'])->count(),
        ];

        return view('admin.finance.index', compact('periods', 'paymentAccounts', 'summary'));
    }

    public function updatePeriodStatus(SellerCommissionPeriod $period, Request $request, SellerCommissionService $commissionService): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:unpaid,awaiting_admin_review,paid,overdue'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $commissionService->markAdminStatus($period, $data['status'], $data['admin_note'] ?? null);

        return back()->with('success', 'Statusul perioadei de comision a fost actualizat.');
    }
}
