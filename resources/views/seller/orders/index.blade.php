<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Comenzile mele
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Comenzi plătite</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $paidOrdersCount }}
                    </h3>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Venit brut</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">
                        {{ number_format((float)$grossRevenue, 2, '.', ',') }} MDL
                    </h3>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Comision ({{ number_format($commissionPercent, 2) }}%)</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">
                        {{ number_format((float)$marketplaceCommission, 2, '.', ',') }} MDL
                    </h3>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Venit net</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">
                        {{ number_format((float)$netRevenue, 2, '.', ',') }} MDL
                    </h3>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $orders->count() }}</span> comenzi
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">ID</th>
                                <th class="px-6 py-3 text-left">Client</th>
                                <th class="px-6 py-3 text-left">Telefon</th>
                                <th class="px-6 py-3 text-left">Status comandă</th>
                                <th class="px-6 py-3 text-left">Plată</th>
                                <th class="px-6 py-3 text-left">Produsele mele</th>
                                <th class="px-6 py-3 text-left">Brut</th>
                                <th class="px-6 py-3 text-left">Net</th>
                                <th class="px-6 py-3 text-right">Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($orders as $order)
                                @php
                                    $myItems = $order->items->where('seller_id', auth()->id());
                                    $myGrossTotal = $myItems->sum(fn($item) => $item->price * $item->qty);
                                    $myNetTotal = $myGrossTotal - ($myGrossTotal * ($commissionPercent / 100));
                                @endphp

                                <tr class="hover:bg-gray-50 align-top">
                                    <td class="px-6 py-4">{{ $order->id }}</td>
                                    <td class="px-6 py-4">
                                        {{ $order->customer_name ?? ($order->first_name . ' ' . $order->last_name) }}
                                    </td>
                                    <td class="px-6 py-4">{{ $order->customer_phone ?? $order->phone }}</td>
                                    <td class="px-6 py-4">{{ $order->status }}</td>
                                    <td class="px-6 py-4">{{ $order->payment_status }}</td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            @foreach($myItems as $item)
                                                <div class="rounded-lg border p-2">
                                                    <div class="font-medium">{{ $item->product_name }}</div>
                                                    <div class="text-xs text-gray-600">
                                                        Qty: {{ $item->qty }} × {{ number_format((float)$item->price, 2, '.', ',') }} MDL
                                                    </div>
                                                    <div class="mt-1 text-xs">
                                                        Status seller:
                                                        <span class="font-semibold">{{ $item->seller_status }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-blue-600">
                                        {{ number_format((float)$myGrossTotal, 2, '.', ',') }} MDL
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-green-600">
                                        {{ number_format((float)$myNetTotal, 2, '.', ',') }} MDL
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <a href="{{ route('seller.orders.show', $order->id) }}"
                                               class="px-3 py-1.5 rounded bg-gray-800 text-white text-xs hover:bg-black">
                                                Detalii
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-gray-600">
                                        Nu există comenzi pentru produsele tale.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>