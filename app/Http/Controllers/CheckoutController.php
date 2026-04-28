<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Payments\PaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create()
    {
        if (!auth()->check()) {
            session()->put('url.intended', url()->current());
            return redirect()->route('register')->with('error', 'Pentru a finaliza comanda, creeaza cont.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Cosul este gol.');
        }

        $subtotal = collect($cart)->sum(fn ($item) => ((float) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0)));

        $productIds = collect($cart)->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $sellerCount = Product::query()
            ->whereIn('id', $productIds)
            ->whereNotNull('seller_id')
            ->distinct('seller_id')
            ->count('seller_id');

        $path = storage_path('app/md/locations.json');
        $districts = [];
        $localitiesMap = [];

        if (file_exists($path)) {
            $locations = json_decode(file_get_contents($path), true);

            $districts = $locations['districts'] ?? [];
            $districts = array_values(array_filter($districts, fn ($x) => is_string($x) && trim($x) !== ''));

            $localitiesMap = $locations['localities'] ?? [];
            $localitiesMap = is_array($localitiesMap) ? $localitiesMap : [];
            $localitiesMap = array_intersect_key($localitiesMap, array_flip($districts));

            foreach ($localitiesMap as $district => $list) {
                $list = is_array($list) ? $list : [];
                $localitiesMap[$district] = array_values(array_filter($list, fn ($x) => is_string($x) && trim($x) !== ''));
            }
        }

        return view('shop.checkout', compact('cart', 'subtotal', 'districts', 'localitiesMap', 'sellerCount'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            session()->put('url.intended', url()->current());
            return redirect()->route('register')->with('error', 'Pentru a finaliza comanda, creeaza cont.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Cosul este gol.');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:120'],
            'locality' => ['required', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
            'accept_terms' => ['accepted'],
        ], [
            'accept_terms.accepted' => 'Trebuie sa confirmi ca intelegi ca produsele pot fi achitate separat in functie de vanzator.',
        ]);

        $groupedCart = [];
        $productIds = collect($cart)->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique()->all();
        $products = Product::query()
            ->with('seller.sellerProfile.paymentAccount')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        foreach ($cart as $index => $item) {
            $productId = (int) ($item['id'] ?? 0);
            $qtyWanted = (int) ($item['qty'] ?? 0);
            $variantId = !empty($item['variant_id']) ? (int) $item['variant_id'] : null;

            if (!$productId || $qtyWanted < 1) {
                continue;
            }

            $product = $products->get($productId);
            if (!$product || (int) $product->status !== 1 || !$product->seller_id) {
                return back()->withInput()->with('error', 'Un produs din cos nu mai este disponibil pentru checkout.');
            }

            if ($variantId) {
                $variant = ProductVariant::query()
                    ->where('product_id', $product->id)
                    ->where('id', $variantId)
                    ->where('is_active', true)
                    ->first();

                if (!$variant || $qtyWanted > (int) $variant->stock) {
                    return back()->withInput()->with('error', "Varianta selectata pentru {$product->name} nu mai are stoc suficient.");
                }
            } elseif ($qtyWanted > (int) $product->stock) {
                return back()->withInput()->with('error', "Stoc insuficient pentru: {$product->name}");
            }

            $groupedCart[$product->seller_id][] = array_merge($item, [
                '_product_index' => $index,
                '_product' => $product,
            ]);
        }

        if (empty($groupedCart)) {
            return back()->withInput()->with('error', 'Nu am putut grupa produsele din cos pe vanzatori.');
        }

        $checkoutUuid = (string) Str::uuid();

        try {
            DB::transaction(function () use ($request, $groupedCart, $checkoutUuid) {
                $fullName = trim($request->first_name . ' ' . $request->last_name);
                $address = $request->district . ', ' . $request->locality . ', ' . $request->street
                    . ($request->postal_code ? (', ' . $request->postal_code) : '');

                foreach ($groupedCart as $sellerId => $sellerItems) {
                    $seller = optional($sellerItems[0]['_product'])->seller;
                    $sellerProfile = $seller?->sellerProfile;
                    $commissionPercent = (float) ($sellerProfile?->commission_percent ?? 10);
                    $sellerSubtotal = 0;

                    foreach ($sellerItems as $sellerItem) {
                        $sellerSubtotal += ((float) ($sellerItem['price'] ?? 0) * (int) ($sellerItem['qty'] ?? 0));
                    }

                    $order = Order::create([
                        'user_id' => auth()->id(),
                        'seller_id' => $sellerId,
                        'checkout_uuid' => $checkoutUuid,
                        'checkout_group_id' => $checkoutUuid,
                        'payment_flow' => 'seller_direct',
                        'payment_provider' => 'maib',
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'phone' => $request->phone,
                        'district' => $request->district,
                        'locality' => $request->locality,
                        'street' => $request->street,
                        'postal_code' => $request->postal_code,
                        'customer_note' => $request->customer_note,
                        'subtotal' => $sellerSubtotal,
                        'commission_percent' => $commissionPercent,
                        'commission_amount' => round($sellerSubtotal * ($commissionPercent / 100), 2),
                        'status' => 'pending_payment',
                        'payment_status' => 'unpaid',
                        'customer_name' => $fullName,
                        'customer_phone' => $request->phone,
                        'customer_address' => $address,
                    ]);

                    $order->order_number = 100000 + $order->id;
                    $order->save();

                    foreach ($sellerItems as $sellerItem) {
                        $productId = (int) ($sellerItem['id'] ?? 0);
                        $variantId = !empty($sellerItem['variant_id']) ? (int) $sellerItem['variant_id'] : null;
                        $qty = (int) ($sellerItem['qty'] ?? 0);

                        $product = Product::lockForUpdate()->find($productId);
                        if (!$product) {
                            throw new \RuntimeException('Produsul nu mai exista in timpul checkout-ului.');
                        }

                        $variant = null;
                        $price = (float) ($sellerItem['price'] ?? 0);

                        if ($variantId) {
                            $variant = ProductVariant::query()
                                ->where('product_id', $product->id)
                                ->where('id', $variantId)
                                ->where('is_active', true)
                                ->lockForUpdate()
                                ->first();

                            if (!$variant || $qty > (int) $variant->stock) {
                                throw new \RuntimeException("Varianta selectata pentru {$product->name} nu mai are stoc suficient.");
                            }

                            $variant->decrement('stock', $qty);
                            $price = !is_null($variant->price)
                                ? (float) $variant->price
                                : (float) $product->final_price;
                        } else {
                            if ($qty > (int) $product->stock) {
                                throw new \RuntimeException("Stoc insuficient pentru: {$product->name}");
                            }

                            $product->decrement('stock', $qty);
                        }

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'variant_id' => $variant?->id,
                            'seller_id' => $product->seller_id,
                            'seller_status' => 'pending',
                            'seller_status_updated_at' => now(),
                            'product_name' => (string) ($sellerItem['name'] ?? $product->name),
                            'variant_label' => $sellerItem['variant_label'] ?? null,
                            'price' => $price,
                            'qty' => $qty,
                        ]);

                        $remainingVariantStock = ProductVariant::query()
                            ->where('product_id', $product->id)
                            ->where('is_active', true)
                            ->sum('stock');

                        $product->stock = $remainingVariantStock > 0
                            ? (int) $remainingVariantStock
                            : max(0, (int) $product->stock);
                        $product->save();
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Checkout split seller error', ['err' => $e->getMessage()]);
            return back()->withInput()->with('error', $e->getMessage() ?: 'Nu am putut crea comenzile separate pentru vanzatori.');
        }

        session()->forget('cart');

        return redirect()
            ->route('checkout.payments.show', $checkoutUuid)
            ->with('success', 'Datele de livrare au fost salvate. Continua cu platile separate pentru fiecare vanzator.');
    }

    public function showPayments(string $checkoutUuid)
    {
        abort_unless(auth()->check(), 403);

        $orders = Order::query()
            ->with(['seller.sellerProfile.paymentAccount', 'items'])
            ->where('user_id', auth()->id())
            ->where('checkout_uuid', $checkoutUuid)
            ->orderBy('id')
            ->get();

        abort_if($orders->isEmpty(), 404);

        $sellerCount = $orders->count();
        $allPaid = $orders->every(fn (Order $order) => $order->payment_status === 'paid');

        return view('shop.checkout-payments', compact('orders', 'checkoutUuid', 'sellerCount', 'allPaid'));
    }

    public function payOrder(Order $order, PaymentLinkService $paymentLinkService)
    {
        abort_unless(auth()->check(), 403);
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->payment_flow !== 'seller_direct') {
            return redirect()->route('orders.show', $order)->with('error', 'Aceasta comanda foloseste fluxul vechi de plata.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.payments.show', $order->checkout_uuid)->with('success', 'Aceasta comanda este deja achitata.');
        }

        try {
            $result = $paymentLinkService->createPaymentUrl($order->fresh(['seller.sellerProfile.paymentAccount', 'items', 'user']));
        } catch (\Throwable $e) {
            return redirect()
                ->route('checkout.payments.show', $order->checkout_uuid)
                ->with('error', $e->getMessage());
        }

        $order->update([
            'payment_provider' => 'maib',
            'payment_url' => $result['payment_url'] ?? null,
            'payment_reference' => $result['payment_reference'] ?? null,
            'payment_link_generated_at' => now(),
            'pay_id' => $result['payment_reference'] ?? $order->pay_id,
            'payment_status' => 'pending',
            'payment_details' => $result['raw_response'] ?? null,
        ]);

        return redirect()->away($result['payment_url']);
    }

    public function simulatePayment(Order $order, string $result)
    {
        abort_unless(app()->environment('local'), 404);
        abort_unless(auth()->check(), 403);
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        if ($order->payment_flow !== 'seller_direct') {
            return redirect()->route('orders.show', $order)->with('error', 'Aceasta comanda nu foloseste flow-ul seller-direct.');
        }

        if (!in_array($result, ['success', 'fail'], true)) {
            abort(404);
        }

        if ($result === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'status' => $order->status === 'pending_payment' ? 'paid' : $order->status,
                'paid_at' => $order->paid_at ?? now(),
                'payment_reference' => $order->payment_reference ?: ('local-test-' . $order->id . '-' . now()->timestamp),
                'pay_id' => $order->pay_id ?: ('local-test-' . $order->id),
                'payment_details' => array_merge($order->payment_details ?? [], [
                    'local_test' => true,
                    'result' => 'success',
                    'simulated_at' => now()->toIso8601String(),
                ]),
            ]);

            return redirect()
                ->route('checkout.payments.show', $order->checkout_uuid)
                ->with('success', 'Plata a fost simulata cu succes pentru comanda #' . ($order->order_number ?? $order->id) . '.');
        }

        $order->update([
            'payment_status' => 'failed',
            'payment_details' => array_merge($order->payment_details ?? [], [
                'local_test' => true,
                'result' => 'failed',
                'simulated_at' => now()->toIso8601String(),
            ]),
        ]);

        return redirect()
            ->route('checkout.payments.show', $order->checkout_uuid)
            ->with('error', 'Plata a fost simulata ca esuata pentru comanda #' . ($order->order_number ?? $order->id) . '.');
    }
}
