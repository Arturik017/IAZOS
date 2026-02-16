<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Maib\MaibService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function create()
    {
        if (!auth()->check()) {
            session()->put('url.intended', url()->current());
            return redirect()->route('register')->with('error', 'Pentru a finaliza comanda, creează cont.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Coșul este gol.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0));
        }

        $path = storage_path('app/md/locations.json');
        $districts = [];
        $localitiesMap = [];

        if (file_exists($path)) {
            $locations = json_decode(file_get_contents($path), true);

            $districts = $locations['districts'] ?? [];
            $districts = array_values(array_filter($districts, fn($x) => is_string($x) && trim($x) !== ''));

            $localitiesMap = $locations['localities'] ?? [];
            $localitiesMap = is_array($localitiesMap) ? $localitiesMap : [];
            $localitiesMap = array_intersect_key($localitiesMap, array_flip($districts));

            foreach ($localitiesMap as $d => $list) {
                $list = is_array($list) ? $list : [];
                $localitiesMap[$d] = array_values(array_filter($list, fn($x) => is_string($x) && trim($x) !== ''));
            }
        }

        return view('shop.checkout', compact('cart', 'subtotal', 'districts', 'localitiesMap'));
    }

    public function store(Request $request, MaibService $maib)

    {
        \Log::debug('CHECKOUT STORE LOG TEST');


        \Log::info('CHECKOUT STORE HIT', [
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);
        if (!auth()->check()) {
            session()->put('url.intended', url()->current());
            return redirect()->route('register')->with('error', 'Pentru a finaliza comanda, creează cont.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Coșul este gol.');
        }

        $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'phone'         => ['required', 'string', 'max:50'],
            'district'      => ['required', 'string', 'max:120'],
            'locality'      => ['required', 'string', 'max:120'],
            'street'        => ['required', 'string', 'max:255'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
            'accept_terms'  => ['accepted'],
        ]);

        // subtotal recalculat
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0));
        }

        // verificare stoc înainte
        foreach ($cart as $item) {
            $productId = (int)($item['id'] ?? 0);
            if (!$productId) continue;

            $product = Product::find($productId);
            if ($product) {
                $qtyWanted = (int)($item['qty'] ?? 0);
                if ($qtyWanted > (int)$product->stock) {
                    return back()->withInput()->with('error', "Stoc insuficient pentru: {$product->name}");
                }
            }
        }

        $provider = env('PAYMENT_PROVIDER', 'maib');

        // -------------- TRANZACȚIE DB (nu lăsăm mizerie în DB dacă MAIB pică) --------------
        try {
            return DB::transaction(function () use ($request, $maib, $cart, $subtotal, $provider) {

                $fullName = trim($request->first_name . ' ' . $request->last_name);
                $address = $request->district . ', ' . $request->locality . ', ' . $request->street
                    . ($request->postal_code ? (', ' . $request->postal_code) : '');

                $order = Order::create([
                    'user_id'          => auth()->id(),
                    'first_name'       => $request->first_name,
                    'last_name'        => $request->last_name,
                    'phone'            => $request->phone,
                    'district'         => $request->district,
                    'locality'         => $request->locality,
                    'street'           => $request->street,
                    'postal_code'      => $request->postal_code,
                    'customer_note'    => $request->customer_note,
                    'subtotal'         => $subtotal,
                    'status'           => 'new',
                    'payment_status'   => 'pending',
                    'customer_name'    => $fullName,
                    'customer_phone'   => $request->phone,
                    'customer_address' => $address,
                ]);

                $order->order_number = 100000 + $order->id;
                $order->save();

                foreach ($cart as $item) {
                    $productId = (int)($item['id'] ?? 0);
                    $product = $productId ? Product::find($productId) : null;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product?->id,
                        'product_name' => (string)($item['name'] ?? ''),
                        'price'        => (float)($item['price'] ?? 0),
                        'qty'          => (int)($item['qty'] ?? 0),
                    ]);
                }

                $order->load('items');

                // MOCK mode (ca să continui dezvoltarea dacă sandbox e blocat)
                if ($provider === 'mock') {
                    $order->update(['payment_status' => 'paid']);
                    session()->forget('cart');
                    return redirect()->route('orders.index')->with('success', 'Plată simulată (MOCK) ✅');
                }

                $clientIp = $request->header('X-Forwarded-For')
                    ? trim(explode(',', (string)$request->header('X-Forwarded-For'))[0])
                    : $request->ip();

                $payload = [
                    'clientIp'    => $clientIp,
                    'amount'      => number_format((float)$order->subtotal, 2, '.', ''),
                    'currency'    => config('services.maib.currency', 'MDL'),
                    'language'    => config('services.maib.lang', 'ro'),
                    'description' => 'Order #' . ($order->order_number ?? $order->id),
                    'orderId'     => (string)(($order->order_number ?? $order->id) . '-' . time()),

                    'clientName'  => $order->customer_name,
                    'email'       => auth()->user()->email ?? 'test@example.com',
                    'phone'       => $order->phone,

                    'items' => $order->items->map(fn($it) => [
                        'id'       => (string)($it->product_id ?? $it->id),
                        'name'     => $it->product_name,
                        'price'    => number_format((float)$it->price, 2, '.', ''),
                        'quantity' => (int)$it->qty,
                    ])->values()->all(),

                    'callbackUrl' => route('pay.maib.callback'),
                    'okUrl'       => route('pay.maib.ok'),
                    'failUrl'     => route('pay.maib.fail'),
                ];

                Log::info('MAIB createPayment payload', ['payload' => $payload]);

                try {
                    $res = $maib->createPayment($payload);
                    Log::info('MAIB createPayment response', ['res' => $res]);
                } catch (RequestException $e) {
                    $status  = $e->response?->status();
                    $body    = $e->response?->json() ?? $e->response?->body();
                    $headers = $e->response?->headers();

                    Log::error('MAIB createPayment RequestException', compact('status', 'body', 'headers') + ['payload' => $payload]);

                    if ($provider === 'maib_with_fallback') {
                        // fallback: nu blocăm dezvoltarea
                        $order->update(['payment_status' => 'paid']);
                        session()->forget('cart');
                        return redirect()->route('orders.index')
                            ->with('success', 'Sandbox MAIB a refuzat tranzacția (13101). Am marcat plata ca MOCK ca să continui dezvoltarea ✅');
                    }

                    // anulăm tot (rollback) ca să nu rămână comenzi create aiurea
                    throw $e;
                }

                $payId  = data_get($res, 'result.payId') ?? data_get($res, 'payId');
                $payUrl = data_get($res, 'result.payUrl') ?? data_get($res, 'payUrl');

                if (!$payId || !$payUrl) {
                    Log::warning('MAIB missing payUrl/payId', ['res' => $res, 'payload' => $payload]);
                    throw new \RuntimeException('MAIB nu a returnat payUrl/payId.');
                }

                $order->update([
                    'pay_id' => $payId,
                    'payment_status' => 'pending',
                ]);

                // golim coșul abia când avem payUrl
                session()->forget('cart');

                // Email la creare (opțional)

                return redirect()->away($payUrl);
            });
        } catch (\Throwable $e) {
            // Dacă ajunge aici, tranzacția DB s-a dat rollback (nu rămâne order în DB)
            Log::error('Checkout store error', ['err' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Nu pot porni plata MAIB acum (sandbox). Vezi laravel.log / contactează MAIB.');
        }
    }
}
