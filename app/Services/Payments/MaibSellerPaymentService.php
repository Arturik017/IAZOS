<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\SellerPaymentAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MaibSellerPaymentService
{
    public function createPaymentUrl(Order $order, SellerPaymentAccount $account): array
    {
        $baseUrl = rtrim(config('services.maib.base_url', 'https://api.maibmerchants.md'), '/');
        $amount = (float) ($order->subtotal ?? 0);

        $tokenResponse = Http::asJson()
            ->timeout(30)
            ->post($baseUrl . '/v1/generate-token', [
                'projectId' => $account->merchant_id,
                'projectSecret' => $account->secret_key,
            ]);

        $tokenResponse->throw();

        $accessToken = data_get($tokenResponse->json(), 'result.accessToken');
        if (!$accessToken) {
            throw new \RuntimeException('Procesatorul MAIB nu a returnat access token pentru seller.');
        }

        $payload = [
            'clientIp' => request()->ip(),
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => config('services.maib.currency', 'MDL'),
            'language' => config('services.maib.lang', 'ro'),
            'description' => 'Comanda #' . ($order->order_number ?? $order->id),
            'orderId' => (string) (($order->order_number ?? $order->id) . '-' . Str::lower(Str::random(8))),
            'clientName' => $order->customer_name,
            'email' => $order->user?->email ?? $account->payment_contact_email ?? 'client@example.com',
            'phone' => $order->phone ?? $order->customer_phone,
            'items' => $order->items->map(fn ($item) => [
                'id' => (string) ($item->variant_id ?? $item->product_id ?? $item->id),
                'name' => trim($item->product_name . ($item->variant_label ? ' / ' . $item->variant_label : '')),
                'price' => number_format((float) $item->price, 2, '.', ''),
                'quantity' => (int) $item->qty,
            ])->values()->all(),
            'callbackUrl' => route('pay.maib.callback'),
            'okUrl' => route('pay.maib.ok'),
            'failUrl' => route('pay.maib.fail'),
        ];

        $paymentResponse = Http::asJson()
            ->timeout(30)
            ->withToken($accessToken)
            ->post($baseUrl . '/v1/pay', $payload);

        $paymentResponse->throw();

        $json = $paymentResponse->json();

        return [
            'payment_url' => data_get($json, 'result.payUrl') ?? data_get($json, 'payUrl'),
            'payment_reference' => data_get($json, 'result.payId') ?? data_get($json, 'payId'),
            'raw_response' => $json,
        ];
    }
}
