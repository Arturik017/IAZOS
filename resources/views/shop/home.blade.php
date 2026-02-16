<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">

            <div class="grid grid-cols-12 gap-6">

                {{-- LEFT SIDEBAR --}}
                <aside class="col-span-12 lg:col-span-3" x-data="{ openCats: false }">
                
                    {{-- MOBILE TOGGLE (doar pe mobil) --}}
                    <div class="">
                        <button
                            type="button"
                            @click="openCats = !openCats"
                            class="w-full lg:hidden flex items-center justify-between px-4 py-3 rounded-2xl bg-white shadow border border-gray-100"
                        >
                            <span class="font-semibold text-gray-900">Categorii</span>
                
                            {{-- Arrow DOAR pe mobil --}}
                            <!--<span class="text-gray-500" x-text="openCats ? '▲' : '▼'"></span>-->
                        </button>
                    </div>
                
                    {{-- SIDEBAR CONTENT (UN SINGUR INCLUDE) --}}
                    <div
                        class="mt-3 lg:mt-0 lg:sticky lg:top-6 lg:block"
                        :class="openCats ? 'block' : 'hidden'"
                    >
                        @include('shop.partials.sidebar')
                    </div>
                
                </aside>





                {{-- MAIN CONTENT --}}
                <main class="col-span-12 lg:col-span-9 space-y-8">

                    {{-- 1 BANNER CAROUSEL --}}
                    <section
                        x-data="bannerCarousel()"
                        x-init="init()"
                        class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden"
                    >
                        <div class="relative">

                            {{-- Slides --}}
                            <div class="relative h-[220px] sm:h-[280px] lg:h-[320px]">
                                <template x-for="(b, i) in banners" :key="i">
                                    <div
                                        x-show="active === i"
                                        x-transition.opacity.duration.400ms
                                        class="absolute inset-0"
                                    >
                                        {{-- dacă ai imagine în storage: --}}
                                        <template x-if="b.image">
                                            <img
                                                :src="b.image"
                                                class="h-full w-full object-cover"
                                                alt="Banner"
                                            />
                                        </template>

                                        {{-- fallback gradient dacă nu e imagine --}}
                                        <template x-if="!b.image">
                                            <div class="h-full w-full bg-gradient-to-r from-gray-900 to-gray-700"></div>
                                        </template>

                                        {{-- overlay text --}}
                                        <div class="absolute inset-0 bg-black/35"></div>
                                        <div class="absolute inset-0 p-6 sm:p-10 flex items-end">
                                            <div class="max-w-2xl">
                                                <div class="text-white/80 text-sm" x-text="b.kicker ?? ''"></div>
                                                <div class="text-white text-2xl sm:text-3xl font-extrabold leading-tight" x-text="b.title ?? 'Promoții & Oferte'"></div>
                                                <div class="mt-2 text-white/80 text-sm sm:text-base" x-text="b.subtitle ?? 'Urmărește ofertele curente.'"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- arrows --}}
                                <button
                                    type="button"
                                    @click="prev()"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-900 rounded-full w-10 h-10 flex items-center justify-center shadow"
                                    aria-label="Previous"
                                >
                                    ‹
                                </button>
                                <button
                                    type="button"
                                    @click="next()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-900 rounded-full w-10 h-10 flex items-center justify-center shadow"
                                    aria-label="Next"
                                >
                                    ›
                                </button>

                                {{-- dots --}}
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
                                    <template x-for="(b, i) in banners" :key="'dot'+i">
                                        <button
                                            type="button"
                                            class="w-2.5 h-2.5 rounded-full"
                                            :class="active === i ? 'bg-white' : 'bg-white/50 hover:bg-white/80'"
                                            @click="go(i)"
                                            aria-label="Go to slide"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </section>

                    {{-- 2 PROMO PRODUCTS CAROUSEL --}}
                    <section class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Promoții</h3>
                                <p class="text-sm text-gray-500">Doar produsele bifate ca promoție.</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50"
                                    onclick="document.getElementById('promoTrack').scrollBy({left: -420, behavior: 'smooth'})">
                                    ←
                                </button>
                                <button type="button" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50"
                                    onclick="document.getElementById('promoTrack').scrollBy({left: 420, behavior: 'smooth'})">
                                    →
                                </button>
                            </div>
                        </div>

                        @if($promoProducts->isEmpty())
                            <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                                Nu există promoții momentan.
                            </div>
                        @else
                            <div
                                id="promoTrack"
                                class="mt-5 flex gap-4 overflow-x-auto pb-2 scroll-smooth"
                                style="scroll-snap-type: x mandatory;"
                            >
                                @foreach($promoProducts as $product)
                                    <div
                                        class="min-w-[260px] sm:min-w-[300px] max-w-[300px]"
                                        style="scroll-snap-align: start;"
                                    >
                                        @include('shop.partials.product-card', ['product' => $product])
                                    </div>
                                @endforeach

                            </div>

                            <div class="mt-2 text-xs text-gray-400">
                                * Poți derula și cu scroll-ul pe orizontală.
                            </div>
                        @endif
                    </section>

                    {{-- 3 CONTACT FORM --}}
                    <section class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Scrie-ne un mesaj</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            (Deocamdată e doar UI. La pasul următor îl facem să trimită în DB/email.)
                        </p>

                        <form class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nume</label>
                                <input type="text" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200" placeholder="Numele tău">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200" placeholder="email@exemplu.com">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Mesaj</label>
                                <textarea rows="4" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200" placeholder="Cu ce te putem ajuta?"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <button type="button"
                                    class="w-full sm:w-auto px-6 py-3 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black transition">
                                    Trimite
                                </button>
                            </div>
                        </form>
                    </section>

                    {{-- 4 FOOTER --}}
                    <footer class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                {{-- Instagram --}}
                                <a href="#" class="w-10 h-10 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center" aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4v10a4 4 0 004 4z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 16a4 4 0 100-8 4 4 0 000 8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.5 6.5h.01"/>
                                    </svg>
                                </a>

                                {{-- TikTok (icon simplificat) --}}
                                <a href="#" class="w-10 h-10 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center" aria-label="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M16 3c.3 2.1 1.9 3.8 4 4.1V11c-1.6-.1-3.1-.7-4-1.6v6.6c0 3.3-2.7 6-6 6s-6-2.7-6-6 2.7-6 6-6c.3 0 .7 0 1 .1V14c-.3-.2-.6-.3-1-.3-1.3 0-2.3 1-2.3 2.3S8.7 18.3 10 18.3s2.3-1 2.3-2.3V3h3.7z"/>
                                    </svg>
                                </a>

                                {{-- Facebook --}}
                                <a href="#" class="w-10 h-10 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center" aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.2c0-.9.2-1.5 1.6-1.5H17V5c-.3 0-1.5-.1-2.9-.1-2.8 0-4.6 1.7-4.6 4.8V11H7v3h2.5v8h4z"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                                <a href="#" class="text-gray-600 hover:text-gray-900">About Us</a>
                                <a href="#" class="text-gray-600 hover:text-gray-900">Contacts</a>
                                <a href="#" class="text-gray-600 hover:text-gray-900">Catalog</a>
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-gray-400">
                            © {{ date('Y') }} TECHY'S. Toate drepturile rezervate.
                        </div>
                    </footer>

                </main>
            </div>
        </div>
    </div>

    {{-- Alpine helper pentru carusel bannere --}}
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
                            title: 'Promoții & Oferte',
                            subtitle: 'Urmărește ofertele curente.',
                            kicker: 'Magazin'
                        }];
                    }
                    this.start();
                },

                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 4500);
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
                }
            }
        }
    </script>

</x-app-layout>
