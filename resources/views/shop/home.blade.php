<x-app-layout>
    @php
        $recommendedProducts = collect($recommendedProducts ?? $promoProducts ?? [])->take(50);
        $promoCollection = collect($promoProducts ?? [])->take(12);
        $newCollection = collect($recommendedProducts ?? $promoProducts ?? [])->sortByDesc('id')->take(12);
        $featuredCategories = collect($categories ?? [])->take(8);
        $featuredSellers = collect($sellerStories ?? [])->take(8);
        $heroBanners = [[
            'image' => asset('images/banners/marketplace-hero-premium.png'),
            'title' => 'Marketplace premium',
            'subtitle' => '',
            'kicker' => 'IAZOS',
        ]];
        $recommendedTitle = $recommendedTitle ?? 'Recomandate pentru tine';
        $recommendedSubtitle = $recommendedSubtitle ?? 'Un grid mare de produse pentru browsing real de marketplace, clar si usor de parcurs.';
        $popularBrandItems = collect($popularBrands ?? [])->map(function ($brand) {
            return [
                'label' => $brand['label'],
                'slug' => $brand['slug'],
                'count' => $brand['count'],
                'logo_url' => $brand['logo_url'] ?? null,
                'url' => route('brand.show', $brand['slug']),
            ];
        })->values();
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="market-page py-8 lg:py-10">
        <div class="market-shell px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-12 gap-6 xl:gap-8">
                <aside class="col-span-12 lg:col-span-2" x-data="{ openCats: false }">
                    <button
                        type="button"
                        @click="openCats = !openCats"
                        class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-4 lg:hidden"
                    >
                        <div class="text-left">
                            <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">Navigare</div>
                            <div class="mt-1 text-sm font-medium text-gray-900">Categorii produse</div>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 transition" :class="openCats ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="mt-3 lg:mt-0 lg:sticky lg:top-24 lg:block" :class="openCats ? 'block' : 'hidden lg:block'">
                        @include('shop.partials.sidebar')
                    </div>
                </aside>

                <main class="col-span-12 space-y-8 lg:col-span-10">
                    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1.65fr_0.75fr]">
                        <section x-data="bannerCarousel()" x-init="init()" class="market-section overflow-hidden rounded-xl border border-gray-200 bg-white">
                            <div class="p-4 sm:p-5">
                                <div class="relative overflow-hidden rounded-xl bg-[#12082b]">
                                    <div class="relative min-h-[220px] sm:min-h-[320px] lg:min-h-[430px]" style="aspect-ratio: 1792 / 928;">
                                        <template x-for="(b, i) in banners" :key="i">
                                            <div x-show="active === i" x-transition.opacity.duration.500ms class="absolute inset-0">
                                                <template x-if="b.image">
                                                    <img :src="b.image" class="h-full w-full object-cover" alt="Banner" />
                                                </template>

                                                <template x-if="!b.image">
                                                    <div class="h-full w-full bg-gray-100"></div>
                                                </template>
                                            </div>
                                        </template>

                                        <button type="button" x-show="banners.length > 1" @click="prev()" class="hero-arrow absolute left-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full" aria-label="Previous">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>

                                        <button type="button" x-show="banners.length > 1" @click="next()" class="hero-arrow absolute right-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full" aria-label="Next">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>

                                        <div x-show="banners.length > 1" class="absolute bottom-4 left-0 right-0 z-20 flex justify-center gap-2">
                                            <template x-for="(b, i) in banners" :key="'dot'+i">
                                                <button type="button" class="hero-dot h-2.5 rounded-full transition-all duration-300" :class="active === i ? 'w-10' : 'w-2.5'" @click="go(i)" aria-label="Go to slide"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="market-section rounded-xl border border-gray-200 bg-white p-6">
                            <div class="max-w-sm">
                                <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">Seller zone</div>

                                <h2 class="mt-5 text-3xl font-semibold leading-tight text-gray-900">Vinde pe IAZOS</h2>

                                <p class="mt-4 text-sm leading-7 text-gray-600">
                                    Intra in marketplace cu un spatiu premium pentru produse si o experienta construita pentru claritate.
                                </p>

                                <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 px-4 py-4">
                                    <div class="text-[10px] uppercase tracking-[0.20em] text-gray-500">Marketplace</div>
                                    <div class="mt-2 text-sm font-medium text-gray-900">
                                        Aplicare rapida, prezenta premium si un loc pregatit pentru crestere.
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-3 gap-3">
                                    <div class="rounded-lg border border-gray-200 bg-white px-3 py-4 text-center">
                                        <div class="text-base font-semibold text-gray-900">Fast</div>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.18em] text-gray-500">Setup</div>
                                    </div>
                                    <div class="rounded-lg border border-gray-200 bg-white px-3 py-4 text-center">
                                        <div class="text-base font-semibold text-gray-900">Secure</div>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.18em] text-gray-500">Flow</div>
                                    </div>
                                    <div class="rounded-lg border border-gray-200 bg-white px-3 py-4 text-center">
                                        <div class="text-base font-semibold text-gray-900">Grow</div>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.18em] text-gray-500">Sales</div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <a href="{{ route('seller.application.create') }}" class="inline-flex w-full items-center justify-center rounded-full bg-gray-900 px-6 py-4 text-sm font-medium text-white">
                                        Aplica ca seller
                                    </a>
                                </div>
                            </div>
                        </section>
                    </section>

                    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="market-trust-card p-5">
                            <div class="market-icon-tile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 3v5c0 4.5-2.9 8.4-7 10-4.1-1.6-7-5.5-7-10V6l7-3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 12 2 2 4-5" />
                                </svg>
                            </div>
                            <div class="mt-4 text-base font-bold text-gray-900">Selleri verificati</div>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Magazine aprobate, profiluri clare si produse moderate pentru o experienta serioasa.</p>
                        </div>

                        <div class="market-trust-card p-5">
                            <div class="market-icon-tile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 10h10M7 14h6M5 20l-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5Z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-base font-bold text-gray-900">Review-uri reale</div>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Rating-ul ramane vizibil, curat si separat de identitatea violet premium.</p>
                        </div>

                        <div class="market-trust-card p-5">
                            <div class="market-icon-tile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M6 11h5M7 17h10a3 3 0 0 0 3-3V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a3 3 0 0 0 3 3Z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-base font-bold text-gray-900">Checkout clar</div>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Actiunile importante sunt evidentiate fara zgomot vizual inutil.</p>
                        </div>

                        <div class="market-trust-card p-5">
                            <div class="market-icon-tile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-base font-bold text-gray-900">Produse proaspete</div>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Sectiuni gandite pentru browsing rapid, comparatie si descoperire.</p>
                        </div>
                    </section>

                    @if($featuredCategories->isNotEmpty())
                        <section class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                                <div class="market-section-header">
                                    <div class="market-eyebrow">Categorii populare</div>
                                    <h2 class="text-2xl font-semibold leading-tight text-gray-900">Navigare rapida, fara meniu incarcat</h2>
                                    <p class="max-w-2xl text-sm leading-7 text-gray-600">Categorii principale prezentate ca zone vizuale clare, pastrand sidebar-ul simplu si eficient.</p>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 2xl:grid-cols-8">
                                @foreach($featuredCategories as $categoryItem)
                                    <a href="{{ route('category.show', $categoryItem) }}" class="market-card group rounded-xl border border-gray-200 bg-gray-50 p-4 transition hover:bg-white">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#4d01a6] shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h7v7H4V6Zm9 0h7v7h-7V6ZM4 15h7v3H4v-3Zm9 0h7v3h-7v-3Z" />
                                            </svg>
                                        </div>
                                        <div class="mt-4 line-clamp-2 min-h-[40px] text-sm font-bold text-gray-900 group-hover:text-[#33004a]">
                                            {{ $categoryItem->name }}
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">
                                            {{ $categoryItem->childrenRecursive?->count() ?? 0 }} subcategorii
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($featuredSellers->isNotEmpty())
                        <section class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                                <div class="market-section-header">
                                    <div class="market-eyebrow">Selleri activi</div>
                                    <h2 class="text-2xl font-semibold leading-tight text-gray-900">Magazine cu prezenta vizuala puternica</h2>
                                    <p class="max-w-2xl text-sm leading-7 text-gray-600">Selleri cu story-uri active, util pentru incredere, ritm si senzatia de marketplace viu.</p>
                                </div>

                                <a href="{{ route('sellers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-900 transition hover:bg-gray-50">
                                    Vezi sellerii
                                </a>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                @foreach($featuredSellers as $sellerItem)
                                    <a href="{{ $sellerItem['seller_url'] }}" class="market-card group rounded-xl border border-gray-200 bg-gray-50 p-5 transition hover:bg-white">
                                        <div class="flex items-start gap-4">
                                            <div class="story-ring h-14 w-14 shrink-0 rounded-full bg-gradient-to-br from-[#220138] via-[#4d01a6] to-[#066e97] p-[3px]">
                                                <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-white">
                                                    @if(!empty($sellerItem['seller_avatar']))
                                                        <img src="{{ $sellerItem['seller_avatar'] }}" alt="{{ $sellerItem['seller_name'] }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full w-full items-center justify-center text-lg font-bold text-[#33004a]">
                                                            {{ strtoupper(mb_substr($sellerItem['seller_name'], 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="min-w-0">
                                                <div class="line-clamp-1 text-base font-bold text-gray-900 group-hover:text-[#33004a]">
                                                    {{ $sellerItem['seller_name'] }}
                                                </div>
                                                <div class="mt-1 text-sm text-gray-500">
                                                    {{ $sellerItem['stories_count'] }} story-uri active
                                                </div>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    <span class="market-chip market-chip-cool">Activ acum</span>
                                                    @if($sellerItem['is_followed_priority'])
                                                        <span class="market-chip">Urmarit</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($promoCollection->isNotEmpty())
                        <section class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                            <div class="market-section-header">
                                <div class="market-eyebrow">Promotii active</div>
                                <h2 class="text-2xl font-semibold leading-tight text-gray-900">Produse cu accent de pret</h2>
                                <p class="max-w-2xl text-sm leading-7 text-gray-600">O selectie compacta pentru vizitatori care cauta rapid oportunitati bune.</p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6">
                                @foreach($promoCollection as $product)
                                    @include('shop.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section x-data="brandCarousel()" x-init="init()" class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">Branduri populare</div>
                                <h2 class="mt-3 text-2xl font-semibold leading-tight text-gray-900">Exploreaza rapid brandurile cautate</h2>
                                <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600">Click pe un brand si mergi direct in pagina dedicata cu produsele disponibile.</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="prev()" class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button type="button" @click="next()" class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 overflow-hidden">
                            <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${activePage * 100}%);`">
                                <template x-for="(page, pageIndex) in pages" :key="pageIndex">
                                    <div class="min-w-full">
                                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
                                            <template x-for="brand in page" :key="brand.slug">
                                                <a :href="brand.url" class="market-card group rounded-xl border border-gray-200 bg-gray-50 px-4 py-5 transition hover:-translate-y-0.5 hover:border-gray-300 hover:bg-white">
                                                    <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Brand</div>
                                                    <div class="mt-3 flex h-12 items-center justify-center rounded-xl bg-white px-3">
                                                        <template x-if="brand.logo_url">
                                                            <img :src="brand.logo_url" :alt="brand.label" class="max-h-8 w-auto object-contain">
                                                        </template>
                                                        <template x-if="!brand.logo_url">
                                                            <div class="text-sm font-semibold text-gray-900" x-text="brand.label"></div>
                                                        </template>
                                                    </div>
                                                    <div class="mt-3 text-lg font-semibold text-gray-900" x-text="brand.label"></div>
                                                    <div class="mt-2 text-xs text-gray-500" x-text="`${brand.count} produse`"></div>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-center gap-2" x-show="pages.length > 1">
                            <template x-for="(_, pageIndex) in pages" :key="`brand-dot-${pageIndex}`">
                                <button type="button" @click="go(pageIndex)" class="h-2.5 rounded-full transition-all duration-300" :class="activePage === pageIndex ? 'w-8 bg-gray-900' : 'w-2.5 bg-gray-300'"></button>
                            </template>
                        </div>
                    </section>

                    <section id="recommended-products" class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-3xl">
                                <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">Recomandari</div>
                                <h2 class="mt-3 text-3xl font-semibold leading-tight text-gray-900">{{ $recommendedTitle }}</h2>
                                <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">{{ $recommendedSubtitle }}</p>
                            </div>
                        </div>

                        @if($recommendedProducts->isEmpty())
                            <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-10 text-center text-sm text-gray-500">
                                Nu exista produse disponibile momentan.
                            </div>
                        @else
                            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6">
                                @foreach($recommendedProducts as $product)
                                    @include('shop.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if($newCollection->isNotEmpty())
                        <section class="market-section rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                            <div class="market-section-header">
                                <div class="market-eyebrow">Produse noi</div>
                                <h2 class="text-2xl font-semibold leading-tight text-gray-900">Cele mai recente aparitii</h2>
                                <p class="max-w-2xl text-sm leading-7 text-gray-600">Un modul final de browsing pentru utilizatori care vor sa vada rapid ce s-a adaugat recent.</p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6">
                                @foreach($newCollection as $product)
                                    @include('shop.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </div>

    <script>
        function bannerCarousel() {
            return {
                active: 0,
                timer: null,
                banners: @json($heroBanners),

                init() {
                    if (!this.banners || this.banners.length === 0) {
                        this.banners = [{ image: null, title: 'Banner', subtitle: '', kicker: 'IAZOS' }];
                    }
                    this.start();
                },

                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 5000);
                },

                stop() {
                    if (this.timer) clearInterval(this.timer);
                    this.timer = null;
                },

                next() {
                    this.active = (this.active + 1) % this.banners.length;
                },

                prev() {
                    this.active = (this.active - 1 + this.banners.length) % this.banners.length;
                },

                go(i) {
                    this.active = i;
                    this.start();
                }
            }
        }

        function brandCarousel() {
            return {
                items: @json($popularBrandItems),
                pages: [],
                activePage: 0,
                perPage: window.innerWidth >= 1280 ? 6 : (window.innerWidth >= 768 ? 3 : 2),

                init() {
                    this.buildPages();
                    window.addEventListener('resize', () => {
                        const nextPerPage = window.innerWidth >= 1280 ? 6 : (window.innerWidth >= 768 ? 3 : 2);
                        if (nextPerPage !== this.perPage) {
                            this.perPage = nextPerPage;
                            this.buildPages();
                        }
                    });
                },

                buildPages() {
                    const chunked = [];
                    for (let i = 0; i < this.items.length; i += this.perPage) {
                        chunked.push(this.items.slice(i, i + this.perPage));
                    }
                    this.pages = chunked.length ? chunked : [[]];
                    this.activePage = 0;
                },

                next() {
                    if (this.pages.length <= 1) return;
                    this.activePage = (this.activePage + 1) % this.pages.length;
                },

                prev() {
                    if (this.pages.length <= 1) return;
                    this.activePage = (this.activePage - 1 + this.pages.length) % this.pages.length;
                },

                go(index) {
                    this.activePage = index;
                }
            }
        }
    </script>
</x-app-layout>
