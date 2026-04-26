<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\SellerCommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(SellerCommissionService $commissionService): View
    {
        $seller = auth()->user();
        abort_unless(($seller->role ?? null) === 'seller' && ($seller->seller_status ?? null) === 'approved', 403);

        $commissionService->syncAllActiveSellers();

        $currentPeriod = $commissionService->currentPeriodForSeller($seller);
        $history = $commissionService->historyForSeller($seller);
        $orders = $commissionService->ordersForPeriod($currentPeriod);

        $daysRemaining = $currentPeriod->deadline_at
            ? now()->startOfDay()->diffInDays($currentPeriod->deadline_at, false)
            : null;

        $platformBankDetails = config('services.marketplace_commissions');

        return view('seller.finance.index', compact(
            'currentPeriod',
            'history',
            'orders',
            'daysRemaining',
            'platformBankDetails'
        ));
    }

    public function submitCurrentPeriod(Request $request, SellerCommissionService $commissionService): RedirectResponse
    {
        $seller = auth()->user();
        abort_unless(($seller->role ?? null) === 'seller' && ($seller->seller_status ?? null) === 'approved', 403);

        $data = $request->validate([
            'seller_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $commissionService->submitCurrentPeriod($seller, $data['seller_note'] ?? null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Perioada a fost marcata ca achitata de tine si asteapta confirmarea adminului.');
    }
}
