<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0));
        }

        return view('shop.cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        return $this->storeCartItem($request, $product, false);
    }

    public function buyNow(Request $request, Product $product)
    {
        return $this->storeCartItem($request, $product, true);
    }

    public function update(Request $request, string $rowId)
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$rowId])) {
            return redirect()->route('cart.index')->with('error', 'Produsul nu mai există în coș.');
        }

        $item = $cart[$rowId];
        $product = Product::find($item['product_id'] ?? $item['id'] ?? null);

        if (!$product || (int) $product->status !== 1) {
            unset($cart[$rowId]);
            session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('error', 'Produsul nu mai este disponibil.');
        }

        $variant = null;
        if (!empty($item['variant_id'])) {
            $variant = ProductVariant::query()
                ->with(['attributes.attribute', 'attributes.option'])
                ->where('product_id', $product->id)
                ->where('id', $item['variant_id'])
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                unset($cart[$rowId]);
                session()->put('cart', $cart);

                return redirect()->route('cart.index')->with('error', 'Varianta selectată nu mai este disponibilă.');
            }
        }

        $stock = $variant ? (int) $variant->stock : (int) $product->stock;
        $price = $variant && !is_null($variant->price)
            ? (float) $variant->price
            : (float) $product->final_price;

        if ($stock <= 0) {
            unset($cart[$rowId]);
            session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('error', 'Produsul sau varianta selectată nu mai este în stoc.');
        }

        $qty = min((int) $request->input('qty'), $stock);

        $cart[$rowId]['qty'] = $qty;
        $cart[$rowId]['stock'] = $stock;
        $cart[$rowId]['price'] = $price;
        $cart[$rowId]['image'] = $variant && $variant->image
            ? $variant->image
            : ($product->primary_image ?: $product->image);
        $cart[$rowId]['variant_label'] = $variant ? $this->makeVariantLabel($variant) : null;
        $cart[$rowId]['product_id'] = $product->id;
        $cart[$rowId]['variant_id'] = $variant?->id;

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Coș actualizat.');
    }

    public function remove(string $rowId)
    {
        $cart = session()->get('cart', []);

        unset($cart[$rowId]);

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produs șters din coș.');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Coș golit.');
    }

    private function storeCartItem(Request $request, Product $product, bool $redirectToCart = false)
    {
        if ((int) $product->status !== 1) {
            return redirect()->route('home')->with('error', 'Produsul nu este disponibil.');
        }

        $variant = null;
        $hasVariants = $product->variants()->where('is_active', true)->exists();

        if ($hasVariants) {
            $request->validate([
                'variant_id' => ['required', 'integer'],
            ]);

            $variant = ProductVariant::query()
                ->with(['attributes.attribute', 'attributes.option'])
                ->where('product_id', $product->id)
                ->where('id', (int) $request->input('variant_id'))
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                return back()->with('error', 'Alege o variantă validă.');
            }
        }

        $stock = $variant ? (int) $variant->stock : (int) $product->stock;

        if ($stock <= 0) {
            return back()->with('error', 'Produsul sau varianta selectată nu este în stoc.');
        }

        $price = $variant && !is_null($variant->price)
            ? (float) $variant->price
            : (float) $product->final_price;

        $rowId = $this->makeRowId($product->id, $variant?->id);
        $cart = session()->get('cart', []);

        if (isset($cart[$rowId])) {
            $newQty = (int) $cart[$rowId]['qty'] + 1;
            $cart[$rowId]['qty'] = min($newQty, $stock);
        } else {
            $cart[$rowId] = [
                'row_id' => $rowId,
                'id' => $product->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_label' => $variant ? $this->makeVariantLabel($variant) : null,
                'price' => $price,
                'qty' => 1,
                'stock' => $stock,
                'image' => $variant && $variant->image
                    ? $variant->image
                    : ($product->primary_image ?: $product->image),
            ];
        }

        $cart[$rowId]['stock'] = $stock;
        $cart[$rowId]['price'] = $price;
        $cart[$rowId]['product_id'] = $product->id;
        $cart[$rowId]['variant_id'] = $variant?->id;
        $cart[$rowId]['variant_label'] = $variant ? $this->makeVariantLabel($variant) : null;
        $cart[$rowId]['image'] = $variant && $variant->image
            ? $variant->image
            : ($product->primary_image ?: $product->image);

        session()->put('cart', $cart);

        if ($redirectToCart) {
            return redirect()->route('cart.index')->with('success', 'Produs adăugat. Poți finaliza comanda.');
        }

        return back()->with('success', 'Produs adăugat în coș.');
    }

    private function makeRowId(int $productId, ?int $variantId = null): string
    {
        return $productId . '-' . ($variantId ?: 'base');
    }

    private function makeVariantLabel(ProductVariant $variant): string
    {
        $variant->loadMissing(['attributes.attribute', 'attributes.option']);

        return $variant->attributes
            ->map(function ($attribute) {
                $name = $attribute->attribute->name ?? 'Atribut';
                $value = $attribute->option->label ?? $attribute->option->value ?? 'Valoare';
                return $name . ': ' . $value;
            })
            ->implode(' / ');
    }
}