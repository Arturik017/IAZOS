<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->orderByDesc('id')
            ->get();

        $grouped = $orders->groupBy(fn($o) => $o->status ?? 'unknown');

        $statusOrder = ['new', 'confirmed', 'processing', 'shipped', 'delivered', 'canceled', 'unknown'];

        return view('admin.orders.index', compact('grouped', 'statusOrder'));
    }

    public function show(Order $order)
    {
        $order->load([
            'items.seller.sellerProfile',
            'items.product',
            'items.variant',
            'items.adminReleasedBy',
            'items.refundedBy',
            'items.refundRequest.user',
            'items.refundRequest.seller',
            'items.refundRequest.reviewer',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:new,confirmed,processing,shipped,delivered,canceled'],
        ]);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Statusul a fost actualizat.');
    }
    
    public function maibRefresh(\Illuminate\Http\Request $request, \App\Models\Order $order, \App\Services\Maib\MaibService $maib)
    {
        if (!$order->pay_id) {
            return back()->with('error', 'Nu există pay_id la comandă.');
        }
    
        $info = $maib->paymentInfoSafe($order->pay_id);
    
        if ($info) {
            $order->payment_details = $info;
        }
    
        $refundAmount = (float) data_get($info, 'result.refundAmount', 0);
    
        if ($refundAmount > 0) {
            $order->refund_status = 'refunded';
            $order->refunded_at = now();
            $order->payment_status = 'refunded';
        } else {
            // dacă ai cerut refund, dar încă nu e confirmat în pay-info
            if (($order->payment_status ?? '') === 'refunded') {
                // îl lăsăm așa cum e
            } else {
                $order->refund_status = 'refund_pending';
            }
        }
    
        $order->save();
    
        return back()->with('success', 'Status MAIB actualizat.');
    }

}
