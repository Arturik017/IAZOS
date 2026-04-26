<?php

namespace App\Http\Controllers;

use App\Mail\PaymentConfirmedMail;
use App\Models\Order;
use App\Services\MarketplaceFinanceService;
use App\Services\Maib\MaibService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function ok(Request $request, MaibService $maib, MarketplaceFinanceService $finance)
    {
        $payId = (string) $request->query('payId', '');
        $orderId = (string) $request->query('orderId', '');

        $order = $this->resolveOrder($payId, $orderId);
        if (!$order) {
            return redirect()->route('home')->with('error', 'Comanda nu a fost gasita dupa orderId/payId.');
        }

        try {
            if (!$order->pay_id && $payId) {
                $order->update(['pay_id' => $payId]);
            }

            $info = $order->pay_id ? $maib->paymentInfo($order->pay_id) : null;
            if ($info) {
                $order->update(['payment_details' => $info]);
            }

            $status = data_get($info, 'result.status');
            $statusCode = data_get($info, 'result.statusCode');

            if ($status === 'OK' && $statusCode === '000') {
                $this->markSuccessfulPayment($order, $info, $finance);

                if ($order->payment_flow === 'seller_direct' && $order->checkout_uuid) {
                    return redirect()->route('checkout.payments.show', $order->checkout_uuid)
                        ->with('success', 'Plata pentru comanda #' . ($order->order_number ?? $order->id) . ' a fost confirmata.');
                }

                return redirect()->route('pay.maib.receipt', ['payId' => $order->pay_id]);
            }

            $order->update(['payment_status' => 'failed']);
            return $this->failedRedirect($order);
        } catch (\Throwable $e) {
            Log::error('MAIB ok error', ['err' => $e->getMessage()]);

            if ($order->payment_flow === 'seller_direct' && $order->checkout_uuid) {
                return redirect()->route('checkout.payments.show', $order->checkout_uuid)
                    ->with('success', 'Comanda a fost creata. Confirmarea platii revine automat dupa raspunsul procesatorului.');
            }

            return redirect()->route('orders.index')->with('success', 'Comanda e creata. Plata se confirma automat.');
        }
    }

    public function fail(Request $request)
    {
        $payId = (string) $request->query('payId', '');
        $orderId = (string) $request->query('orderId', '');

        $order = $this->resolveOrder($payId, $orderId);
        if ($order) {
            $order->update(['payment_status' => 'failed']);
        }

        return $this->failedRedirect($order);
    }

    public function callback(Request $request, MaibService $maib, MarketplaceFinanceService $finance)
    {
        $payload = $request->all();

        Log::info('MAIB CALLBACK', [
            'ip' => $request->ip(),
            'body' => $payload,
        ]);

        $payId = (string) data_get($payload, 'result.payId', '');
        $orderId = (string) data_get($payload, 'result.orderId', '');

        if (!$payId && !$orderId) {
            Log::warning('MAIB CALLBACK: missing identifiers', $payload);
            return response()->json(['ok' => true]);
        }

        $order = $this->resolveOrder($payId, $orderId);
        if (!$order) {
            Log::warning('MAIB CALLBACK: order not found', compact('payId', 'orderId'));
            return response()->json(['ok' => true]);
        }

        if (!$order->pay_id && $payId) {
            $order->update(['pay_id' => $payId]);
        }

        $isSuccess = data_get($payload, 'result.status') === 'OK'
            && data_get($payload, 'result.statusCode') === '000';

        if ($isSuccess) {
            $this->markSuccessfulPayment($order, $payload, $finance);
        } else {
            $order->update([
                'payment_status' => 'failed',
                'payment_details' => $payload,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function receipt(Request $request)
    {
        $payId = (string) $request->query('payId', '');

        if (!$payId) {
            return redirect()->route('home')->with('error', 'Lipseste payId.');
        }

        $order = Order::where('pay_id', $payId)->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Comanda nu a fost gasita.');
        }

        return view('payment.receipt', ['order' => $order]);
    }

    private function resolveOrder(string $payId, string $orderId): ?Order
    {
        $order = $payId ? Order::where('pay_id', $payId)->first() : null;

        if (!$order && $orderId) {
            $baseOrderId = explode('-', $orderId)[0] ?? $orderId;

            $order = Order::where('order_number', $baseOrderId)->first()
                ?? Order::where('id', $baseOrderId)->first();
        }

        return $order;
    }

    private function markSuccessfulPayment(Order $order, array $payload, MarketplaceFinanceService $finance): void
    {
        $wasAlreadyPaid = ($order->payment_status === 'paid') || !is_null($order->paid_at);

        $order->update([
            'payment_status' => 'paid',
            'status' => $order->status === 'pending_payment' ? 'paid' : $order->status,
            'paid_at' => $order->paid_at ?? now(),
            'payment_details' => $payload,
        ]);

        if (!$wasAlreadyPaid && $order->payment_flow !== 'seller_direct') {
            $finance->allocateOrderPayment($order->fresh('items.seller.sellerProfile'));
        }

        if (!$wasAlreadyPaid) {
            try {
                $order->loadMissing('items');
                $to = $order->user?->email ?? ($order->customer_email ?? null) ?? ($order->email ?? null);

                if ($to) {
                    Mail::to($to)->send(new PaymentConfirmedMail($order));
                }
            } catch (\Throwable $e) {
                Log::error('PaymentConfirmedMail failed', [
                    'order_id' => $order->id,
                    'err' => $e->getMessage(),
                ]);
            }
        }
    }

    private function failedRedirect(?Order $order)
    {
        if ($order && $order->payment_flow === 'seller_direct' && $order->checkout_uuid) {
            return redirect()->route('checkout.payments.show', $order->checkout_uuid)
                ->with('error', 'Plata a esuat sau a fost anulata.');
        }

        return redirect()->route('orders.index')->with('error', 'Plata a esuat sau a fost anulata.');
    }
}
