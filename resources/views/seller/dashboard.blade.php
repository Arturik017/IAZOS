<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard seller</h2>
                <p class="text-sm text-gray-500">Produse, comenzi si comisionul pe care il ai de achitat platformei.</p>
            </div>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'missing' => 'Lipsa',
            'pending' => 'In verificare',
            'active' => 'Activ',
            'rejected' => 'Respins',
        ];

        $statusClasses = [
            'missing' => 'bg-gray-100 text-gray-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'active' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];

        $periodStatusLabels = [
            'in_progress' => 'In curs',
            'unpaid' => 'De achitat',
            'awaiting_admin_review' => 'Asteapta review admin',
            'paid' => 'Achitat',
            'overdue' => 'Intarziat',
        ];
    @endphp

    <div class="market-page py-10">
        <div class="market-shell mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Produse active</div>
                    <div class="mt-2 text-4xl font-bold text-gray-900">{{ $productsCount }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Comenzi platite</div>
                    <div class="mt-2 text-4xl font-bold text-gray-900">{{ $paidOrdersCount }}</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Vanzari brute</div>
                    <div class="mt-2 text-4xl font-bold text-blue-700">{{ number_format((float) $grossRevenue, 2, '.', ',') }} MDL</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Comision datorat platformei</div>
                    <div class="mt-2 text-4xl font-bold text-amber-600">{{ number_format((float) $commissionDue, 2, '.', ',') }} MDL</div>
                    <div class="mt-2 text-sm text-gray-500">{{ number_format((float) $commissionPercent, 2, '.', ',') }}%</div>
                </div>
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Deadline urmator</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $nextDeadline?->format('d.m.Y') ?? '-' }}</div>
                    <div class="mt-2 text-sm text-gray-500">
                        @if(!is_null($daysRemaining))
                            {{ $daysRemaining >= 0 ? $daysRemaining . ' zile ramase' : abs($daysRemaining) . ' zile intarziere' }}
                        @else
                            Fara deadline
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rezumat perioada curenta</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Perioada {{ $currentPeriod->period_start?->format('d.m.Y') }} - {{ $currentPeriod->period_end?->format('d.m.Y') }}.
                            </p>
                        </div>
                        <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$paymentAccountStatus] ?? 'bg-gray-100 text-gray-700' }}">
                            Plati online: {{ $statusLabels[$paymentAccountStatus] ?? $paymentAccountStatus }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Status perioada</div>
                            <div class="mt-2 text-lg font-bold text-gray-900">{{ $periodStatusLabels[$currentPeriod->status] ?? $currentPeriod->status }}</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Comision curent</div>
                            <div class="mt-2 text-lg font-bold text-gray-900">{{ number_format((float) $currentPeriod->commission_amount, 2, '.', ',') }} MDL</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Mesaje noi</div>
                            <div class="mt-2 text-lg font-bold text-gray-900">{{ $messageUnreadCount ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('seller.products.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-4 py-4 text-base font-semibold text-white hover:bg-black">
                            Produse
                        </a>
                        <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
                            Comenzi
                        </a>
                        <a href="{{ route('seller.profile.edit') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
                            Profil seller
                        </a>
                        <a href="{{ route('seller.finance.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
                            Comisioane platforma
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Cum functioneaza acum</h3>
                        <div class="mt-4 space-y-3 text-sm text-gray-600">
                            <div class="rounded-2xl bg-gray-50 px-4 py-4">
                                <div class="font-semibold text-gray-900">1. Clientul achita direct sellerul</div>
                                <div>Fiecare comanda a ta are plata proprie, fara split payment prin platforma.</div>
                            </div>
                            <div class="rounded-2xl bg-gray-50 px-4 py-4">
                                <div class="font-semibold text-gray-900">2. Platforma calculeaza comisionul</div>
                                <div>Comisionul se calculeaza doar pentru comenzile tale platite.</div>
                            </div>
                            <div class="rounded-2xl bg-gray-50 px-4 py-4">
                                <div class="font-semibold text-gray-900">3. Achiti comisionul periodic</div>
                                <div>Perioada este de 3 saptamani, iar dupa inchidere ai 7 zile pentru plata.</div>
                            </div>
                        </div>
                    </div>

                    @if(\App\Models\User::supportsMessaging())
                        <div class="market-section rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Mesaje</h3>
                            <p class="mt-1 text-sm text-gray-500">Ramai aproape de clienti si de admin.</p>
                            <div class="mt-5 space-y-3">
                                <a href="{{ route('messages.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-4 py-4 text-base font-semibold text-white hover:bg-sky-700">
                                    Deschide inbox
                                </a>
                                <a href="{{ route('seller.stories.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
                                    Story-uri
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
