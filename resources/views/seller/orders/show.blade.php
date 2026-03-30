<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalii comandă #{{ $order->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Vezi doar partea ta din această comandă.
                </p>
            </div>

            <a href="{{ route('seller.orders.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-semibold hover:bg-black">
                Înapoi la comenzi
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Plată</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">
                        {{ $order->payment_status }}
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Status comandă</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">
                        {{ $order->status }}
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Total brut</p>
                    <h3 class="text-2xl font-bold text-blue-600 mt-2">
                        {{ number_format((float)$myGrossTotal, 2, '.', ',') }} MDL
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <p class="text-sm text-gray-500">Total net</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-2">
                        {{ number_format((float)$myNetTotal, 2, '.', ',') }} MDL
                    </h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow p-6 border">
                    <h3 class="text-lg font-semibold mb-4">Date client</h3>

                    <div class="space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">Nume:</span>
                            {{ $order->customer_name ?? ($order->first_name . ' ' . $order->last_name) }}
                        </div>

                        <div>
                            <span class="font-medium">Telefon:</span>
                            {{ $order->customer_phone ?? $order->phone }}
                        </div>

                        <div>
                            <span class="font-medium">Adresă:</span>
                            {{ $order->district }}, {{ $order->locality }}, {{ $order->street }}
                            @if($order->postal_code)
                                , {{ $order->postal_code }}
                            @endif
                        </div>

                        @if($order->customer_note)
                            <div>
                                <span class="font-medium">Notă client:</span>
                                {{ $order->customer_note }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 border">
                    <h3 class="text-lg font-semibold mb-4">Rezumat financiar</h3>

                    <div class="space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">Comision marketplace:</span>
                            {{ number_format((float)$commissionPercent, 2) }}%
                        </div>

                        <div>
                            <span class="font-medium">Valoare brută:</span>
                            {{ number_format((float)$myGrossTotal, 2, '.', ',') }} MDL
                        </div>

                        <div>
                            <span class="font-medium">Comision reținut:</span>
                            {{ number_format((float)$myCommission, 2, '.', ',') }} MDL
                        </div>

                        <div>
                            <span class="font-medium">Venit net estimat:</span>
                            {{ number_format((float)$myNetTotal, 2, '.', ',') }} MDL
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 border">
                <h3 class="text-lg font-semibold mb-4">Produsele mele din această comandă</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Produs</th>
                                <th class="px-4 py-3 text-left">Cantitate</th>
                                <th class="px-4 py-3 text-left">Preț</th>
                                <th class="px-4 py-3 text-left">Total</th>
                                <th class="px-4 py-3 text-left">Status seller</th>
                                <th class="px-4 py-3 text-left">Acțiune</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($myItems as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3">{{ $item->qty }}</td>
                                    <td class="px-4 py-3">{{ number_format((float)$item->price, 2, '.', ',') }} MDL</td>
                                    <td class="px-4 py-3 font-semibold">
                                        {{ number_format((float)($item->price * $item->qty), 2, '.', ',') }} MDL
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                            {{ $item->seller_status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST"
                                              action="{{ route('seller.orders.items.status', [$order->id, $item->id]) }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <select name="seller_status" class="border rounded-lg px-3 py-2 text-sm">
                                                @foreach($allowedStatuses as $status)
                                                    <option value="{{ $status }}" @selected($item->seller_status === $status)>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="submit"
                                                    class="px-3 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                                                Salvează
                                            </button>
                                        </form>

                                        @if($item->seller_status_updated_at)
                                            <div class="mt-2 text-xs text-gray-500">
                                                Actualizat: {{ $item->seller_status_updated_at->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>