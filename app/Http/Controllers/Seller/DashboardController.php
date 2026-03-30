<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $commissionPercent = (float) ($user->sellerProfile->commission_percent ?? 10);

        $productsCount = Product::where('seller_id', $user->id)->count();

        $paidOrdersCount = Order::where('payment_status', 'paid')
            ->whereHas('items', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->count();

        $grossRevenue = OrderItem::where('seller_id', $user->id)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->selectRaw('SUM(price * qty) as total')
            ->value('total') ?? 0;

        $marketplaceCommission = $grossRevenue * ($commissionPercent / 100);
        $netRevenue = $grossRevenue - $marketplaceCommission;

        return view('seller.dashboard', compact(
            'productsCount',
            'paidOrdersCount',
            'grossRevenue',
            'commissionPercent',
            'marketplaceCommission',
            'netRevenue'
        ));
    }
}