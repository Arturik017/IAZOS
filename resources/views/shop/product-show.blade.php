<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500">Preț fix (include vama + livrare în Moldova)</p>
            </div>

            <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">
                ← Înapoi
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                    <div class="h-[360px] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="h-[360px] w-full object-cover" alt="{{ $product->name }}">
                        @else
                            <div class="h-[360px] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Fără imagine</span>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                            Activ
                        </span>

                        <div class="text-sm text-gray-600">
                            Stoc: <span class="font-semibold text-gray-900">{{ $product->stock }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-sm text-gray-500">Preț final</div>
                        <div class="text-3xl font-extrabold text-gray-900">
                            {{ number_format($product->final_price, 2) }} MDL
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-900">Descriere</h3>
                        <p class="mt-2 text-gray-700 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="mt-7 flex flex-col sm:flex-row gap-3">
                        <form method="POST" action="{{ route('cart.add', $product) }}" class="w-full sm:w-auto">
                            @csrf
                            <button class="w-full px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                                Adaugă în coș
                            </button>
                        </form>


                        <button class="w-full sm:w-auto px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition">
                            Cumpără acum
                        </button>
                    </div>

                    <div class="mt-4 text-sm text-gray-500">
                        După plată, comanda intră în procesare și primești livrarea fără costuri ascunse.
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
