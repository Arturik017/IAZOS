<x-app-layout>
    @php
        $recommendedProducts = collect($recommendedProducts ?? $promoProducts ?? [])->take(50);
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-8 lg:py-10">
        <div class="market-shell px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-12 gap-6 xl:gap-8">

                {{-- LEFT SIDEBAR --}}
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

                {{-- MAIN --}}
                <main class="col-span-12 space-y-8 lg:col-span-10">

                    {{-- TOP HERO --}}
                    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1.65fr_0.75fr]">

                        {{-- BANNER --}}
                        <section
                            x-data="bannerCarousel()"
                            x-init="init()"
                            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                        >
                            <div class="p-4 sm:p-5">
                                <div class="relative overflow-hidden rounded-lg bg-gray-100">
                                    <div class="relative h-[280px] sm:h-[360px] lg:h-[440px]">
                                        <template x-for="(b, i) in banners" :key="i">
                                            <div
                                                x-show="active === i"
                                                x-transition.opacity.duration.500ms
                                                class="absolute inset-0"
                                            >
                                                <template x-if="b.image">
                                                    <img
                                                        :src="b.image"
                                                        class="h-full w-full object-cover banner-zoom"
                                                        alt="Banner"
                                                    />
                                                </template>

                                                <template x-if="!b.image">
                                                    <div class="h-full w-full bg-gray-100"></div>
                                                </template>
                                            </div>
                                        </template>

                                        <button
                                            type="button"
                                            @click="prev()"
                                            class="hero-arrow absolute left-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full"
                                            aria-label="Previous"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click="next()"
                                            class="hero-arrow absolute right-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full"
                                            aria-label="Next"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>

                                        <div class="absolute bottom-4 left-0 right-0 z-20 flex justify-center gap-2">
                                            <template x-for="(b, i) in banners" :key="'dot'+i">
                                                <button
                                                    type="button"
                                                    class="hero-dot h-2.5 rounded-full transition-all duration-300"
                                                    :class="active === i ? 'w-10' : 'w-2.5'"
                                                    @click="go(i)"
                                                    aria-label="Go to slide"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- SELLER CARD --}}
                        <section class="rounded-xl border border-gray-200 bg-white p-6">
                            <div class="max-w-sm">
                                <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">
                                    Seller zone
                                </div>

                                <h2 class="mt-5 text-3xl font-semibold leading-tight text-gray-900">
                                    Vinde pe IAZOS
                                </h2>

                                <p class="mt-4 text-sm leading-7 text-gray-600">
                                    Intră în marketplace cu un spațiu premium pentru produse și o experiență construită pentru claritate.
                                </p>

                                <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 px-4 py-4">
                                    <div class="text-[10px] uppercase tracking-[0.20em] text-gray-500">Marketplace</div>
                                    <div class="mt-2 text-sm font-medium text-gray-900">
                                        Aplicare rapidă, prezență premium și un loc pregătit pentru creștere.
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
                                    <a
                                        href="{{ route('seller.application.create') }}"
                                        class="inline-flex w-full items-center justify-center rounded-full bg-gray-900 px-6 py-4 text-sm font-medium text-white"
                                    >
                                        Aplică ca seller
                                    </a>
                                </div>
                            </div>
                        </section>
                    </section>

                    {{-- SEPARATOR --}}
                    <section class="market-separator rounded-lg py-4">
                        <div class="market-separator-mask">
                            <div class="market-separator-track">
                                <span>RECOMMENDED PRODUCTS • PREMIUM SELECTION • DISCOVER MORE • IAZOS MARKETPLACE •</span>
                                <span>RECOMMENDED PRODUCTS • PREMIUM SELECTION • DISCOVER MORE • IAZOS MARKETPLACE •</span>
                            </div>
                        </div>
                    </section>

                    {{-- RECOMMENDED --}}
                    <section id="recommended-products" class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-3xl">
                                <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">
                                    Recomandări
                                </div>

                                <h2 class="mt-3 text-3xl font-semibold leading-tight text-gray-900">
                                    Recomandate pentru tine
                                </h2>

                                <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">
                                    Un grid mare de produse pentru browsing real de marketplace, clar și ușor de parcurs.
                                </p>
                            </div>
                        </div>

                        @if($recommendedProducts->isEmpty())
                            <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-10 text-center text-sm text-gray-500">
                                Nu există produse disponibile momentan.
                            </div>
                        @else
                            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                                @foreach($recommendedProducts as $product)
                                    @include('shop.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        @endif
                    </section>

                </main>
            </div>
        </div>
    </div>

    <script>
        function bannerCarousel() {
            return {
                active: 0,
                timer: null,
                banners: @json($banners),

                init() {
                    if (!this.banners || this.banners.length === 0) {
                        this.banners = [{
                            image: null,
                            title: 'Banner',
                            subtitle: '',
                            kicker: 'IAZOS'
                        }];
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
    </script>
</x-app-layout>