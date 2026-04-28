<nav x-data="{ open: false }">
    <div class="market-shell px-4 sm:px-6 lg:px-8">
        <div class="flex h-22 items-center justify-between gap-6 py-3">

            <div class="flex min-w-0 items-center gap-8">
                <a href="{{ route('home') }}" class="shrink-0 flex items-center">
                    <img
                        src="{{ asset('favicon1.png') }}"
                        alt="IAZOS"
                        class="h-10 w-auto object-contain"
                    >
                </a>

                <div class="hidden lg:flex lg:items-center lg:gap-9">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Acasa
                    </x-nav-link>

                    <x-nav-link :href="route('sellers.index')" :active="request()->routeIs('sellers.*')">
                        Selleri
                    </x-nav-link>

                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                        Despre noi
                    </x-nav-link>

                    <x-nav-link :href="route('terms')" :active="request()->routeIs('terms')">
                        Termeni
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden min-w-0 flex-1 lg:flex lg:justify-center">
                <form action="{{ route('search') }}" method="GET" class="w-full max-w-[640px]">
                    <div class="flex items-center rounded-xl border border-gray-300 bg-white p-1.5 shadow-sm transition focus-within:border-[#4d01a6] focus-within:shadow-[0_0_0_4px_rgba(77,1,166,0.10)]">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cauta produse..."
                            class="w-full border-0 bg-transparent px-5 py-3 text-sm shadow-none focus:ring-0"
                        >
                        <button
                            type="submit"
                            class="shrink-0 rounded-xl bg-gray-900 px-7 py-3 text-sm font-semibold text-white"
                        >
                            Cauta
                        </button>
                    </div>
                </form>
            </div>

            <div class="flex shrink-0 items-center gap-3">
                @auth
                    @if(\App\Models\User::supportsMessaging())
                        <a href="{{ route('messages.index') }}" class="relative hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5M6 20l-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8l-2 2Z" />
                            </svg>
                            Mesaje
                            @if(($messageUnreadCount ?? 0) > 0)
                                <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                    {{ $messageUnreadCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <a href="{{ route('orders.index') }}" class="hidden sm:inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        Comenzile mele
                    </a>

                    <a href="{{ route('wishlist.index') }}" class="relative hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                        </svg>
                        Favorite
                        @if(($wishlistCount ?? 0) > 0)
                            <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                {{ $wishlistCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('cart.index') }}" class="relative hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        Cos
                        @if(($cartCount ?? 0) > 0)
                            <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->role === 'admin')
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    Admin
                                </x-dropdown-link>
                            @endif

                            @if(Auth::user()->role === 'seller')
                                <x-dropdown-link :href="route('seller.dashboard')">
                                    Dashboard seller
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('wishlist.index') }}" class="relative hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                        </svg>
                        Favorite
                        @if(($wishlistCount ?? 0) > 0)
                            <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                {{ $wishlistCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('cart.index') }}" class="relative hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        Cos
                        @if(($cartCount ?? 0) > 0)
                            <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50">
                        Logare
                    </a>

                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white">
                        Inregistrare
                    </a>
                @endauth

                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white p-3 text-gray-900 lg:hidden">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-gray-200 bg-white lg:hidden">
        <div class="space-y-1 px-4 py-4">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Acasa
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('sellers.index')" :active="request()->routeIs('sellers.*')">
                Selleri
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                Despre noi
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('terms')" :active="request()->routeIs('terms')">
                Termeni
            </x-responsive-nav-link>

            @auth
                @if(\App\Models\User::supportsMessaging())
                    <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                        Mesaje @if(($messageUnreadCount ?? 0) > 0) ({{ $messageUnreadCount }}) @endif
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    Comenzile mele
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">
                    Favorite @if(($wishlistCount ?? 0) > 0) ({{ $wishlistCount }}) @endif
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">
                    Favorite @if(($wishlistCount ?? 0) > 0) ({{ $wishlistCount }}) @endif
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                    Cos @if(($cartCount ?? 0) > 0) ({{ $cartCount }}) @endif
                </x-responsive-nav-link>
            @endauth

            <form action="{{ route('search') }}" method="GET" class="pt-3">
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cauta produse..."
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm"
                    >
                    <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white">
                        Cauta
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>
