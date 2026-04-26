<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IAZOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white shadow">
            <div class="market-shell mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>

    <footer class="market-footer">
        <div class="market-shell px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl">
                    <a href="{{ route('home') }}" class="inline-flex items-center">
                        <img src="{{ asset('favicon1.png') }}" alt="IAZOS" class="h-11 w-auto object-contain brightness-0 invert">
                    </a>
                    <p class="mt-4 text-sm leading-7 text-white/70">
                        Marketplace construit pentru experiente clare, selleri seriosi si produse prezentate intr-un cadru premium.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4 lg:min-w-[520px]">
                    <a href="{{ route('home') }}" class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center font-medium transition hover:bg-white/10">
                        Home
                    </a>
                    <a href="{{ route('sellers.index') }}" class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center font-medium transition hover:bg-white/10">
                        Selleri
                    </a>
                    <a href="{{ route('about') }}" class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center font-medium transition hover:bg-white/10">
                        Despre
                    </a>
                    <a href="{{ route('seller.application.create') }}" class="rounded-xl border border-white/10 bg-white px-4 py-3 text-center font-semibold text-[#33004a] transition hover:bg-white/90">
                        Devino seller
                    </a>
                </div>
            </div>

            <div class="market-footer-line my-8"></div>

            <div class="flex flex-col gap-3 text-xs text-white/55 sm:flex-row sm:items-center sm:justify-between">
                <div>{{ config('app.name', 'IAZOS') }}. White mode premium marketplace.</div>
                <div class="flex gap-4">
                    <a href="{{ route('terms') }}">Termeni</a>
                    <a href="{{ route('about') }}">Contact</a>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
</body>
</html>
