<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Ia DOAR comenzile userului logat
        $orders = Order::where('user_id', $userId)
            ->with('items')           // presupune relația items în Order
            ->orderByDesc('id')
            ->get();

        // Grupare pe status (new / confirmed / etc)
        $grouped = $orders->groupBy(fn ($o) => $o->status ?? 'unknown');

        // Ordinea secțiunilor
        $statusOrder = ['new', 'confirmed', 'processing', 'shipped', 'delivered', 'canceled', 'unknown'];

        return view('shop.orders', compact('grouped', 'statusOrder'));
    }
}
