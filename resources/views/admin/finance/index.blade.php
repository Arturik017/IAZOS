<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Finance marketplace</h2>
                <p class="text-sm text-gray-500">Comisioane selleri, perioade de plata si configurari de payment account.</p>
            </div>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'in_progress' => 'In curs',
            'unpaid' => 'De achitat',
            'awaiting_admin_review' => 'Asteapta review admin',
            'paid' => 'Achitat',
            'overdue' => 'Intarziat',
            'missing' => 'Lipsa',
            'pending' => 'In verificare',
            'active' => 'Activ',
            'rejected' => 'Respins',
        ];

        $statusClasses = [
            'in_progress' => 'bg-blue-100 text-blue-700',
            'unpaid' => 'bg-amber-100 text-amber-700',
            'awaiting_admin_review' => 'bg-purple-100 text-purple-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'overdue' => 'bg-red-100 text-red-700',
            'missing' => 'bg-gray-100 text-gray-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'active' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="grid gap-6 md:grid-cols-4">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Perioade in review</div>
                    <div class="mt-2 text-4xl font-bold text-purple-700">{{ $summary['awaiting_admin_review'] }}</div>
                </div>
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Perioade intarziate</div>
                    <div class="mt-2 text-4xl font-bold text-red-700">{{ $summary['overdue'] }}</div>
                </div>
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Comision de incasat</div>
                    <div class="mt-2 text-4xl font-bold text-amber-600">{{ number_format((float) $summary['unpaid_total'], 2, '.', ',') }} MDL</div>
                </div>
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Payment accounts de verificat</div>
                    <div class="mt-2 text-4xl font-bold text-gray-900">{{ $summary['payment_accounts_pending'] }}</div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Perioade comision selleri</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Seller</th>
                                <th class="px-4 py-3 text-left">Perioada</th>
                                <th class="px-4 py-3 text-left">Vanzari brute</th>
                                <th class="px-4 py-3 text-left">Comision</th>
                                <th class="px-4 py-3 text-left">Deadline</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Actualizare</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($periods as $period)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $period->seller?->sellerProfile?->shop_name ?? $period->seller?->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $period->seller?->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $period->period_start?->format('d.m.Y') }} - {{ $period->period_end?->format('d.m.Y') }}</td>
                                    <td class="px-4 py-3">{{ number_format((float) $period->gross_sales_amount, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ number_format((float) $period->commission_amount, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ $period->deadline_at?->format('d.m.Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$period->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$period->status] ?? $period->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.finance.periods.status', $period) }}" class="space-y-2">
                                            @csrf
                                            <select name="status" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                @foreach(['unpaid', 'awaiting_admin_review', 'paid', 'overdue'] as $status)
                                                    <option value="{{ $status }}" @selected($period->status === $status)>{{ $statusLabels[$status] }}</option>
                                                @endforeach
                                            </select>
                                            <input name="admin_note" value="{{ $period->admin_note }}" placeholder="Nota admin" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                            <button type="submit" class="inline-flex rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                                Salveaza
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nu exista perioade de comision inca.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Seller payment accounts</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Seller</th>
                                <th class="px-4 py-3 text-left">Provider</th>
                                <th class="px-4 py-3 text-left">Merchant / Terminal</th>
                                <th class="px-4 py-3 text-left">IBAN</th>
                                <th class="px-4 py-3 text-left">Contact</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Verificat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($paymentAccounts as $account)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $account->sellerProfile?->shop_name ?? $account->sellerProfile?->user?->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $account->sellerProfile?->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 uppercase">{{ $account->provider ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div>{{ $account->merchant_id ?: '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $account->terminal_id ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $account->settlement_iban ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $account->payment_contact_email ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$account->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$account->status] ?? $account->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $account->verified_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nu exista payment accounts inca.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
