<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Dashboard Seller
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Produse</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $productsCount }}</h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Comenzi plătite</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $paidOrdersCount }}</h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Venit brut</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">
                        {{ number_format((float)$grossRevenue, 2, '.', ',') }} MDL
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Comision ({{ number_format($commissionPercent, 2) }}%)</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">
                        {{ number_format((float)$marketplaceCommission, 2, '.', ',') }} MDL
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Venit net</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">
                        {{ number_format((float)$netRevenue, 2, '.', ',') }} MDL
                    </h3>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Produse</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $productsCount }}</h3>
                            <p class="text-sm text-gray-600 mt-2">
                                Vezi și gestionează produsele tale.
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold">
                            P
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('seller.products.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
                            Lista produse
                        </a>

                        <a href="{{ route('seller.products.create') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-black transition">
                            Adaugă produs
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Comenzi</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $paidOrdersCount }}</h3>
                            <p class="text-sm text-gray-600 mt-2">
                                Vezi comenzile care conțin produsele tale.
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center text-xl font-bold">
                            O
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('seller.orders.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition">
                            Lista comenzi
                        </a>
                    </div>
                    <a href="{{ route('seller.profile.edit') }}"
                        class="inline-flex items-center justify-center w-full px-4 py-3 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition">
                        Profil seller
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>