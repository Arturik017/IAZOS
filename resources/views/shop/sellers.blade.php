<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Selleri</h2>
                <p class="text-sm text-gray-500">Magazine publice disponibile in marketplace.</p>
            </div>

            <form action="{{ route('sellers.index') }}" method="GET" class="w-full lg:w-auto">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Cauta seller..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200 lg:w-80"
                    >
                    <button
                        type="submit"
                        class="rounded-xl bg-gray-900 px-5 py-3 font-semibold text-white hover:bg-black"
                    >
                        Cauta
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

    @php
        $canUseFollowers = \App\Models\User::supportsSellerFollowers();
    @endphp

    <div class="market-page py-10">
        <div class="market-shell mx-auto space-y-8 px-4 sm:px-6 lg:px-8">

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

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="market-section rounded-xl border border-gray-100 bg-white p-5 shadow">
                    <div class="text-sm text-gray-500">Selleri gasiti</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $sellers->total() }}</div>
                </div>

                <div class="market-section rounded-xl border border-gray-100 bg-white p-5 shadow">
                    <div class="text-sm text-gray-500">Cautare</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $q !== '' ? $q : 'Toti sellerii' }}
                    </div>
                </div>

                <div class="market-section rounded-xl border border-gray-100 bg-white p-5 shadow">
                    <div class="text-sm text-gray-500">Pagina</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $sellers->currentPage() }} / {{ $sellers->lastPage() }}
                    </div>
                </div>
            </div>

            @if($sellers->isEmpty())
                <div class="market-section rounded-xl border border-gray-100 bg-white p-10 text-center shadow">
                    <div class="font-semibold text-gray-900">Nu exista selleri pentru aceasta cautare.</div>
                    <div class="mt-1 text-sm text-gray-500">Incearca alt nume sau alt cuvant cheie.</div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    @foreach($sellers as $seller)
                        @php
                            $profile = $seller->sellerProfile;
                            $avgRating = round((float) ($seller->seller_reviews_received_avg_rating ?? 0), 1);
                            $reviewsCount = (int) ($seller->seller_reviews_received_count ?? 0);
                            $filledStars = max(0, min(5, (int) round($avgRating)));
                            $avatarUrl = $profile?->avatar_path ? \App\Support\MediaUrl::public($profile->avatar_path) : null;
                            $avatarInitial = strtoupper(mb_substr($profile->shop_name ?: $seller->name, 0, 1));
                            $canShowFollowButton = auth()->check()
                                && $canUseFollowers
                                && auth()->id() !== $seller->id;
                            $isFollowing = (bool) ($seller->is_following ?? false);
                            $activeStoryIds = collect($seller->active_story_ids ?? [])->values()->all();
                            $hasActiveStories = count($activeStoryIds) > 0;
                        @endphp

                        <div class="market-card rounded-xl border border-gray-100 bg-white p-6 shadow transition hover:border-gray-200 hover:shadow-lg">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    @if($hasActiveStories)
                                        <a
                                            href="{{ route('seller.public.show', ['user' => $seller, 'story' => 1]) }}"
                                            class="story-ring h-14 w-14 shrink-0 rounded-full p-[3px]"
                                            data-story-ids='@json($activeStoryIds)'
                                            aria-label="Deschide story-urile sellerului"
                                        >
                                            <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-gray-50">
                                                @if($avatarUrl)
                                                    <img src="{{ $avatarUrl }}" alt="{{ $profile->shop_name ?: $seller->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-lg font-bold text-gray-500">
                                                        {{ $avatarInitial }}
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    @else
                                        <div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 bg-gray-50">
                                            @if($avatarUrl)
                                                <img src="{{ $avatarUrl }}" alt="{{ $profile->shop_name ?: $seller->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-lg font-bold text-gray-500">
                                                    {{ $avatarInitial }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

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
                                </div>

                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Aprobat
                                </span>
                            </div>

                            @if($canShowFollowButton)
                                <div class="mt-3">
                                    @if($isFollowing)
                                        <form method="POST" action="{{ route('seller.follow.destroy', $seller) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                                            >
                                                Nu mai urmari
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('seller.follow.store', $seller) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black"
                                            >
                                                Urmareste sellerul
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <div class="text-sm text-yellow-500">
                                    {{ str_repeat('★', $filledStars) }}{{ str_repeat('☆', 5 - $filledStars) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $avgRating }} ({{ $reviewsCount }})
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ (int) ($seller->followers_count ?? 0) }} followers
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
                                    <span class="font-semibold text-gray-900">Adresa:</span>
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
                                <div class="mt-4 line-clamp-3 text-sm text-gray-600">
                                    {{ $profile->notes }}
                                </div>
                            @endif

                            <div class="mt-5">
                                <a href="{{ route('seller.public.show', $seller) }}"
                                   class="text-sm font-semibold text-gray-900 hover:text-gray-700">
                                    Vezi seller →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>
                    {{ $sellers->links() }}
                </div>
            @endif

        </div>
    </div>

    <script>
        (function () {
            const seen = JSON.parse(window.localStorage.getItem('iazos_seen_stories') || '{}');
            document.querySelectorAll('.story-ring[data-story-ids]').forEach((element) => {
                try {
                    const ids = JSON.parse(element.dataset.storyIds || '[]');
                    const allSeen = ids.length > 0 && ids.every((id) => !!seen[String(id)]);
                    element.classList.add(allSeen ? 'bg-gray-300' : 'bg-gradient-to-br', ...(allSeen ? [] : ['from-orange-400', 'via-fuchsia-500', 'to-blue-500']));
                } catch (e) {
                    element.classList.add('bg-gradient-to-br', 'from-orange-400', 'via-fuchsia-500', 'to-blue-500');
                }
            });
        })();
    </script>
</x-app-layout>
