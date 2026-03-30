<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
                <p class="text-sm text-gray-500">Gestionează produse, categorii și bannere.</p>
            </div>

            <a href="{{ route('home') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                Vezi site
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Produse --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Produse</div>
                            <div class="text-xl font-bold text-gray-900">Administrare</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700 font-bold">
                            P
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Adaugă, editează, șterge produse, poze, promoții.
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('admin.products.index') }}"
                           class="w-full text-center px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
                            Lista produse
                        </a>
                        <a href="{{ route('admin.products.create') }}"
                           class="w-full text-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Adaugă
                        </a>
                    </div>
                </div>

                {{-- Categorii --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Categorii</div>
                            <div class="text-xl font-bold text-gray-900">Structură</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-700 font-bold">
                            C
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Creează categorii și subcategorii pentru sidebar.
                    </p>

                    <div class="mt-5">
                        <a href="{{ route('admin.categories.index') }}"
                           class="block w-full text-center px-4 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700">
                            Gestionare categorii
                        </a>
                    </div>
                </div>

                {{-- Bannere --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Bannere</div>
                            <div class="text-xl font-bold text-gray-900">Carusel</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-700 font-bold">
                            B
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Adaugă bannere pentru carusel (poză, titlu, subtitlu).
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('admin.banners.index') }}"
                           class="w-full text-center px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
                            Lista bannere
                        </a>
                        <a href="{{ route('admin.banners.create') }}"
                           class="w-full text-center px-4 py-2 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700">
                            Adaugă
                        </a>
                    </div>
                </div>

                {{-- Comenzi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Comenzi</div>
                            <div class="text-xl font-bold text-gray-900">Vânzări</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center text-green-700 font-bold">
                            O
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Vezi și gestionează comenzile clienților.
                    </p>

                    <div class="mt-5">
                        <a href="{{ route('admin.orders.index') }}"
                        class="block w-full text-center px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                            Lista comenzi
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Cereri seller</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">
                                {{ \App\Models\SellerApplication::where('status', 'pending')->count() }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-2">
                                Cereri noi care așteaptă aprobare.
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center text-xl font-bold">
                            S
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.seller_applications.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-3 bg-yellow-500 text-white rounded-xl font-semibold hover:bg-yellow-600 transition">
                            Vezi cererile
                        </a>
                    </div>
                </div>


            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">Sfat</div>
                        <div class="text-sm text-gray-600">
                            Dacă ai schimbat DB / migrații și ceva nu apare, rulează:
                            <span class="font-mono">php artisan optimize:clear</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
