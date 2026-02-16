<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Maib\MaibService;
use Illuminate\Http\Request;

use App\Mail\PaymentConfirmedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // MAIB va redirecționa: okUrl?payId=...&orderId=...
    public function ok(Request $request, MaibService $maib)
    {
        $payId   = (string) $request->query('payId', '');
        $orderId = (string) $request->query('orderId', '');

        $order = null;

        if ($payId) {
            $order = Order::where('pay_id', $payId)->first();
        }

        if (!$order && $orderId) {
            $baseOrderId = explode('-', $orderId)[0] ?? $orderId;

            $order = Order::where('order_number', $baseOrderId)->first()
                ?? Order::where('id', $baseOrderId)->first();
        }

        if (!$order) {
            return redirect()->route('home')->with('error', 'Comanda nu a fost găsită după orderId/payId.');
        }

        try {
            if (!$order->pay_id && $payId) {
                $order->update(['pay_id' => $payId]);
            }

            // info din MAIB
            $info = $order->pay_id ? $maib->paymentInfo($order->pay_id) : null;

            if ($info) {
                $order->update(['payment_details' => $info]);
            }

            $status     = data_get($info, 'result.status');
            $statusCode = data_get($info, 'result.statusCode');

            if ($status === 'OK' && $statusCode === '000') {
            
                $wasAlreadyPaid = ($order->payment_status === 'paid') || !is_null($order->paid_at);
            
                $order->update([
                    'payment_status'  => 'paid',
                    'paid_at'         => $order->paid_at ?? now(),
                    'payment_details' => $info,
                ]);
            
                // ✅ Trimite email aici (pentru că aici ai confirmarea reală)
                // Trimitem doar dacă NU era deja paid (ca să nu dubleze la refresh)
                if (!$wasAlreadyPaid) {
                    try {
                        $order->loadMissing('items');
            
                        $to = $order->user?->email; // necesită relația user() în Order
                        Log::info('PaymentConfirmedMail (OK) debug', [
                            'order_id' => $order->id,
                            'to' => $to,
                            'mail_mailer' => config('mail.default'),
                        ]);
            
                        if ($to) {
                            Mail::to($to)->send(new PaymentConfirmedMail($order));
                            Log::info('PaymentConfirmedMail (OK) sent', ['order_id' => $order->id, 'to' => $to]);
                        } else {
                            Log::warning('PaymentConfirmedMail (OK) NOT sent: missing email', ['order_id' => $order->id]);
                        }
                    } catch (\Throwable $e) {
                        Log::error('PaymentConfirmedMail (OK) failed', ['order_id' => $order->id, 'err' => $e->getMessage()]);
                    }
                }
            
                return redirect()->route('pay.maib.receipt', ['payId' => $order->pay_id]);
            }

            $order->update(['payment_status' => 'failed']);
            return redirect()->route('orders.index')->with('error', 'Plata a eșuat sau a fost anulată.');
        } catch (\Throwable $e) {
            Log::error('MAIB ok error', ['err' => $e->getMessage()]);
            return redirect()->route('orders.index')->with('success', 'Comanda e creată. Plata se confirmă automat.');
        }
    }

    public function fail(Request $request)
    {
        $payId   = (string) $request->query('payId', '');
        $orderId = (string) $request->query('orderId', '');

        $order = $payId ? Order::where('pay_id', $payId)->first() : null;

        if (!$order && $orderId) {
            $baseOrderId = explode('-', $orderId)[0] ?? $orderId;
            $order = Order::where('order_number', $baseOrderId)->first()
                ?? Order::where('id', $baseOrderId)->first();
        }

        if ($order) {
            $order->update(['payment_status' => 'failed']);
        }

        return redirect()->route('orders.index')->with('error', 'Plata a eșuat sau a fost anulată.');
    }

    public function callback(Request $request, MaibService $maib)
    {
        $payload = $request->all();

        Log::info('MAIB CALLBACK', [
            'ip' => $request->ip(),
            'body' => $payload,
        ]);

        $payId   = (string) data_get($payload, 'result.payId', '');
        $orderId = (string) data_get($payload, 'result.orderId', '');

        if (!$payId && !$orderId) {
            Log::warning('MAIB CALLBACK: missing identifiers', $payload);
            return response()->json(['ok' => true]);
        }

        $baseOrderId = explode('-', $orderId)[0] ?? $orderId;

        $order = Order::where('pay_id', $payId)->first()
            ?? Order::where('order_number', $baseOrderId)->first()
            ?? Order::where('id', $baseOrderId)->first();

        if (!$order) {
            Log::warning('MAIB CALLBACK: order not found', compact('payId', 'orderId'));
            return response()->json(['ok' => true]);
        }

        if (!$order->pay_id && $payId) {
            $order->update(['pay_id' => $payId]);
        }

        $isSuccess = (
            data_get($payload, 'result.status') === 'OK' &&
            data_get($payload, 'result.statusCode') === '000'
        );

        // anti-duplicate
        $wasAlreadyPaid = ($order->payment_status === 'paid') || !is_null($order->paid_at);

        if ($isSuccess) {

            $order->update([
                'payment_status'  => 'paid',
                'paid_at'         => $order->paid_at ?? now(),
                'payment_details' => $payload,
            ]);

            // trimitem email DOAR prima dată
            if (!$wasAlreadyPaid) {
                try {
                    $order->loadMissing('items');

                    // ✅ destinatar sigur:
                    // 1) user->email (cel mai corect)
                    // 2) dacă ai coloană customer_email / email în orders (opțional)
                    $to = $order->user?->email
                        ?? ($order->customer_email ?? null)
                        ?? ($order->email ?? null);

                    Log::info('PaymentConfirmedMail debug', [
                        'order_id' => $order->id,
                        'to' => $to,
                        'user_id' => $order->user_id ?? null,
                        'mail_mailer' => config('mail.default'),
                        'mail_from' => config('mail.from.address'),
                    ]);

                    if ($to) {
                        Mail::to($to)->send(new PaymentConfirmedMail($order));
                        Log::info('PaymentConfirmedMail sent OK', ['order_id' => $order->id, 'to' => $to]);
                    } else {
                        Log::warning('PaymentConfirmedMail NOT sent: recipient missing', ['order_id' => $order->id]);
                    }
                } catch (\Throwable $e) {
                    Log::error('PaymentConfirmedMail failed', [
                        'order_id' => $order->id,
                        'err' => $e->getMessage(),
                    ]);
                }
            }

        } else {

            $order->update([
                'payment_status'  => 'failed',
                'payment_details' => $payload,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function receipt(Request $request)
    {
        $payId = (string) $request->query('payId', '');

        if (!$payId) {
            return redirect()->route('home')->with('error', 'Lipsește payId.');
        }

        $order = Order::where('pay_id', $payId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Comanda nu a fost găsită.');
        }

        return view('payment.receipt', ['order' => $order]);
    }
}
