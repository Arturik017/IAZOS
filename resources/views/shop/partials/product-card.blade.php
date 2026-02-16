@php
    /** @var \App\Models\Product $product */
    $url = route('product.show', $product);
@endphp

<div
    class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden hover:shadow-lg transition cursor-pointer select-none"
    role="link"
    tabindex="0"
    onclick="window.location.href='{{ $url }}'"
    onkeydown="if(event.key === 'Enter'){ window.location.href='{{ $url }}' }"
>
    {{-- IMAGINE --}}
    <div class="relative">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}"
                 class="h-44 w-full object-cover"
                 alt="{{ $product->name }}">
        @else
            <div class="h-44 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                Fără imagine
            </div>
        @endif

        {{-- ICONIȚĂ COȘ (Adaugă) --}}
        <form
            method="POST"
            action="{{ route('cart.add', $product) }}"
            class="absolute top-3 right-3"
            onclick="event.stopPropagation();"
        >
            @csrf
            <button
                type="submit"
                class="w-11 h-11 rounded-full bg-white/95 hover:bg-white shadow border border-gray-200 flex items-center justify-center"
                title="Adaugă în coș"
                aria-label="Adaugă în coș"
                onclick="event.stopPropagation();"
            >
                {{-- Icon coș --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M6 6h15l-1.5 9h-13L5 3H2"/>
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M9 22a1 1 0 100-2 1 1 0 000 2zM18 22a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </button>
        </form>

        {{-- Badge promo (opțional) --}}
        @if((int)($product->is_promo ?? 0) === 1)
            <div class="absolute bottom-3 left-3">
                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                    Promo
                </span>
            </div>
        @endif
    </div>

    {{-- CONȚINUT --}}
    <div class="p-5">
        <div class="font-semibold text-gray-900 leading-snug line-clamp-2">
            {{ $product->name }}
        </div>

        <div class="mt-2 flex items-end justify-between gap-3">
            <div class="text-xl font-extrabold text-gray-900">
                {{ number_format($product->final_price, 2) }} MDL
            </div>

            <div class="text-sm text-gray-500">
                Stoc: <span class="font-semibold text-gray-900">{{ $product->stock }}</span>
            </div>
        </div>

        {{-- Hint mic (opțional) --}}
        <div class="mt-3 text-xs text-gray-400">
            Click pe card pentru detalii
        </div>
    </div>
</div>
