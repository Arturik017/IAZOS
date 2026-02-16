<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500">Detalii produs</p>
            </div>

            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

                <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- Sidebar categorii --}}
                <aside class="w-full lg:w-72" x-data="{ openCats: false }">
                
                    {{-- MOBILE TOGGLE (doar pe mobil) --}}
                    <div class="lg:hidden">
                        <button
                            type="button"
                            @click="openCats = !openCats"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-2xl bg-white shadow border border-gray-100"
                        >
                            <span class="font-semibold text-gray-900">Categorii</span>
                
                            {{-- Arrow DOAR pe mobil --}}
                            <!--<span class="text-gray-500" x-text="openCats ? '▲' : '▼'"></span>-->
                        </button>
                    </div>
                
                    {{-- SIDEBAR CONTENT (UN SINGUR INCLUDE) --}}
                    <div
                        class="mt-3 lg:mt-0 lg:sticky lg:top-6 lg:block"
                        :class="openCats ? 'block' : 'hidden'"
                    >
                        @include('shop.partials.sidebar')
                    </div>
                
                </aside>

                <div class="flex-1 space-y-8">

                    {{-- PRODUS --}}
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="bg-gray-50">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="w-full h-[320px] sm:h-[420px] object-cover"
                                         alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-[320px] sm:h-[420px] flex items-center justify-center text-gray-400">
                                        Fără imagine
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 sm:p-8">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-3xl font-extrabold text-gray-900">
                                        {{ number_format($product->final_price, 2) }} MDL
                                    </div>

                                    @if((int)$product->stock > 0)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            În stoc
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Stoc epuizat
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 text-sm text-gray-600">
                                    Stoc: <span class="font-semibold text-gray-900">{{ $product->stock }}</span>
                                </div>

                                @if(!empty($product->description))
                                    <div class="mt-5">
                                        <div class="text-sm font-semibold text-gray-900">Descriere</div>
                                        <div class="mt-2 text-sm text-gray-600 leading-relaxed">
                                            {{ $product->description }}
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-6 flex gap-3">
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                                                @disabled((int)$product->stock <= 0 || (int)$product->status !== 1)>
                                            Adaugă în coș
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.buy', $product) }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition disabled:opacity-50"
                                                @disabled((int)$product->stock <= 0 || (int)$product->status !== 1)>
                                            Cumpără acum
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 text-xs text-gray-400">
                                    Prețurile includ vama și livrarea în Moldova.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SIMILARE --}}
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Produse similare</h3>
                        <p class="text-sm text-gray-500">Din aceeași categorie/subcategorie.</p>

                        @if($similarProducts->isEmpty())
                            <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                                Nu există produse similare momentan.
                            </div>
                        @else
                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($similarProducts as $p)
                                    @include('shop.partials.product-card', ['product' => $p])
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
