<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Favoritele mele</h2>
                <p class="text-sm text-gray-500">Pastreaza produsele dorite si muta-le rapid in cos.</p>
            </div>

            <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">
                ← Inapoi la magazin
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-6">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($items->isEmpty())
                <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center shadow">
                    <div class="text-lg font-semibold text-gray-900">Nu ai produse in wishlist.</div>
                    <div class="mt-1 text-sm text-gray-500">Apasa pe inimioara la produse ca sa le salvezi aici.</div>

                    <a href="{{ route('home') }}"
                       class="mt-5 inline-block rounded-xl bg-gray-900 px-6 py-3 font-semibold text-white hover:bg-black transition">
                        Vezi produse
                    </a>
                </div>
            @else
                <form method="POST" action="{{ route('wishlist.move_to_cart') }}" x-data="{ all: false }">
                    @csrf

                    <div class="mb-5 flex flex-col gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="text-lg font-bold text-gray-900">{{ $items->count() }} produse salvate</div>
                            <div class="mt-1 text-sm text-gray-500">Bifeaza mai multe produse si trimite-le direct in cos.</div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" class="rounded border-gray-300" x-model="all" @change="document.querySelectorAll('.wishlist-product-checkbox').forEach(cb => cb.checked = all)">
                                Selecteaza tot
                            </label>

                            <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-black">
                                Adauga selectate in cos
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($items as $item)
                            @php
                                $product = $item->product;
                                $variant = $item->variant;
                                $image = \App\Support\MediaUrl::public(($variant && $variant->image) ? $variant->image : ($product->primary_image ?: $product->image));
                                $sellerName = $product->seller?->sellerProfile?->shop_name ?? $product->seller?->name ?? null;
                                $needsVariantSelection = $product->variants()->where('is_active', true)->exists() && !$variant;
                                $variantLabel = $variant
                                    ? $variant->attributes->map(fn ($attribute) => ($attribute->attribute->name ?? 'Atribut') . ': ' . ($attribute->option->label ?? $attribute->custom_value ?? 'Valoare'))->implode(' / ')
                                    : null;
                            @endphp

                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow">
                                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                                    <div class="flex items-start gap-4">
                                        <label class="mt-1">
                                            <input
                                                type="checkbox"
                                                name="item_ids[]"
                                                value="{{ $item->id }}"
                                                class="wishlist-product-checkbox rounded border-gray-300"
                                                {{ $needsVariantSelection ? 'disabled' : '' }}
                                            >
                                        </label>

                                        <div class="w-24 shrink-0">
                                            @if($image)
                                                <img src="{{ $image }}" alt="{{ $product->name }}" class="h-24 w-24 rounded-xl border border-gray-200 object-cover">
                                            @else
                                                <div class="flex h-24 w-24 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-400">
                                                    Fara imagine
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div>
                                                <a href="{{ route('product.show', $product) }}" class="text-lg font-semibold text-gray-900 hover:text-gray-700">
                                                    {{ $product->name }}
                                                </a>

                                                @if($sellerName)
                                                    <div class="mt-1 text-sm text-gray-500">{{ $sellerName }}</div>
                                                @endif

                                                @if($variantLabel)
                                                    <div class="mt-2 text-sm font-medium text-gray-700">
                                                        {{ $variantLabel }}
                                                    </div>
                                                @endif

                                                <div class="mt-3 text-xl font-extrabold text-gray-900">
                                                    {{ number_format((float) (($variant && !is_null($variant->price)) ? $variant->price : $product->final_price), 2) }} MDL
                                                </div>

                                                <div class="mt-2 text-sm {{ (int) $product->stock > 0 ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ (int) $product->stock > 0 ? 'In stoc' : 'Fara stoc' }}
                                                </div>

                                                @if($needsVariantSelection)
                                                    <div class="mt-2 text-sm text-amber-700">
                                                        Acest produs are variante. Alege varianta din pagina produsului inainte sa-l adaugi in cos.
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap gap-3">
                                                <a href="{{ route('product.show', $product) }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                                                    Vezi produsul
                                                </a>

                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('wishlist-remove-{{ $item->id }}').submit();"
                                                    class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100"
                                                >
                                                    Scoate
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>

                @foreach($items as $item)
                    @if($item->product)
                        <form id="wishlist-remove-{{ $item->id }}" method="POST" action="{{ route('wishlist.destroy', $item->product) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                            @if($item->variant_id)
                                <input type="hidden" name="variant_id" value="{{ $item->variant_id }}">
                            @endif
                        </form>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
