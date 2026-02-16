<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Maib\MaibService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    public function store(Request $request, Order $order, MaibService $maib)
    {
        // 1) Validare sumă
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        // 2) Verificări obligatorii
        if (!$order->pay_id) {
            return back()->with('error', 'Nu există pay_id la comandă. Nu pot face refund.');
        }

        if (($order->payment_status ?? '') !== 'paid') {
            return back()->with('error', 'Refund se poate face doar dacă payment_status = paid.');
        }

        $amount = (float)$request->amount;

        // Protecție: să nu depășești totalul comenzii
        if ($amount > (float)$order->subtotal) {
            return back()->with('error', 'Suma refund nu poate depăși totalul comenzii.');
        }

        try {
            // 3) Apel MAIB
            $res = $maib->refund(
                payId: (string)$order->pay_id,
                amount: $amount,
                reason: (string)($request->reason ?? 'Refund')
            );

            // 4) Log - ca să vezi exact ce a returnat MAIB
            Log::info('MAIB REFUND RESPONSE', [
                'order_id' => $order->id,
                'pay_id'   => $order->pay_id,
                'amount'   => $amount,
                'res'      => $res,
            ]);

            // 5) Update status în DB (GARANTAT)
            // folosim save() ca să nu depindem de fillable, dar tot e bine să fie fillable.
            $order->payment_status = 'refunded';
            $order->save();

            return back()->with('success', 'Refund trimis. payment_status setat pe refunded.');

        } catch (\Throwable $e) {
            Log::error('MAIB REFUND ERROR', [
                'order_id' => $order->id,
                'pay_id'   => $order->pay_id,
                'err'      => $e->getMessage(),
            ]);

            return back()->with('error', 'Refund a eșuat: ' . $e->getMessage());
        }
    }
}
