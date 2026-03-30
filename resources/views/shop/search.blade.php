<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Rezultate căutare
                </h2>
                <p class="text-sm text-gray-500">
                    Căutare pentru: <span class="font-semibold">{{ $q ?: '—' }}</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            @if(!empty($q))
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Selleri găsiți</h3>
                            <p class="text-sm text-gray-500">Magazine și selleri care se potrivesc cu căutarea.</p>
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $sellers->count() }} rezultate
                        </div>
                    </div>

                    @if($sellers->isEmpty())
                        <div class="mt-5 text-sm text-gray-500">
                            Nu au fost găsiți selleri pentru această căutare.
                        </div>
                    @else
                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($sellers as $seller)
                                <a href="{{ route('seller.public.show', $seller->id) }}"
                                   class="block rounded-2xl border border-gray-200 bg-gray-50 p-5 hover:border-blue-300 hover:shadow transition">
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ $seller->sellerProfile->shop_name ?? $seller->name }}
                                    </div>

                                    @if(!empty($seller->sellerProfile->legal_name))
                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ $seller->sellerProfile->legal_name }}
                                        </div>
                                    @endif

                                    @if(!empty($seller->sellerProfile->pickup_address))
                                        <div class="mt-2 text-sm text-gray-500">
                                            {{ $seller->sellerProfile->pickup_address }}
                                        </div>
                                    @endif

                                    @if(!empty($seller->sellerProfile->notes))
                                        <div class="mt-3 text-sm text-gray-600 line-clamp-3">
                                            {{ $seller->sellerProfile->notes }}
                                        </div>
                                    @endif

                                    <div class="mt-4 text-sm font-semibold text-blue-600">
                                        Vezi seller →
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Produse găsite</h3>
                        <p class="text-sm text-gray-500">Produse care se potrivesc cu căutarea ta.</p>
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $products->total() }} rezultate
                    </div>
                </div>

                @if($products->isEmpty())
                    <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                        Nu există produse pentru această căutare.
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            @include('shop.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>