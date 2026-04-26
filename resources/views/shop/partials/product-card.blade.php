@php
    $image = \App\Support\MediaUrl::public($product->image);
    $finalPrice = $product->final_price ?? 0;
    $oldPrice = $product->price ?? null;
    $isPromo = (int)($product->is_promo ?? 0) === 1;
    $stock = (int)($product->stock ?? 0);
    $sellerName = $product->seller?->sellerProfile?->shop_name ?? $product->seller?->name ?? null;
    $avgRating = round((float)($product->reviews_avg_rating ?? 0), 1);
    $reviewsCount = (int)($product->reviews_count ?? 0);
    $filledStars = max(0, min(5, (int) round($avgRating)));
    $isWishlisted = auth()->check() ? \App\Support\WishlistState::has((int) $product->id) : false;
@endphp

<div class="market-product-card group overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
    <div class="relative overflow-hidden bg-gray-100">
        <a href="{{ route('product.show', $product) }}" class="block">
            @if($image)
                <img
                    src="{{ $image }}"
                    alt="{{ $product->name }}"
                    class="h-64 w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                >
            @else
                <div class="flex h-64 w-full items-center justify-center text-sm text-gray-400">
                    Fara imagine
                </div>
            @endif
        </a>

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if($isPromo)
                <span class="market-badge-purple rounded-full px-3 py-1 text-[11px] font-semibold">
                    Promo
                </span>
            @endif

            @if($stock > 0)
                <span class="rounded-full bg-green-100 px-3 py-1 text-[11px] font-semibold text-green-700">
                    In stoc
                </span>
            @else
                <span class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-semibold text-red-700">
                    Epuizat
                </span>
            @endif
        </div>

        @auth
            <div class="absolute right-3 top-3">
                @if($isWishlisted)
                    <form method="POST" action="{{ route('wishlist.destroy', $product) }}" class="m-0 inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-white/90 text-red-500 ring-1 ring-black/10 backdrop-blur-sm transition hover:bg-white" style="width: 2rem; height: 2rem; padding: 0;" aria-label="Scoate din favorite">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 0.95rem; height: 0.95rem; display: block;">
                                <path d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('wishlist.store', $product) }}" class="m-0 inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-white/90 text-gray-600 ring-1 ring-black/10 backdrop-blur-sm transition hover:bg-white hover:text-red-500" style="width: 2rem; height: 2rem; padding: 0;" aria-label="Adauga la favorite">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 0.95rem; height: 0.95rem; display: block;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>

    <a href="{{ route('product.show', $product) }}" class="block p-4">
        <div class="line-clamp-2 min-h-[48px] text-base font-bold text-gray-900 transition group-hover:text-[#33004a]">
            {{ $product->name }}
        </div>

        @if($sellerName)
            <div class="mt-2 text-sm text-gray-500">
                {{ $sellerName }}
            </div>
        @endif

        <div class="mt-3 flex items-center gap-2">
            <div class="text-sm text-yellow-500">
                {{ str_repeat('★', $filledStars) }}{{ str_repeat('☆', 5 - $filledStars) }}
            </div>
            <div class="text-sm text-gray-500">
                {{ $avgRating }} ({{ $reviewsCount }})
            </div>
        </div>

        <div class="mt-4 flex items-end gap-2">
            <div class="market-price text-xl font-extrabold text-gray-900">
                {{ number_format((float) $finalPrice, 2) }} MDL
            </div>

            @if($isPromo && $oldPrice && (float)$oldPrice > (float)$finalPrice)
                <div class="text-sm text-gray-400 line-through">
                    {{ number_format((float) $oldPrice, 2) }} MDL
                </div>
            @endif
        </div>
    </a>
</div>
