<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = auth()->user()
            ->wishlistItems()
            ->with([
                'product.seller.sellerProfile',
                'product.category',
                'product.subcategory',
                'variant.attributes.attribute',
                'variant.attributes.option',
            ])
            ->latest()
            ->get()
            ->filter(function ($item) {
                $product = $item->product;

                if (!$product) {
                    return false;
                }

                if ((int) $product->status !== 1) {
                    return false;
                }

                if (!is_null($product->seller_id) && !(bool) $product->is_approved) {
                    return false;
                }

                return true;
            })
            ->values();

        return view('shop.wishlist', [
            'items' => $items,
        ]);
    }

    public function store(Product $product): RedirectResponse
    {
        if ((int) $product->status !== 1) {
            return back()->with('error', 'Produsul nu este disponibil pentru wishlist.');
        }

        $variantId = request()->integer('variant_id') ?: null;
        $variant = null;

        if ($product->variants()->where('is_active', true)->exists()) {
            if (!$variantId) {
                return back()->with('error', 'Alege mai intai varianta pe care vrei sa o salvezi la favorite.');
            }

            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->where('id', $variantId)
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                return back()->with('error', 'Varianta selectata pentru favorite nu este valida.');
            }
        }

        auth()->user()->wishlistItems()->firstOrCreate([
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
        ]);

        return back()->with('success', 'Produsul a fost adaugat la wishlist.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $variantId = request()->integer('variant_id') ?: null;

        $query = auth()->user()->wishlistItems()
            ->where('product_id', $product->id);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }

        $query->delete();

        return back()->with('success', 'Produsul a fost scos din wishlist.');
    }

    public function moveToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $wishlistItems = auth()->user()
            ->wishlistItems()
            ->whereIn('id', $data['item_ids'])
            ->with(['variant.attributes.attribute', 'variant.attributes.option'])
            ->get()
            ->keyBy('id');

        $products = Product::query()
            ->with(['variants' => fn ($query) => $query->where('is_active', true)])
            ->whereIn('id', $wishlistItems->pluck('product_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $cart = session()->get('cart', []);
        $added = 0;
        $needsVariant = 0;
        $outOfStock = 0;
        $movedWishlistIds = [];

        foreach ($data['item_ids'] as $itemId) {
            $wishlistItem = $wishlistItems->get((int) $itemId);
            $product = $wishlistItem ? $products->get((int) $wishlistItem->product_id) : null;

            if (!$product || !$wishlistItem || (int) $product->status !== 1) {
                continue;
            }

            if (!is_null($product->seller_id) && !(bool) $product->is_approved) {
                continue;
            }

            $variant = $wishlistItem->variant;

            if ($product->variants->isNotEmpty() && !$variant) {
                $needsVariant++;
                continue;
            }

            if ($variant && ((int) $variant->product_id !== (int) $product->id || !(bool) $variant->is_active)) {
                $needsVariant++;
                continue;
            }

            $stock = $variant ? (int) $variant->stock : (int) $product->stock;
            if ($stock <= 0) {
                $outOfStock++;
                continue;
            }

            $rowId = $product->id . '-' . ($variant?->id ?: 'base');

            if (isset($cart[$rowId])) {
                $cart[$rowId]['qty'] = min(((int) $cart[$rowId]['qty']) + 1, $stock);
            } else {
                $cart[$rowId] = [
                    'row_id' => $rowId,
                    'id' => $product->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'name' => $product->name,
                    'variant_label' => $variant ? $this->makeVariantLabel($variant) : null,
                    'price' => $variant && !is_null($variant->price) ? (float) $variant->price : (float) $product->final_price,
                    'qty' => 1,
                    'stock' => $stock,
                    'image' => $variant && $variant->image ? $variant->image : ($product->primary_image ?: $product->image),
                ];
            }

            $cart[$rowId]['stock'] = $stock;
            $cart[$rowId]['price'] = $variant && !is_null($variant->price) ? (float) $variant->price : (float) $product->final_price;
            $cart[$rowId]['image'] = $variant && $variant->image ? $variant->image : ($product->primary_image ?: $product->image);
            $cart[$rowId]['variant_id'] = $variant?->id;
            $cart[$rowId]['variant_label'] = $variant ? $this->makeVariantLabel($variant) : null;
            $added++;
            $movedWishlistIds[] = $wishlistItem->id;
        }

        session()->put('cart', $cart);

        if (!empty($movedWishlistIds)) {
            auth()->user()
                ->wishlistItems()
                ->whereIn('id', array_values(array_unique($movedWishlistIds)))
                ->delete();
        }

        $messages = [];
        if ($added > 0) {
            $messages[] = $added . ' produs(e) adaugate in cos si scoase din favorite.';
        }
        if ($needsVariant > 0) {
            $messages[] = $needsVariant . ' produs(e) necesita alegerea unei variante.';
        }
        if ($outOfStock > 0) {
            $messages[] = $outOfStock . ' produs(e) sunt fara stoc.';
        }

        return redirect()->route('wishlist.index')->with(
            $added > 0 ? 'success' : 'error',
            implode(' ', $messages ?: ['Nu s-a putut adauga niciun produs in cos.'])
        );
    }

    private function makeVariantLabel(ProductVariant $variant): string
    {
        $variant->loadMissing(['attributes.attribute', 'attributes.option']);

        return $variant->attributes
            ->map(function ($attribute) {
                $name = $attribute->attribute->name ?? 'Atribut';
                $value = $attribute->option->label ?? $attribute->custom_value ?? 'Valoare';

                return $name . ': ' . $value;
            })
            ->implode(' / ');
    }
}
