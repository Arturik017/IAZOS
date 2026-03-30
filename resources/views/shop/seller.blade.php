<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $sellerProfile->shop_name ?: $user->name }}
                </h2>
                <p class="text-sm text-gray-500">Pagina publică a sellerului</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('sellers.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                    ← Toți sellerii
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $sellerAvgRating = $user->averageSellerRating();
        $sellerReviewsCount = $user->sellerReviewsCount();
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            @if(session('success'))
                <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="p-6 lg:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-3xl font-bold text-gray-900">
                                    {{ $sellerProfile->shop_name ?: $user->name }}
                                </h3>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Seller verificat
                                </span>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <div class="text-yellow-500 text-lg">
                                    {{ str_repeat('★', (int) round($sellerAvgRating)) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $sellerAvgRating }} din 5 ({{ $sellerReviewsCount }} review-uri)
                                </div>
                            </div>

                            @if($sellerProfile->legal_name)
                                <div class="mt-2 text-sm text-gray-500">
                                    {{ $sellerProfile->legal_name }}
                                </div>
                            @endif

                            @if($sellerProfile->notes)
                                <div class="mt-5 text-sm leading-relaxed text-gray-600">
                                    {{ $sellerProfile->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-full lg:min-w-[420px]">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Produse publice</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['products_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">În stoc</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['in_stock_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Promo</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['promo_count'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Date seller</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-semibold text-gray-900">Magazin:</span>
                            {{ $sellerProfile->shop_name ?: '—' }}
                        </div>

                        <div>
                            <span class="font-semibold text-gray-900">Tip seller:</span>
                            {{ $sellerProfile->seller_type ?: '—' }}
                        </div>

                        @if($sellerProfile->phone)
                            <div>
                                <span class="font-semibold text-gray-900">Telefon:</span>
                                {{ $sellerProfile->phone }}
                            </div>
                        @endif

                        @if($sellerProfile->pickup_address)
                            <div>
                                <span class="font-semibold text-gray-900">Adresă:</span>
                                {{ $sellerProfile->pickup_address }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Livrare</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-semibold text-gray-900">Tip livrare:</span>
                            {{ $sellerProfile->delivery_type ?: '—' }}
                        </div>

                        @if($sellerProfile->courier_company)
                            <div>
                                <span class="font-semibold text-gray-900">Curier:</span>
                                {{ $sellerProfile->courier_company }}
                            </div>
                        @endif

                        @if($sellerProfile->courier_contract_details)
                            <div>
                                <span class="font-semibold text-gray-900">Detalii curier:</span>
                                {{ $sellerProfile->courier_contract_details }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Caută în produsele sellerului</h3>

                    <form action="{{ route('seller.public.show', $user) }}" method="GET" class="mt-4 space-y-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Nume produs..."
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                        >

                        <select
                            name="sort"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                        >
                            <option value="new" @selected($sort === 'new')>Cele mai noi</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Preț crescător</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Preț descrescător</option>
                        </select>

                        <button
                            type="submit"
                            class="w-full px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black"
                        >
                            Aplică
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Review-uri seller</h3>
                <p class="text-sm text-gray-500">Doar cumpărătorii reali pot lăsa review sellerului.</p>

                @auth
                    @if($canReviewSeller)
                        <form method="POST" action="{{ route('seller.reviews.store', $user) }}" class="mt-5 space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <select name="rating" class="w-full border rounded-xl p-3">
                                    <option value="">Alege rating</option>
                                    <option value="5" @selected(old('rating', $mySellerReview?->rating) == 5)>5 stele</option>
                                    <option value="4" @selected(old('rating', $mySellerReview?->rating) == 4)>4 stele</option>
                                    <option value="3" @selected(old('rating', $mySellerReview?->rating) == 3)>3 stele</option>
                                    <option value="2" @selected(old('rating', $mySellerReview?->rating) == 2)>2 stele</option>
                                    <option value="1" @selected(old('rating', $mySellerReview?->rating) == 1)>1 stea</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comentariu</label>
                                <textarea name="comment" rows="4" class="w-full border rounded-xl p-3" placeholder="Spune părerea ta despre seller...">{{ old('comment', $mySellerReview?->comment) }}</textarea>
                            </div>

                            <button class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                                {{ $mySellerReview ? 'Actualizează review-ul' : 'Trimite review-ul' }}
                            </button>
                        </form>
                    @else
                        <div class="mt-5 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3">
                            Poți lăsa review sellerului doar dacă ai cumpărat și achitat de la el.
                        </div>
                    @endif
                @else
                    <div class="mt-5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 px-4 py-3">
                        Pentru review trebuie să fii logat și să fi cumpărat de la acest seller.
                    </div>
                @endauth

                <div class="mt-8 space-y-5">
                    @forelse($sellerReviews as $review)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                    <div class="text-yellow-500 text-sm">{{ str_repeat('★', (int) $review->rating) }}</div>
                                </div>

                                <div class="text-xs text-gray-400">
                                    {{ $review->created_at->format('d.m.Y') }}
                                </div>
                            </div>

                            @if($review->comment)
                                <div class="mt-3 text-sm text-gray-700">
                                    {{ $review->comment }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">
                            Nu există review-uri încă pentru acest seller.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Produsele sellerului</h3>
                        <p class="text-sm text-gray-500">
                            {{ $products->total() }} produse găsite
                        </p>
                    </div>

                    @if($q !== '')
                        <a href="{{ route('seller.public.show', $user) }}"
                           class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Resetează filtrul
                        </a>
                    @endif
                </div>

                @if($products->isEmpty())
                    <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                        Sellerul nu are produse publice pentru filtrul selectat.
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