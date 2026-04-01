<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Maib\MaibService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function store(Request $request, MaibService $maib)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:120'],
            'locality' => ['required', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        $provider = env('PAYMENT_PROVIDER', 'maib');
        $subtotal = 0;
        $normalizedItems = [];

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ((int) $item['qty'] > (int) $product->stock) {
                return response()->json([
                    'ok' => false,
                    'message' => "Stoc insuficient pentru: {$product->name}",
                ], 422);
            }

            $price = (float) ($product->final_price ?? $product->price);
            $qty = (int) $item['qty'];
            $lineTotal = $price * $qty;

            $subtotal += $lineTotal;

            $normalizedItems[] = [
                'product' => $product,
                'price' => $price,
                'qty' => $qty,
            ];
        }

        try {
            $result = DB::transaction(function () use ($data, $user, $maib, $provider, $subtotal, $normalizedItems, $request) {
                $fullName = trim($data['first_name'] . ' ' . $data['last_name']);
                $address = $data['district'] . ', ' . $data['locality'] . ', ' . $data['street']
                    . (!empty($data['postal_code']) ? ', ' . $data['postal_code'] : '');

                $order = Order::create([
                    'user_id' => $user->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'district' => $data['district'],
                    'locality' => $data['locality'],
                    'street' => $data['street'],
                    'postal_code' => $data['postal_code'] ?? null,
                    'customer_note' => $data['customer_note'] ?? null,
                    'subtotal' => $subtotal,
                    'status' => 'new',
                    'payment_status' => 'pending',
                    'customer_name' => $fullName,
                    'customer_phone' => $data['phone'],
                    'customer_address' => $address,
                ]);

                $order->order_number = 100000 + $order->id;
                $order->save();

                foreach ($normalizedItems as $row) {
                    $product = $row['product'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'seller_status' => 'pending',
                        'seller_status_updated_at' => now(),
                        'product_name' => $product->name,
                        'price' => $row['price'],
                        'qty' => $row['qty'],
                    ]);
                }

                $order->load(['items.product', 'items.seller']);

                if ($provider === 'mock') {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    return response()->json([
                        'ok' => true,
                        'message' => 'Checkout creat cu succes (MOCK).',
                        'payment_url' => null,
                        'pay_id' => null,
                        'order' => $this->orderPayload($order->fresh(['items.product', 'items.seller'])),
                    ], 201);
                }

                $clientIp = $request->header('X-Forwarded-For')
                    ? trim(explode(',', (string) $request->header('X-Forwarded-For'))[0])
                    : $request->ip();

                $payload = [
                    'clientIp' => $clientIp,
                    'amount' => number_format((float) $order->subtotal, 2, '.', ''),
                    'currency' => config('services.maib.currency', 'MDL'),
                    'language' => config('services.maib.lang', 'ro'),
                    'description' => 'Order #' . ($order->order_number ?? $order->id),
                    'orderId' => (string) (($order->order_number ?? $order->id) . '-' . time()),
                    'clientName' => $order->customer_name,
                    'email' => $user->email ?? 'test@example.com',
                    'phone' => $order->phone,
                    'items' => $order->items->map(function ($it) {
                        return [
                            'id' => (string) ($it->product_id ?? $it->id),
                            'name' => $it->product_name,
                            'price' => number_format((float) $it->price, 2, '.', ''),
                            'quantity' => (int) $it->qty,
                        ];
                    })->values()->all(),

                    'callbackUrl' => route('pay.maib.callback'),
                    'okUrl' => route('pay.maib.ok'),
                    'failUrl' => route('pay.maib.fail'),
                ];

                Log::info('API MAIB createPayment payload', [
                    'order_id' => $order->id,
                    'payload' => $payload,
                ]);

                try {
                    $res = $maib->createPayment($payload);
                } catch (RequestException $e) {
                    $status = $e->response?->status();
                    $body = $e->response?->json() ?? $e->response?->body();

                    Log::error('API MAIB createPayment RequestException', [
                        'order_id' => $order->id,
                        'status' => $status,
                        'body' => $body,
                    ]);

                    throw $e;
                }

                $payId = data_get($res, 'result.payId') ?? data_get($res, 'payId');
                $payUrl = data_get($res, 'result.payUrl') ?? data_get($res, 'payUrl');

                if (!$payId || !$payUrl) {
                    Log::warning('API MAIB missing payUrl/payId', [
                        'order_id' => $order->id,
                        'response' => $res,
                    ]);

                    throw new \RuntimeException('MAIB nu a returnat payUrl/payId.');
                }

                $order->update([
                    'pay_id' => $payId,
                    'payment_status' => 'pending',
                ]);

                $order->refresh()->load(['items.product', 'items.seller']);

                return response()->json([
                    'ok' => true,
                    'message' => 'Checkout creat. Redirecționează utilizatorul către plată.',
                    'payment_url' => $payUrl,
                    'pay_id' => $payId,
                    'order' => $this->orderPayload($order),
                ], 201);
            });

            return $result;
        } catch (\Throwable $e) {
            Log::error('API checkout error', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Nu pot porni plata MAIB acum.',
            ], 500);
        }
    }

    private function orderPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'subtotal' => (float) $order->subtotal,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'pay_id' => $order->pay_id,
            'paid_at' => optional($order->paid_at)?->toISOString(),
            'created_at' => optional($order->created_at)?->toISOString(),
            'customer' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'phone' => $order->phone,
                'district' => $order->district,
                'locality' => $order->locality,
                'street' => $order->street,
                'postal_code' => $order->postal_code,
                'customer_note' => $order->customer_note,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'qty' => (int) $item->qty,
                    'price' => (float) $item->price,
                    'line_total' => (float) $item->price * (int) $item->qty,
                    'seller_status' => $item->seller_status,
                    'seller' => $item->seller ? [
                        'id' => $item->seller->id,
                        'name' => $item->seller->name,
                    ] : null,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'slug' => $item->product->slug,
                        'image' => $item->product->image,
                    ] : null,
                ];
            })->values()->all(),
        ];
    }
}