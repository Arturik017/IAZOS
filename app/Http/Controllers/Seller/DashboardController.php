<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerCommissionPeriod;
use App\Services\SellerCommissionService;
use App\Support\MessageState;

class DashboardController extends Controller
{
    public function index(SellerCommissionService $commissionService)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $commissionService->syncAllActiveSellers();
        $currentPeriod = $commissionService->currentPeriodForSeller($user);

        $productsCount = Product::query()
            ->where('seller_id', $user->id)
            ->where('status', 1)
            ->count();

        $paidOrdersCount = Order::query()
            ->where('seller_id', $user->id)
            ->where('payment_flow', 'seller_direct')
            ->where('payment_status', 'paid')
            ->count();

        $grossRevenue = (float) $currentPeriod->gross_sales_amount;
        $commissionPercent = (float) $currentPeriod->commission_percent;
        $commissionDue = (float) $currentPeriod->commission_amount;
        $nextDeadline = $currentPeriod->deadline_at;
        $daysRemaining = $nextDeadline ? now()->startOfDay()->diffInDays($nextDeadline, false) : null;

        $paymentAccount = $user->sellerProfile?->paymentAccount;
        $paymentAccountStatus = $paymentAccount?->status ?? 'missing';

        $messageUnreadCount = 0;
        if (MessageState::supported()) {
            $messageUnreadCount = \App\Models\Conversation::query()
                ->where(function ($query) use ($user) {
                    $query->where('seller_id', $user->id)
                        ->orWhere('client_id', $user->id);
                })
                ->withCount([
                    'messages as unread_messages_count' => function ($query) {
                        $query->whereNull('read_at')
                            ->where('sender_id', '!=', auth()->id());
                    },
                ])
                ->get()
                ->sum('unread_messages_count');
        }

        return view('seller.dashboard', compact(
            'productsCount',
            'paidOrdersCount',
            'grossRevenue',
            'commissionPercent',
            'commissionDue',
            'nextDeadline',
            'daysRemaining',
            'currentPeriod',
            'paymentAccountStatus',
            'messageUnreadCount'
        ));
    }
}
