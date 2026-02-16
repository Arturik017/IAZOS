<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img
                            src="{{ asset('favicon1.png') }}"
                            alt="Logo"
                            class="block h-9 w-auto"
                        >
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        Acasă
                    </x-nav-link>

                    <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                        Despre noi
                    </x-nav-link>

                    <x-nav-link href="{{ route('terms') }}" :active="request()->routeIs('terms')">
                        Termeni
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
                                Admin
                            </x-nav-link>
                        @endif
                    @endauth

                    {{-- Search (Desktop) --}}
                    <form action="{{ route('search') }}" method="GET" class="hidden md:flex items-center">
                        <div class="relative">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Caută produse..."
                                class="w-72 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            />
                            <button
                                type="submit"
                                class="absolute right-1 top-1/2 -translate-y-1/2 px-3 py-1 rounded-md bg-gray-900 text-white text-sm hover:bg-black"
                            >
                                Caută
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @php
                    $cartCount = collect(session('cart', []))->sum('qty');
                @endphp

                <a href="{{ route('cart.index') }}"
                   class="relative mr-3 inline-flex items-center justify-center rounded-full p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                        <path d="M10 2a3 3 0 00-3 3v1H5a1 1 0 00-1 1v9a2 2 0 002 2h8a2 2 0 002-2V7a1 1 0 00-1-1h-2V5a3 3 0 00-3-3zm-1 4V5a1 1 0 112 0v1h-2z" />
                    </svg>

                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @guest
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">
                        Logare
                    </a>

                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">
                        Înregistrare
                    </a>
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('orders.index')">
                                Comenzile mele
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ✅ Search bar pe telefon (vizibil direct sub navbar) --}}
    <div class="sm:hidden px-4 pb-3">
        <form action="{{ route('search') }}" method="GET" class="flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Caută produse..."
                class="flex-1 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
            />
            <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black">
                Caută
            </button>
        </form>
    </div>

    <!-- Responsive Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">

        {{-- Link-uri publice în burger --}}
        <div class="pt-2 pb-2 space-y-1">
            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                Acasă
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                Despre noi
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('terms') }}" :active="request()->routeIs('terms')">
                Termeni
            </x-responsive-nav-link>
        </div>

        @guest
            <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.*')">
                Coș
                @if(collect(session('cart', []))->sum('qty') > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-blue-600 text-white">
                        {{ collect(session('cart', []))->sum('qty') }}
                    </span>
                @endif
            </x-responsive-nav-link>

            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('login') }}">
                    Logare
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('register') }}">
                    Înregistrare
                </x-responsive-nav-link>
            </div>
        @endguest

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.*')">
                    Coș
                    @if(collect(session('cart', []))->sum('qty') > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-blue-600 text-white">
                            {{ collect(session('cart', []))->sum('qty') }}
                        </span>
                    @endif
                </x-responsive-nav-link>

                {{-- ✅ Comenzile mele pe telefon în burger --}}
                <x-responsive-nav-link href="{{ route('orders.index') }}" :active="request()->routeIs('orders.*')">
                    Comenzile mele
                </x-responsive-nav-link>

                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
                        Admin
                    </x-responsive-nav-link>
                @endif

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profil
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link
                            href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
