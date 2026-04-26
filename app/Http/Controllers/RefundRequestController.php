<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function store(Request $request, Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);
        abort_unless((int) $item->order_id === (int) $order->id, 404);

        if ($item->refundRequest) {
            return back()->with('error', 'Exista deja o solicitare de refund pentru acest produs.');
        }

        if (in_array($item->financial_status, ['cancelled', 'refunded'], true)) {
            return back()->with('error', 'Acest produs este deja inchis financiar.');
        }

        $data = $request->validate([
            'target_status' => ['required', 'in:cancelled,refunded'],
            'client_reason' => ['required', 'string', 'max:255'],
            'client_note' => ['nullable', 'string', 'max:3000'],
        ]);

        RefundRequest::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'user_id' => auth()->id(),
            'seller_id' => $item->seller_id,
            'target_status' => $data['target_status'],
            'status' => 'requested',
            'client_reason' => $data['client_reason'],
            'client_note' => $data['client_note'] ?? null,
        ]);

        return back()->with('success', 'Solicitarea de refund a fost trimisa. Sellerul si adminul o pot analiza acum.');
    }
}
