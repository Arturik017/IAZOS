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

    <div class="market-page py-10">
        <div class="market-shell mx-auto space-y-8 px-4 sm:px-6 lg:px-8">

            @if(!empty($q))
                <div class="market-section bg-white rounded-xl shadow border border-gray-100 p-6">
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
                        <div class="market-empty mt-5">
                            <div class="market-empty-mark">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5M6 20l-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8l-2 2Z" />
                                </svg>
                            </div>
                            <div class="mt-5 font-semibold text-gray-900">Nu au fost gasiti selleri pentru aceasta cautare.</div>
                            <div class="mt-1 text-sm text-gray-500">Incearca o denumire de magazin sau un cuvant mai scurt.</div>
                        </div>
                        <div class="hidden">
                            Nu au fost găsiți selleri pentru această căutare.
                        </div>
                    @else
                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($sellers as $seller)
                                <a href="{{ route('seller.public.show', $seller->id) }}"
                                   class="market-card block rounded-xl border border-gray-200 bg-gray-50 p-5 hover:shadow transition">
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ $seller->sellerProfile->shop_name ?? $seller->name }}
                                    </div>

                                    @if(!empty($seller->sellerProfile->legal_name))
                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ $seller->sellerProfile->legal_name }}
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

            <div class="market-section bg-white rounded-xl shadow border border-gray-100 p-6">
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
                    <div class="market-empty mt-5">
                        <div class="market-empty-mark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </div>
                        <div class="mt-5 font-semibold text-gray-900">Nu exista produse pentru aceasta cautare.</div>
                        <div class="mt-1 text-sm text-gray-500">Verifica termenul cautat sau exploreaza categoriile principale.</div>
                        <a href="{{ route('home') }}" class="mt-5 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white">
                            Inapoi la marketplace
                        </a>
                    </div>
                    <div class="hidden">
                        Nu există produse pentru această căutare.
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">
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
