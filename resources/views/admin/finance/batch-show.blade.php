<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Batch payout #{{ $batch->id }}</h2>
            <a href="{{ route('admin.finance.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Inapoi la finance</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="rounded-2xl border bg-white p-6 shadow"><div class="text-sm text-gray-500">Status</div><div class="mt-2 text-2xl font-bold text-gray-900">{{ $batch->status }}</div></div>
                <div class="rounded-2xl border bg-white p-6 shadow"><div class="text-sm text-gray-500">Total</div><div class="mt-2 text-2xl font-bold text-emerald-600">{{ number_format((float) $batch->total_amount, 2, '.', ',') }} MDL</div></div>
                <div class="rounded-2xl border bg-white p-6 shadow"><div class="text-sm text-gray-500">Items</div><div class="mt-2 text-2xl font-bold text-gray-900">{{ $batch->items_count }}</div></div>
                <div class="rounded-2xl border bg-white p-6 shadow"><div class="text-sm text-gray-500">Paid at</div><div class="mt-2 text-2xl font-bold text-gray-900">{{ $batch->paid_at?->format('d.m.Y H:i') ?: '-' }}</div></div>
            </div>

            <div class="rounded-2xl border bg-white p-6 shadow">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-900">Itemi batch</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.finance.batch.export', $batch) }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">Export CSV</a>
                        @if($batch->status !== 'paid')
                            <form method="POST" action="{{ route('admin.finance.batch.mark_paid', $batch) }}">
                                @csrf
                                <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Marcheaza paid</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Seller</th>
                                <th class="px-4 py-3 text-left">Beneficiar</th>
                                <th class="px-4 py-3 text-left">IBAN</th>
                                <th class="px-4 py-3 text-left">Suma</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($batch->items as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->seller?->sellerProfile?->shop_name ?? $item->seller?->name }}</td>
                                    <td class="px-4 py-3">{{ $item->beneficiary_name ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->iban ?: '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ number_format((float) $item->amount, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3">{{ $item->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
