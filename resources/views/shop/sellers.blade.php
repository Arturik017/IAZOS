<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Selleri</h2>
                <p class="text-sm text-gray-500">Magazine publice disponibile în marketplace.</p>
            </div>

            <form action="{{ route('sellers.index') }}" method="GET" class="w-full lg:w-auto">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Caută seller..."
                        class="w-full lg:w-80 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                    >
                    <button
                        type="submit"
                        class="px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black"
                    >
                        Caută
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Selleri găsiți</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $sellers->total() }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Căutare</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $q !== '' ? $q : 'Toți sellerii' }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Pagină</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $sellers->currentPage() }} / {{ $sellers->lastPage() }}
                    </div>
                </div>
            </div>

            @if($sellers->isEmpty())
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-10 text-center">
                    <div class="text-gray-900 font-semibold">Nu există selleri pentru această căutare.</div>
                    <div class="text-gray-500 text-sm mt-1">Încearcă alt nume sau alt cuvânt cheie.</div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($sellers as $seller)
                        @php
                            $profile = $seller->sellerProfile;
                            $avgRating = round((float)($seller->seller_reviews_received_avg_rating ?? 0), 1);
                            $reviewsCount = (int)($seller->seller_reviews_received_count ?? 0);
                            $filledStars = max(0, min(5, (int) round($avgRating)));
                        @endphp

                        <a href="{{ route('seller.public.show', $seller) }}"
                           class="block bg-white rounded-2xl shadow border border-gray-100 p-6 hover:shadow-lg hover:border-gray-200 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xl font-bold text-gray-900">
                                        {{ $profile->shop_name ?: $seller->name }}
                                    </div>

                                    @if($profile->legal_name)
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $profile->legal_name }}
                                        </div>
                                    @endif
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Aprobat
                                </span>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <div class="text-sm text-yellow-500">
                                    {{ str_repeat('★', $filledStars) }}{{ str_repeat('☆', 5 - $filledStars) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $avgRating }} ({{ $reviewsCount }})
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <div class="text-xs uppercase tracking-wide text-gray-500">Produse</div>
                                    <div class="mt-1 text-lg font-bold text-gray-900">
                                        {{ $seller->public_products_count }}
                                    </div>
                                </div>

                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <div class="text-xs uppercase tracking-wide text-gray-500">Tip</div>
                                    <div class="mt-1 text-sm font-bold text-gray-900">
                                        {{ $profile->seller_type ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            @if($profile->pickup_address)
                                <div class="mt-4 text-sm text-gray-600">
                                    <span class="font-semibold text-gray-900">Adresă:</span>
                                    {{ $profile->pickup_address }}
                                </div>
                            @endif

                            @if($profile->delivery_type)
                                <div class="mt-2 text-sm text-gray-600">
                                    <span class="font-semibold text-gray-900">Livrare:</span>
                                    {{ $profile->delivery_type }}
                                </div>
                            @endif

                            @if($profile->notes)
                                <div class="mt-4 text-sm text-gray-600 line-clamp-3">
                                    {{ $profile->notes }}
                                </div>
                            @endif

                            <div class="mt-5 text-sm font-semibold text-gray-900">
                                Vezi seller →
                            </div>
                        </a>
                    @endforeach
                </div>

                <div>
                    {{ $sellers->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>