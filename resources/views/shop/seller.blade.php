<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $sellerProfile->shop_name ?: $user->name }}
                </h2>
                <p class="text-sm text-gray-500">Pagina publica a sellerului</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('sellers.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                    Toți sellerii
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $sellerAvgRating = $user->averageSellerRating();
        $sellerReviewsCount = $user->sellerReviewsCount();
        $sellerAvatar = $sellerProfile->avatar_path ? \App\Support\MediaUrl::public($sellerProfile->avatar_path) : null;
        $sellerInitial = strtoupper(mb_substr($sellerProfile->shop_name ?: $user->name, 0, 1));
        $hasSellerStories = $sellerStories->isNotEmpty();
        $storyGroupsPayload = collect($storyGroups ?? [])->values();
        $canShowFollowButton = auth()->check()
            && \App\Models\User::supportsSellerFollowers()
            && auth()->id() !== $user->id;
    @endphp

    <script>
        window.sellerStoryConfig = {
            groups: @json($storyGroupsPayload),
            currentSellerId: @json($user->id),
            autoOpen: @json(request()->boolean('story')),
            isAuthenticated: @json(auth()->check()),
            loginUrl: @json(route('login')),
            csrfToken: @json(csrf_token()),
            storyMessageUrlTemplate: @json(route('stories.message', ['story' => '__STORY__'])),
            storyLikeUrlTemplate: @json(route('stories.like', ['story' => '__STORY__'])),
            sellerShowUrlTemplate: @json(route('seller.public.show', ['user' => '__SELLER__'])),
        };

        window.sellerStoryViewer = function (config) {
            return {
                groups: config.groups || [],
                currentSellerId: config.currentSellerId || null,
                autoOpen: !!config.autoOpen,
                timeline: [],
                isOpen: false,
                activeIndex: 0,
                imageTimer: null,
                imageDuration: 5500,
                imageStartedAt: null,
                currentProgress: 0,
                videoError: false,
                videoProgress: 0,
                seenStories: {},
                storyMessageText: '',
                storyMessageBusy: false,
                storyLikeBusy: false,
                toastMessage: '',

                init() {
                    this.loadSeen();
                    this.buildTimeline();

                    if (this.autoOpen && this.timeline.length) {
                        this.openGroupBySeller(this.currentSellerId);
                    }
                },

                buildTimeline() {
                    this.timeline = [];

                    this.groups.forEach((group, groupIndex) => {
                        (group.stories || []).forEach((story, storyIndex) => {
                            this.timeline.push({
                                ...story,
                                groupIndex,
                                storyIndex,
                                seller_id: group.seller_id,
                                seller_name: group.seller_name,
                                seller_avatar: group.seller_avatar,
                                seller_initial: (group.seller_name || 'S').trim().charAt(0).toUpperCase() || 'S',
                                group_story_ids: (group.stories || []).map(item => item.id),
                            });
                        });
                    });
                },

                loadSeen() {
                    try {
                        this.seenStories = JSON.parse(window.localStorage.getItem('iazos_seen_stories') || '{}') || {};
                    } catch (e) {
                        this.seenStories = {};
                    }
                },

                saveSeen() {
                    window.localStorage.setItem('iazos_seen_stories', JSON.stringify(this.seenStories));
                },

                markSeen(storyId) {
                    if (!storyId) return;
                    this.seenStories[String(storyId)] = true;
                    this.saveSeen();
                },

                isGroupSeenBySeller(sellerId) {
                    const group = this.groups.find(item => Number(item.seller_id) === Number(sellerId));
                    if (!group || !(group.stories || []).length) return false;
                    return group.stories.every(story => !!this.seenStories[String(story.id)]);
                },

                storyRingClassBySeller(sellerId) {
                    return this.isGroupSeenBySeller(sellerId)
                        ? 'bg-gray-300'
                        : 'bg-gradient-to-br from-orange-400 via-fuchsia-500 to-blue-500';
                },

                openStory(index = 0) {
                    if (!this.timeline.length) return;
                    this.activeIndex = index;
                    this.isOpen = true;
                    this.storyMessageText = '';
                    this.$nextTick(() => this.playCurrent());
                },

                openGroupBySeller(sellerId) {
                    const index = this.timeline.findIndex(item => Number(item.seller_id) === Number(sellerId));
                    if (index === -1) return;
                    this.openStory(index);
                },

                closeStory() {
                    this.isOpen = false;
                    this.stopImageTimer();
                    this.storyMessageText = '';
                    if (this.$refs.storyVideo) {
                        this.$refs.storyVideo.pause();
                    }
                },

                currentItem() {
                    return this.timeline[this.activeIndex] || null;
                },

                prev() {
                    if (!this.timeline.length) return;
                    this.activeIndex = this.activeIndex === 0 ? this.timeline.length - 1 : this.activeIndex - 1;
                    this.playCurrent();
                },

                next() {
                    if (!this.timeline.length) return;
                    this.activeIndex = this.activeIndex === this.timeline.length - 1 ? 0 : this.activeIndex + 1;
                    this.playCurrent();
                },

                playCurrent() {
                    this.stopImageTimer();
                    this.currentProgress = 0;
                    this.videoProgress = 0;
                    this.videoError = false;
                    this.storyMessageText = '';

                    this.$nextTick(() => {
                        const item = this.currentItem();
                        if (!item) return;
                        this.markSeen(item.id);

                        if (item.media_type === 'video' && this.$refs.storyVideo) {
                            this.$refs.storyVideo.currentTime = 0;
                            this.$refs.storyVideo.muted = false;
                            this.$refs.storyVideo.volume = 1;
                            this.$refs.storyVideo.play().catch(() => {});
                            return;
                        }

                        this.startImageTimer();
                    });
                },

                startImageTimer() {
                    this.imageStartedAt = Date.now();
                    this.currentProgress = 0;

                    this.imageTimer = setInterval(() => {
                        const elapsed = Date.now() - this.imageStartedAt;
                        this.currentProgress = Math.min(100, (elapsed / this.imageDuration) * 100);

                        if (elapsed >= this.imageDuration) {
                            this.next();
                        }
                    }, 80);
                },

                stopImageTimer() {
                    if (this.imageTimer) {
                        clearInterval(this.imageTimer);
                    }
                    this.imageTimer = null;
                },

                prepareVideo() {
                    if (!this.$refs.storyVideo) return;
                    this.$refs.storyVideo.muted = false;
                    this.$refs.storyVideo.volume = 1;
                },

                updateVideoProgress() {
                    if (!this.$refs.storyVideo) return;

                    const duration = this.$refs.storyVideo.duration || 0;
                    const currentTime = this.$refs.storyVideo.currentTime || 0;

                    if (duration > 0) {
                        this.videoProgress = Math.min(100, (currentTime / duration) * 100);
                        this.currentProgress = this.videoProgress;
                    }
                },

                progressStyle(index) {
                    if (index < this.activeIndex) return 'width: 100%';
                    if (index > this.activeIndex) return 'width: 0%';
                    return `width: ${this.currentProgress}%`;
                },

                currentSellerUrl() {
                    const item = this.currentItem();
                    if (!item) return '#';
                    return item.seller_url || config.sellerShowUrlTemplate.replace('__SELLER__', item.seller_id);
                },

                ensureAuth() {
                    if (config.isAuthenticated) {
                        return true;
                    }

                    window.location.href = config.loginUrl;
                    return false;
                },

                storyMessageUrl(storyId) {
                    return config.storyMessageUrlTemplate.replace('__STORY__', storyId);
                },

                storyLikeUrl(storyId) {
                    return config.storyLikeUrlTemplate.replace('__STORY__', storyId);
                },

                setToast(message) {
                    this.toastMessage = message;
                    setTimeout(() => {
                        if (this.toastMessage === message) {
                            this.toastMessage = '';
                        }
                    }, 2200);
                },

                async sendStoryMessage() {
                    if (!this.ensureAuth()) return;

                    const item = this.currentItem();
                    const body = (this.storyMessageText || '').trim();
                    if (!item || !body || this.storyMessageBusy) return;

                    this.storyMessageBusy = true;

                    try {
                        const response = await fetch(this.storyMessageUrl(item.id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify({ body }),
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Mesajul nu a putut fi trimis.');
                        }

                        this.storyMessageText = '';
                        window.location.href = payload.conversation_url;
                    } catch (error) {
                        this.setToast(error.message || 'Mesajul nu a putut fi trimis.');
                    } finally {
                        this.storyMessageBusy = false;
                    }
                },

                async toggleStoryLike() {
                    if (!this.ensureAuth()) return;

                    const item = this.currentItem();
                    if (!item || this.storyLikeBusy) return;

                    this.storyLikeBusy = true;

                    try {
                        const method = item.is_liked ? 'DELETE' : 'POST';
                        const response = await fetch(this.storyLikeUrl(item.id), {
                            method,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Nu am putut actualiza aprecierea.');
                        }

                        this.timeline = this.timeline.map((story) => {
                            if (Number(story.id) !== Number(item.id)) {
                                return story;
                            }

                            return {
                                ...story,
                                is_liked: !!payload.liked,
                                likes_count: Number(payload.likes_count || 0),
                            };
                        });
                    } catch (error) {
                        this.setToast(error.message || 'Nu am putut actualiza aprecierea.');
                    } finally {
                        this.storyLikeBusy = false;
                    }
                }
            }
        };
    </script>

    <div class="market-page py-10" x-data="window.sellerStoryViewer(window.sellerStoryConfig)" x-init="init()">
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

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <ul class="ml-5 list-disc text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="market-section overflow-hidden rounded-xl border border-gray-100 bg-white shadow">
                <div class="p-6 lg:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex max-w-3xl items-start gap-5">
                            @if($hasSellerStories)
                                <button
                                    type="button"
                                    x-on:click="openGroupBySeller({{ $user->id }})"
                                    class="group relative h-20 w-20 shrink-0 rounded-full p-[3px] transition hover:scale-[1.02]"
                                    x-bind:class="storyRingClassBySeller({{ $user->id }})"
                                    aria-label="Deschide story-urile sellerului"
                                >
                                    <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-gray-50">
                                        @if($sellerAvatar)
                                            <img src="{{ $sellerAvatar }}"
                                                 alt="{{ $sellerProfile->shop_name ?: $user->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-gray-500">
                                                {{ $sellerInitial }}
                                            </div>
                                        @endif
                                    </div>
                                </button>
                            @else
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full border border-gray-200 bg-gray-50">
                                    @if($sellerAvatar)
                                        <img src="{{ $sellerAvatar }}"
                                             alt="{{ $sellerProfile->shop_name ?: $user->name }}"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-gray-500">
                                            {{ $sellerInitial }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-3xl font-bold text-gray-900">
                                        {{ $sellerProfile->shop_name ?: $user->name }}
                                    </h3>

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Seller verificat
                                    </span>
                                </div>

                                @if($canShowFollowButton)
                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                        @if($isFollowingSeller)
                                            <form method="POST" action="{{ route('seller.follow.destroy', $user) }}">
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
                                            <form method="POST" action="{{ route('seller.follow.store', $user) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black"
                                                >
                                                    Urmareste sellerul
                                                </button>
                                            </form>
                                        @endif

                                        @if(auth()->check() && \App\Models\User::supportsMessaging() && auth()->id() !== $user->id && auth()->user()->role !== 'seller')
                                            <form method="POST" action="{{ route('messages.start_seller', $user) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                                                >
                                                    Trimite mesaj
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <div class="text-yellow-500 text-lg">
                                        {{ str_repeat('★', (int) round($sellerAvgRating)) }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $sellerAvgRating }} din 5 ({{ $sellerReviewsCount }} review-uri)
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $user->followers_count ?? 0 }} urmaritori
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
                        </div>

                        <div class="grid min-w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4 lg:min-w-[420px]">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Produse publice</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['products_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">In stoc</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['in_stock_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Promo</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['promo_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Followers</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $user->followers_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="market-section rounded-xl border border-gray-100 bg-white p-6 shadow">
                    <h3 class="text-lg font-semibold text-gray-900">Date seller</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-semibold text-gray-900">Magazin:</span>
                            {{ $sellerProfile->shop_name ?: '—' }}
                        </div>

                        @if($sellerProfile->phone)
                            <div>
                                <span class="font-semibold text-gray-900">Telefon:</span>
                                {{ $sellerProfile->phone }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="market-section rounded-xl border border-gray-100 bg-white p-6 shadow">
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

                <div class="market-section rounded-xl border border-gray-100 bg-white p-6 shadow">
                    <h3 class="text-lg font-semibold text-gray-900">Cauta in produsele sellerului</h3>

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
                            <option value="price_asc" @selected($sort === 'price_asc')>Pret crescator</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Pret descrescator</option>
                        </select>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-gray-900 px-4 py-3 font-semibold text-white hover:bg-black"
                        >
                            Aplica
                        </button>
                    </form>
                </div>
            </div>

            <div class="market-section rounded-xl border border-gray-100 bg-white p-6 shadow">
                <h3 class="text-lg font-semibold text-gray-900">Review-uri seller</h3>
                <p class="text-sm text-gray-500">Doar cumparatorii reali pot lasa review sellerului.</p>

                @auth
                    @if($canReviewSeller)
                        <form method="POST" action="{{ route('seller.reviews.store', $user) }}" class="mt-5 space-y-4">
                            @csrf

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Rating</label>
                                <select name="rating" class="w-full rounded-xl border p-3">
                                    <option value="">Alege rating</option>
                                    <option value="5" @selected(old('rating', $mySellerReview?->rating) == 5)>5 stele</option>
                                    <option value="4" @selected(old('rating', $mySellerReview?->rating) == 4)>4 stele</option>
                                    <option value="3" @selected(old('rating', $mySellerReview?->rating) == 3)>3 stele</option>
                                    <option value="2" @selected(old('rating', $mySellerReview?->rating) == 2)>2 stele</option>
                                    <option value="1" @selected(old('rating', $mySellerReview?->rating) == 1)>1 stea</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Comentariu</label>
                                <textarea name="comment" rows="4" class="w-full rounded-xl border p-3" placeholder="Spune parerea ta despre seller...">{{ old('comment', $mySellerReview?->comment) }}</textarea>
                            </div>

                            <button class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                                {{ $mySellerReview ? 'Actualizeaza review-ul' : 'Trimite review-ul' }}
                            </button>
                        </form>
                    @else
                        <div class="mt-5 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800">
                            Poti lasa review sellerului doar daca ai cumparat si achitat de la el.
                        </div>
                    @endif
                @else
                    <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-700">
                        Pentru review trebuie sa fii logat si sa fi cumparat de la acest seller.
                    </div>
                @endauth

                <div class="mt-8 space-y-5">
                    @forelse($sellerReviews as $review)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                    <div class="text-sm text-yellow-500">{{ str_repeat('★', (int) $review->rating) }}</div>
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
                            Nu exista review-uri inca pentru acest seller.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="market-section rounded-xl border border-gray-100 bg-white p-6 shadow">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Produsele sellerului</h3>
                        <p class="text-sm text-gray-500">
                            {{ $products->total() }} produse gasite
                        </p>
                    </div>

                    @if($q !== '')
                        <a href="{{ route('seller.public.show', $user) }}"
                           class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Reseteaza filtrul
                        </a>
                    @endif
                </div>

                @if($products->isEmpty())
                    <div class="mt-5 rounded-xl border border-dashed border-gray-200 p-10 text-center text-gray-600">
                        Sellerul nu are produse publice pentru filtrul selectat.
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
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

        <div
            x-cloak
            x-show="isOpen"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 px-4 py-6"
            x-on:keydown.escape.window="closeStory()"
        >
            <div class="relative flex h-full max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-[2rem] bg-neutral-950 text-white shadow-2xl">
                <div class="absolute inset-x-0 top-0 z-20 px-4 pt-4">
                    <div class="flex gap-1.5">
                        <template x-for="(item, index) in timeline" :key="item.id">
                            <div class="h-1 flex-1 overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-white transition-all duration-300" :style="progressStyle(index)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <a class="flex items-center gap-3" :href="currentSellerUrl()">
                            <div class="h-11 w-11 overflow-hidden rounded-full bg-white/10 ring-2 ring-white/40">
                                <template x-if="isOpen && currentItem() && currentItem().seller_avatar">
                                    <img :src="currentItem().seller_avatar" :alt="currentItem().seller_name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!isOpen || !currentItem() || !currentItem().seller_avatar">
                                    <div class="flex h-full w-full items-center justify-center text-sm font-semibold" x-text="currentItem() ? currentItem().seller_initial : 'S'"></div>
                                </template>
                            </div>
                            <div>
                                <div class="text-sm font-semibold" x-text="currentItem() ? currentItem().seller_name : ''"></div>
                                <div class="text-xs text-white/70" x-text="currentItem() && currentItem().expires_at ? `Expira ${currentItem().expires_at}` : ''"></div>
                            </div>
                        </a>

                        <button type="button" x-on:click="closeStory()" class="rounded-full bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/80 hover:bg-white/20">
                            Inchide
                        </button>
                    </div>
                </div>

                <div class="relative flex-1 bg-black">
                    <button type="button" x-on:click="prev()" class="absolute inset-y-0 left-0 z-10 w-1/3" aria-label="Story precedent"></button>
                    <button type="button" x-on:click="next()" class="absolute inset-y-0 right-0 z-10 w-1/3" aria-label="Story urmator"></button>

                    <div class="flex h-full items-center justify-center pt-24">
                        <template x-if="isOpen && currentItem() && currentItem().media_type === 'video'">
                            <div class="flex h-full w-full items-center justify-center">
                                <video
                                    x-ref="storyVideo"
                                    :src="currentItem().media_url"
                                    class="h-full w-full object-contain"
                                    playsinline
                                    controls
                                    autoplay
                                    x-on:loadedmetadata="prepareVideo()"
                                    x-on:timeupdate="updateVideoProgress()"
                                    x-on:ended="next()"
                                    x-on:error="videoError = true"
                                ></video>

                                <template x-if="videoError">
                                    <div class="absolute inset-x-6 bottom-6 rounded-3xl border border-white/10 bg-black/70 p-6 text-center backdrop-blur">
                                        <div class="text-lg font-semibold text-white">Acest video nu se poate reda direct in browser.</div>
                                        <div class="mt-3 text-sm leading-6 text-white/70">
                                            Formatul incarcat din telefon nu este redat corect aici. Il poti descarca sau deschide direct.
                                        </div>
                                        <a :href="currentItem().media_url" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex items-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-gray-900">
                                            Deschide video-ul
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="isOpen && currentItem() && currentItem().media_type !== 'video'">
                            <img :src="currentItem().media_url" :alt="sellerName" class="h-full w-full object-contain">
                        </template>
                    </div>
                </div>

                <div class="border-t border-white/10 px-4 py-4 space-y-4">
                    <div class="min-h-[2.5rem] text-sm leading-6 text-white/90" x-text="currentItem()?.caption || 'Story activ de la seller.'"></div>

                    <div
                        x-cloak
                        x-show="toastMessage"
                        class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm text-white/90"
                        x-text="toastMessage"
                    ></div>

                    <div class="flex items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <input
                                type="text"
                                x-model="storyMessageText"
                                x-on:keydown.enter.prevent="sendStoryMessage()"
                                class="w-full rounded-full border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/20 focus:outline-none focus:ring-0"
                                placeholder="Scrie ceva sellerului despre acest story..."
                            >
                        </div>

                        <button
                            type="button"
                            x-on:click="toggleStoryLike()"
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20"
                            :class="currentItem() && currentItem().is_liked ? 'text-pink-400' : 'text-white'"
                            :disabled="storyLikeBusy"
                            aria-label="Apreciaza story"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" :fill="currentItem() && currentItem().is_liked ? 'currentColor' : 'none'" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            x-on:click="sendStoryMessage()"
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-gray-900 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="storyMessageBusy || !storyMessageText.trim()"
                            aria-label="Trimite mesaj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
