<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\GuestWishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = auth()->check()
            ? $this->loadAuthenticatedItems()
            : $this->loadGuestItems();

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
                if (auth()->check()) {
                    auth()->user()->wishlistItems()->updateOrCreate(
                        ['product_id' => $product->id],
                        ['variant_id' => null]
                    );
                } else {
                    GuestWishlist::add((int) $product->id, null);
                }

                return back()->with('success', 'Produsul a fost adaugat la wishlist. Vei putea alege varianta mai tarziu.');
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

        if (auth()->check()) {
            auth()->user()->wishlistItems()->updateOrCreate(
                ['product_id' => $product->id],
                ['variant_id' => $variant?->id]
            );
        } else {
            GuestWishlist::add((int) $product->id, $variant?->id);
        }

        return back()->with('success', 'Produsul a fost adaugat la wishlist.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $variantId = request()->integer('variant_id') ?: null;

        if (auth()->check()) {
            $query = auth()->user()->wishlistItems()
                ->where('product_id', $product->id);

            if ($variantId) {
                $query->where('variant_id', $variantId);
            }

            $query->delete();
        } else {
            if ($variantId) {
                GuestWishlist::remove((int) $product->id, $variantId);
            } else {
                $rowsToRemove = GuestWishlist::items()
                    ->filter(fn ($item) => (int) $item['product_id'] === (int) $product->id)
                    ->pluck('row_id')
                    ->all();

                GuestWishlist::removeRows($rowsToRemove);
            }
        }

        return back()->with('success', 'Produsul a fost scos din wishlist.');
    }

    public function moveToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['string'],
        ]);

        if (!auth()->check()) {
            return $this->moveGuestWishlistToCart($data['item_ids']);
        }

        $numericItemIds = collect($data['item_ids'])
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        $wishlistItems = auth()->user()
            ->wishlistItems()
            ->whereIn('id', $numericItemIds)
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

    private function loadAuthenticatedItems(): Collection
    {
        return auth()->user()
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
            ->filter(fn ($item) => $this->isValidMarketplaceWishlistItem($item->product))
            ->values();
    }

    private function loadGuestItems(): Collection
    {
        $entries = GuestWishlist::items();

        $products = Product::query()
            ->with([
                'seller.sellerProfile',
                'category',
                'subcategory',
            ])
            ->whereIn('id', $entries->pluck('product_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $variantIds = $entries->pluck('variant_id')->filter()->unique()->values();
        $variants = ProductVariant::query()
            ->with(['attributes.attribute', 'attributes.option'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return $entries
            ->map(function ($entry) use ($products, $variants) {
                $product = $products->get((int) $entry['product_id']);
                $variant = $entry['variant_id'] ? $variants->get((int) $entry['variant_id']) : null;

                return (object) [
                    'id' => (string) $entry['row_id'],
                    'product_id' => (int) $entry['product_id'],
                    'variant_id' => $entry['variant_id'],
                    'product' => $product,
                    'variant' => $variant,
                ];
            })
            ->filter(fn ($item) => $this->isValidMarketplaceWishlistItem($item->product))
            ->values();
    }

    private function moveGuestWishlistToCart(array $itemIds): RedirectResponse
    {
        $guestItems = GuestWishlist::items()->keyBy('row_id');

        $products = Product::query()
            ->with(['variants' => fn ($query) => $query->where('is_active', true)])
            ->whereIn('id', $guestItems->pluck('product_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $variantIds = $guestItems->pluck('variant_id')->filter()->unique()->values();
        $variants = ProductVariant::query()
            ->with(['attributes.attribute', 'attributes.option'])
            ->whereIn('id', $variantIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $cart = session()->get('cart', []);
        $added = 0;
        $needsVariant = 0;
        $outOfStock = 0;
        $movedRowIds = [];

        foreach ($itemIds as $itemId) {
            $wishlistItem = $guestItems->get((string) $itemId);
            $product = $wishlistItem ? $products->get((int) $wishlistItem['product_id']) : null;

            if (!$product || !$wishlistItem || !$this->isValidMarketplaceWishlistItem($product)) {
                continue;
            }

            $variant = $wishlistItem['variant_id']
                ? $variants->get((int) $wishlistItem['variant_id'])
                : null;

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
            $movedRowIds[] = (string) $wishlistItem['row_id'];
        }

        session()->put('cart', $cart);

        if (!empty($movedRowIds)) {
            GuestWishlist::removeRows(array_values(array_unique($movedRowIds)));
        }

        return redirect()->route('wishlist.index')->with(
            $added > 0 ? 'success' : 'error',
            implode(' ', $this->buildMoveMessages($added, $needsVariant, $outOfStock))
        );
    }

    private function buildMoveMessages(int $added, int $needsVariant, int $outOfStock): array
    {
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

        return $messages ?: ['Nu s-a putut adauga niciun produs in cos.'];
    }

    private function isValidMarketplaceWishlistItem(?Product $product): bool
    {
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
    }
}
