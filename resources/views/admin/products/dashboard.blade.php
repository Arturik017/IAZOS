<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
                <p class="text-sm text-gray-500">Control central pentru produse, selleri, comisioane si plati online.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Vezi site
            </a>
        </div>
    </x-slot>

    <div class="market-page py-10">
        <div class="market-shell mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Produse active</div>
                    <div class="mt-2 text-4xl font-bold text-gray-900">{{ $activeProducts }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Cereri seller</div>
                    <div class="mt-2 text-4xl font-bold text-amber-700">{{ $pendingSellerApplications }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Perioade in review</div>
                    <div class="mt-2 text-4xl font-bold text-purple-700">{{ $commissionPeriodsAwaitingReview }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Perioade intarziate</div>
                    <div class="mt-2 text-4xl font-bold text-red-700">{{ $commissionPeriodsOverdue }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Payment accounts de verificat</div>
                    <div class="mt-2 text-4xl font-bold text-blue-700">{{ $paymentAccountsPending }}</div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Produse</div>
                    <div class="mt-2 text-xl font-bold text-gray-900">Administrare catalog</div>
                    <p class="mt-3 text-sm text-gray-600">Adauga, editeaza si aproba produse fara sa atingi flow-ul nou de plati.</p>
                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('admin.products.index') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-black">Lista produse</a>
                        <a href="{{ route('admin.products.create') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50">Adauga</a>
                    </div>
                </div>

                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Seller applications</div>
                    <div class="mt-2 text-xl font-bold text-gray-900">Aprobare selleri</div>
                    <p class="mt-3 text-sm text-gray-600">Verifici cererile seller si vezi si datele lor pentru plati online.</p>
                    <div class="mt-5">
                        <a href="{{ route('admin.seller_applications.index') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white hover:bg-amber-600">Deschide cererile</a>
                    </div>
                </div>

                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Finance</div>
                    <div class="mt-2 text-xl font-bold text-gray-900">Comisioane marketplace</div>
                    <p class="mt-3 text-sm text-gray-600">Vezi perioadele de comision, seller payment accounts si confirmarile manuale.</p>
                    <div class="mt-5">
                        <a href="{{ route('admin.finance.index') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Deschide finance</a>
                    </div>
                </div>

                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Categorii</div>
                    <div class="mt-2 text-xl font-bold text-gray-900">Structura catalog</div>
                    <p class="mt-3 text-sm text-gray-600">Gestionare categorii si subcategorii pentru marketplace.</p>
                    <div class="mt-5">
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50">Gestionare categorii</a>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Bannere</div>
                    <div class="mt-2 text-xl font-bold text-gray-900">Promovare homepage</div>
                    <p class="mt-3 text-sm text-gray-600">Controlezi caruselul si bannerele de pe site.</p>
                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('admin.banners.index') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-black">Lista</a>
                        <a href="{{ route('admin.banners.create') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50">Adauga</a>
                    </div>
                </div>

                @if(\App\Models\User::supportsMessaging())
                    <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="text-sm text-gray-500">Mesaje</div>
                        <div class="mt-2 text-xl font-bold text-gray-900">{{ $messageUnreadCount }} necitite</div>
                        <p class="mt-3 text-sm text-gray-600">Inbox comun pentru selleri si clienti.</p>
                        <div class="mt-5">
                            <a href="{{ route('messages.index') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-700">Deschide inbox</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
