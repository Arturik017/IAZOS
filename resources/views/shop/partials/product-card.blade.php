@php
    $image = $product->image ? asset('storage/' . $product->image) : null;
    $finalPrice = $product->final_price ?? 0;
    $oldPrice = $product->price ?? null;
    $isPromo = (int)($product->is_promo ?? 0) === 1;
    $stock = (int)($product->stock ?? 0);
    $sellerName = $product->seller?->sellerProfile?->shop_name ?? $product->seller?->name ?? null;

    $avgRating = round((float)($product->reviews_avg_rating ?? 0), 1);
    $reviewsCount = (int)($product->reviews_count ?? 0);
    $filledStars = max(0, min(5, (int) round($avgRating)));
@endphp

<a href="{{ route('product.show', $product) }}"
   class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <div class="relative overflow-hidden bg-gray-100">
        @if($image)
            <img
                src="{{ $image }}"
                alt="{{ $product->name }}"
                class="h-64 w-full object-cover transition duration-500 group-hover:scale-[1.03]"
            >
        @else
            <div class="flex h-64 w-full items-center justify-center text-sm text-gray-400">
                Fără imagine
            </div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if($isPromo)
                <span class="rounded-full bg-yellow-100 px-3 py-1 text-[11px] font-semibold text-yellow-800">
                    Promo
                </span>
            @endif

            @if($stock > 0)
                <span class="rounded-full bg-green-100 px-3 py-1 text-[11px] font-semibold text-green-700">
                    În stoc
                </span>
            @else
                <span class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-semibold text-red-700">
                    Epuizat
                </span>
            @endif
        </div>
    </div>

    <div class="p-4">
        <div class="line-clamp-2 min-h-[48px] text-base font-bold text-gray-900">
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
            <div class="text-xl font-extrabold text-gray-900">
                {{ number_format((float) $finalPrice, 2) }} MDL
            </div>

            @if($isPromo && $oldPrice && (float)$oldPrice > (float)$finalPrice)
                <div class="text-sm text-gray-400 line-through">
                    {{ number_format((float) $oldPrice, 2) }} MDL
                </div>
            @endif
        </div>
    </div>
</a>