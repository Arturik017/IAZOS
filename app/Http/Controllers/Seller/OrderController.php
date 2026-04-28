<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
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

        $orders = Order::with(['items'])
            ->whereHas('items', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->orderByDesc('id')
            ->get();

        $grossRevenue = OrderItem::where('seller_id', $user->id)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->selectRaw('SUM(price * qty) as total')
            ->value('total') ?? 0;

        $marketplaceCommission = $grossRevenue * ($commissionPercent / 100);
        $netRevenue = $grossRevenue - $marketplaceCommission;

        $paidOrdersCount = Order::where('payment_status', 'paid')
            ->whereHas('items', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->count();

        $pendingRefundRequestsCount = \App\Models\RefundRequest::query()
            ->where('seller_id', $user->id)
            ->where('status', 'requested')
            ->count();

        return view('seller.orders.index', compact(
            'orders',
            'grossRevenue',
            'paidOrdersCount',
            'commissionPercent',
            'marketplaceCommission',
            'netRevenue',
            'pendingRefundRequestsCount'
        ));
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $commissionPercent = (float) ($user->sellerProfile->commission_percent ?? 10);

        $order = Order::with(['items.refundRequest.user', 'items.refundRequest.reviewer'])
            ->where('id', $id)
            ->whereHas('items', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->firstOrFail();

        $myItems = $order->items->where('seller_id', $user->id);
        $openRefundRequests = $myItems
            ->filter(fn ($item) => $item->refundRequest && $item->refundRequest->status === 'requested')
            ->values();

        $myGrossTotal = $myItems->sum(function ($item) {
            return $item->price * $item->qty;
        });

        $myCommission = $myGrossTotal * ($commissionPercent / 100);
        $myNetTotal = $myGrossTotal - $myCommission;

        $allowedStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered_pending_review', 'cancelled'];

        return view('seller.orders.show', compact(
            'order',
            'myItems',
            'commissionPercent',
            'myGrossTotal',
            'myCommission',
            'myNetTotal',
            'allowedStatuses',
            'openRefundRequests'
        ));
    }

    public function updateItemStatus(Request $request, $orderId, $itemId)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $data = $request->validate([
            'seller_status' => ['required', 'in:pending,confirmed,processing,shipped,delivered_pending_review,cancelled'],
        ]);

        $order = Order::with('items')->findOrFail($orderId);

        $item = $order->items()
            ->where('id', $itemId)
            ->where('seller_id', $user->id)
            ->firstOrFail();

        $item->seller_status = $data['seller_status'];
        $item->seller_status_updated_at = now();
        $item->save();

        $this->refreshOrderStatus($order->fresh('items'));

        return redirect()
            ->route('seller.orders.show', $order->id)
            ->with('success', 'Statusul produsului a fost actualizat.');
    }

    private function refreshOrderStatus(Order $order): void
    {
        $statuses = $order->items->pluck('seller_status')->filter()->values();

        if ($statuses->isEmpty()) {
            $order->status = 'new';
            $order->save();
            return;
        }

        if ($statuses->every(fn ($status) => $status === 'cancelled')) {
            $order->status = 'cancelled';
            $order->save();
            return;
        }

        if ($statuses->every(fn ($status) => in_array($status, ['delivered_pending_review', 'delivered'], true))) {
            $order->status = 'delivered_pending_review';
            $order->save();
            return;
        }

        if ($statuses->contains('shipped') || $statuses->contains('delivered_pending_review') || $statuses->contains('delivered')) {
            $order->status = 'partial_shipped';
            $order->save();
            return;
        }

        if ($statuses->contains('confirmed') || $statuses->contains('processing')) {
            $order->status = 'processing';
            $order->save();
            return;
        }

        $order->status = 'new';
        $order->save();
    }
}
