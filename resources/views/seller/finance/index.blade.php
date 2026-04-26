<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Comisioane platforma</h2>
                <p class="text-sm text-gray-500">Vanzatorii incaseaza direct banii de la clienti. Aici vezi ce comision ai de achitat catre platforma.</p>
            </div>
            <a href="{{ route('seller.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Inapoi la dashboard</a>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'in_progress' => 'In curs',
            'unpaid' => 'De achitat',
            'awaiting_admin_review' => 'Asteapta review admin',
            'paid' => 'Achitat',
            'overdue' => 'Intarziat',
        ];

        $statusClasses = [
            'in_progress' => 'bg-blue-100 text-blue-700',
            'unpaid' => 'bg-amber-100 text-amber-700',
            'awaiting_admin_review' => 'bg-purple-100 text-purple-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'overdue' => 'bg-red-100 text-red-700',
        ];

        $currentStatus = $currentPeriod->status;
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="rounded-3xl border border-blue-200 bg-blue-50 px-6 py-5 text-blue-900 shadow-sm">
                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-700">Model nou de finante</div>
                <p class="mt-2 text-sm leading-6">
                    Vanzatorii incaseaza direct banii de la clienti. Platforma calculeaza comisionul pentru comenzile platite,
                    iar comisionul se achita o data la 3 saptamani. Dupa inchiderea perioadei, ai 7 zile pentru achitare.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Rezumat perioada curenta</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Perioada: {{ $currentPeriod->period_start?->format('d.m.Y') }} - {{ $currentPeriod->period_end?->format('d.m.Y') }}
                            </p>
                        </div>
                        <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Vanzari brute</div>
                            <div class="mt-2 text-xl font-bold text-gray-900">{{ number_format((float) $currentPeriod->gross_sales_amount, 2, '.', ',') }} MDL</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Comision %</div>
                            <div class="mt-2 text-xl font-bold text-gray-900">{{ number_format((float) $currentPeriod->commission_percent, 2, '.', ',') }}%</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Comision datorat</div>
                            <div class="mt-2 text-xl font-bold text-amber-600">{{ number_format((float) $currentPeriod->commission_amount, 2, '.', ',') }} MDL</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Deadline</div>
                            <div class="mt-2 text-xl font-bold text-gray-900">{{ $currentPeriod->deadline_at?->format('d.m.Y') ?? '-' }}</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Zile ramase</div>
                            <div class="mt-2 text-xl font-bold text-gray-900">
                                @if(!is_null($daysRemaining))
                                    {{ $daysRemaining >= 0 ? $daysRemaining : '-' . abs($daysRemaining) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Status</div>
                            <div class="mt-2 text-xl font-bold text-gray-900">{{ $statusLabels[$currentStatus] ?? $currentStatus }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900">Instructiuni de plata catre platforma</h3>
                    <div class="mt-5 space-y-4 text-sm text-gray-700">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div><span class="font-semibold text-gray-900">Beneficiar:</span> {{ $platformBankDetails['beneficiary_name'] ?? 'TODO-SET-BENEFICIARY' }}</div>
                            <div class="mt-2"><span class="font-semibold text-gray-900">IBAN:</span> {{ $platformBankDetails['iban'] ?? 'TODO-SET-IBAN' }}</div>
                            <div class="mt-2"><span class="font-semibold text-gray-900">Banca:</span> {{ $platformBankDetails['bank_name'] ?? 'TODO-SET-BANK' }}</div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <div class="font-semibold text-gray-900">Destinatia platii</div>
                            <div class="mt-2 text-gray-700">
                                Comision perioada {{ $currentPeriod->period_start?->format('d.m.Y') }} - {{ $currentPeriod->period_end?->format('d.m.Y') }},
                                Seller {{ auth()->user()->sellerProfile?->shop_name ?? auth()->user()->name }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('seller.finance.current_period.submit') }}" class="space-y-3">
                            @csrf
                            <textarea name="seller_note" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm" placeholder="Ex: plata trimisa azi prin transfer bancar">{{ old('seller_note', $currentPeriod->seller_note) }}</textarea>
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gray-900 px-4 py-4 text-base font-semibold text-white hover:bg-black">
                                Am trimis plata
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Comenzile incluse in perioada</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Order number</th>
                                <th class="px-4 py-3 text-left">Data</th>
                                <th class="px-4 py-3 text-left">Total comanda</th>
                                <th class="px-4 py-3 text-left">Comision %</th>
                                <th class="px-4 py-3 text-left">Comision</th>
                                <th class="px-4 py-3 text-left">Status plata comanda</th>
                                <th class="px-4 py-3 text-left">Status comision</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($orders as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">#{{ $row->order->order_number ?? $row->order->id }}</td>
                                    <td class="px-4 py-3">{{ $row->order->paid_at?->format('d.m.Y H:i') ?? $row->order->created_at?->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ number_format((float) $row->gross, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ number_format((float) $row->commission_percent, 2, '.', ',') }}%</td>
                                    <td class="px-4 py-3 font-semibold text-amber-600">{{ number_format((float) $row->commission, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ $row->order->payment_status }}</td>
                                    <td class="px-4 py-3">{{ $statusLabels[$currentStatus] ?? $currentStatus }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nu exista comenzi incluse in aceasta perioada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Istoric perioade</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Perioada</th>
                                <th class="px-4 py-3 text-left">Vanzari brute</th>
                                <th class="px-4 py-3 text-left">Comision datorat</th>
                                <th class="px-4 py-3 text-left">Deadline</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Data achitarii</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($history as $period)
                                <tr>
                                    <td class="px-4 py-3">{{ $period->period_start?->format('d.m.Y') }} - {{ $period->period_end?->format('d.m.Y') }}</td>
                                    <td class="px-4 py-3">{{ number_format((float) $period->gross_sales_amount, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ number_format((float) $period->commission_amount, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ $period->deadline_at?->format('d.m.Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$period->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$period->status] ?? $period->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $period->paid_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
