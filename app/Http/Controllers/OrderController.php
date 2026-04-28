<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $orders = Order::query()
            ->where('user_id', $userId)
            ->with([
                'items',
                'items.seller',
                'items.seller.sellerProfile',
            ])
            ->orderByDesc('id')
            ->get();

        $orders->each(function ($order) {
            $order->items_count = $order->items->count();
            $order->sellers_count = $order->items
                ->pluck('seller_id')
                ->filter()
                ->unique()
                ->count();
        });

        $grouped = $orders->groupBy(fn ($o) => $o->status ?? 'unknown');

        $statusOrder = [
            'pending_payment',
            'paid',
            'new',
            'confirmed',
            'processing',
            'partial_shipped',
            'shipped',
            'completed',
            'delivered',
            'cancelled',
            'canceled',
            'unknown',
        ];

        return view('shop.orders', compact('grouped', 'statusOrder'));
    }

    public function show(Order $order)
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);

        $order->load([
            'items',
            'items.product',
            'items.seller',
            'items.seller.sellerProfile',
            'items.refundRequest.reviewer',
        ]);

        $sellerGroups = $order->items
            ->groupBy('seller_id')
            ->map(function ($items, $sellerId) {
                $seller = optional($items->first())->seller;

                $subtotal = $items->sum(function ($item) {
                    return (float) $item->price * (int) $item->qty;
                });

                return (object) [
                    'seller_id' => $sellerId,
                    'seller' => $seller,
                    'seller_name' => $seller?->sellerProfile?->shop_name
                        ?? $seller?->name
                        ?? 'Seller necunoscut',
                    'items' => $items->values(),
                    'items_count' => $items->count(),
                    'subtotal' => $subtotal,
                    'summary_status' => $this->resolveSellerSummaryStatus($items),
                ];
            })
            ->sortBy('seller_name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('shop.order-show', [
            'order' => $order,
            'sellerGroups' => $sellerGroups,
        ]);
    }

    private function resolveSellerSummaryStatus($items): string
    {
        $statuses = collect($items)
            ->pluck('seller_status')
            ->filter()
            ->values();

        if ($statuses->isEmpty()) {
            return 'pending';
        }

        if ($statuses->every(fn ($status) => $status === 'cancelled')) {
            return 'cancelled';
        }

        if ($statuses->every(fn ($status) => $status === 'delivered')) {
            return 'delivered';
        }

        if ($statuses->contains('shipped') || $statuses->contains('delivered')) {
            return 'partial_shipped';
        }

        if ($statuses->contains('processing') || $statuses->contains('accepted')) {
            return 'processing';
        }

        return 'pending';
    }
}
