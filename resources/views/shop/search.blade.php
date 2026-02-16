<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-12 gap-6">

                {{-- Sidebar categorii --}}
                <aside class="col-span-12 lg:col-span-3">
                    <div class="lg:sticky lg:top-6">
                        @include('shop.partials.sidebar')
                    </div>
                </aside>

                {{-- Rezultate --}}
                <main class="col-span-12 lg:col-span-9">
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h1 class="text-xl font-bold text-gray-900">
                            Rezultate căutare
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Căutare: <span class="font-semibold text-gray-900">{{ $q ?: '—' }}</span>
                            • Găsite: <span class="font-semibold text-gray-900">{{ $products->total() }}</span>
                        </p>
                    </div>

                    <div class="mt-6">
                        @if($products->isEmpty())
                            <div class="bg-white p-10 rounded-2xl shadow text-center border border-gray-100">
                                <div class="text-gray-900 font-semibold">Nu am găsit produse.</div>
                                <div class="text-gray-500 text-sm mt-1">Încearcă alte cuvinte.</div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($products as $product)
                                    <a href="{{ route('product.show', $product) }}"
                                       class="block bg-white rounded-2xl shadow border border-gray-100 overflow-hidden hover:shadow-xl transition">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}"
                                                 class="h-44 w-full object-cover" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-44 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                                Fără imagine
                                            </div>
                                        @endif

                                        <div class="p-5">
                                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>

                                            <div class="mt-2 flex items-end justify-between">
                                                <div class="text-xl font-extrabold text-gray-900">
                                                    {{ number_format($product->final_price, 2) }} MDL
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Stoc: <span class="font-semibold text-gray-900">{{ $product->stock }}</span>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <form method="POST" action="{{ route('cart.add', $product) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                                                        Adaugă în coș
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $products->links() }}
                            </div>
                        @endif
                    </div>
                </main>

            </div>
        </div>
    </div>
</x-app-layout>
