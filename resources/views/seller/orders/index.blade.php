<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Comenzile mele</h2>
                <p class="mt-1 text-sm text-gray-500">Vezi comenzile tale, stadiul logistic si ce se intampla financiar cu fiecare produs.</p>
            </div>
            <a href="{{ route('seller.finance.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Vezi finantele
            </a>
        </div>
    </x-slot>

    @php
        $sellerStatusLabels = [
            'pending' => 'Nou',
            'confirmed' => 'Confirmat',
            'processing' => 'In pregatire',
            'shipped' => 'Expediat',
            'delivered_pending_review' => 'Livrat',
            'cancelled' => 'Anulat',
        ];
    @endphp

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Comenzi platite</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $paidOrdersCount }}</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Valoare bruta totala</p>
                    <h3 class="mt-2 text-3xl font-bold text-blue-600">{{ number_format((float) $grossRevenue, 2, '.', ',') }} MDL</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Comision marketplace ({{ number_format($commissionPercent, 2) }}%)</p>
                    <h3 class="mt-2 text-3xl font-bold text-rose-600">{{ number_format((float) $marketplaceCommission, 2, '.', ',') }} MDL</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Venit net estimat</p>
                    <h3 class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format((float) $netRevenue, 2, '.', ',') }} MDL</h3>
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="rounded-xl bg-gray-50 px-4 py-4">
                        <div class="text-sm font-semibold text-gray-900">Status logistic</div>
                        <div class="mt-1 text-sm text-gray-600">Urmaresti separat fiecare produs: nou, confirmat, expediat sau livrat.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 px-4 py-4">
                        <div class="text-sm font-semibold text-gray-900">Plata clientului</div>
                        <div class="mt-1 text-sm text-gray-600">Clientul plateste direct catre tine. Platforma urmareste doar daca plata a fost confirmata.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 px-4 py-4">
                        <div class="text-sm font-semibold text-gray-900">Refund / anulare</div>
                        <div class="mt-1 text-sm text-gray-600">Daca exista o cerere de refund, o vezi pe produsul respectiv si poti raspunde direct.</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">
                <div class="border-b px-6 py-4">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $orders->count() }}</span> comenzi
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">Comanda</th>
                                <th class="px-6 py-3 text-left">Client</th>
                                <th class="px-6 py-3 text-left">Plata</th>
                                <th class="px-6 py-3 text-left">Produsele mele</th>
                                <th class="px-6 py-3 text-left">Valori</th>
                                <th class="px-6 py-3 text-left">Plata / refund</th>
                                <th class="px-6 py-3 text-right">Actiuni</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($orders as $order)
                                @php
                                    $myItems = $order->items->where('seller_id', auth()->id());
                                    $myGrossTotal = 0;
                                    $myCommissionTotal = 0;
                                    $myNetTotal = 0;

                                    foreach ($myItems as $item) {
                                        $itemGross = (float) $item->price * (int) $item->qty;
                                        $itemCommission = $item->platform_commission_amount !== null
                                            ? (float) $item->platform_commission_amount
                                            : ($itemGross * ($commissionPercent / 100));
                                        $itemNet = $item->seller_net_amount !== null
                                            ? (float) $item->seller_net_amount
                                            : ($itemGross - $itemCommission);

                                        $myGrossTotal += $itemGross;
                                        $myCommissionTotal += $itemCommission;
                                        $myNetTotal += $itemNet;
                                    }
                                @endphp

                                <tr class="align-top hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">#{{ $order->order_number ?: $order->id }}</div>
                                        <div class="mt-1 text-xs text-gray-500">ID intern: {{ $order->id }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $order->created_at?->format('d.m.Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $order->customer_name ?? trim(($order->first_name ?? '').' '.($order->last_name ?? '')) }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $order->customer_phone ?? $order->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $order->payment_status }}</div>
                                        <div class="mt-2 text-xs text-gray-500">Status comanda: {{ $order->status }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            @foreach($myItems as $item)
                                                <div class="rounded-xl border border-gray-200 p-3">
                                                    <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                    <div class="mt-1 text-xs text-gray-500">Qty {{ $item->qty }} x {{ number_format((float) $item->price, 2, '.', ',') }} MDL</div>
                                                    <div class="mt-2 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                        {{ $sellerStatusLabels[$item->seller_status] ?? $item->seller_status }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2 text-sm">
                                            <div>
                                                <div class="text-xs uppercase tracking-[0.16em] text-gray-400">Brut</div>
                                                <div class="font-semibold text-blue-600">{{ number_format((float) $myGrossTotal, 2, '.', ',') }} MDL</div>
                                            </div>
                                            <div>
                                                <div class="text-xs uppercase tracking-[0.16em] text-gray-400">Comision</div>
                                                <div class="font-semibold text-rose-600">{{ number_format((float) $myCommissionTotal, 2, '.', ',') }} MDL</div>
                                            </div>
                                            <div>
                                                <div class="text-xs uppercase tracking-[0.16em] text-gray-400">Net</div>
                                                <div class="font-semibold text-emerald-600">{{ number_format((float) $myNetTotal, 2, '.', ',') }} MDL</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                    @foreach($myItems as $item)
                                        @php
                                            $paymentSummary = 'Plata client: ' . ($order->payment_status === 'paid' ? 'confirmata' : 'neconfirmata');
                                            $paymentDetail = 'Comision platforma inclus in perioada curenta.';

                                            if ($item->financial_status === 'refunded' || $item->refundRequest?->status === 'approved') {
                                                $paymentSummary = 'Rambursat clientului';
                                                $paymentDetail = 'Acest produs a iesit din calculul normal al comisionului.';
                                            } elseif ($item->seller_status === 'cancelled' || $item->financial_status === 'cancelled') {
                                                $paymentSummary = 'Anulat';
                                                $paymentDetail = 'Produsul nu mai este activ in fluxul acestei comenzi.';
                                            } elseif ($order->payment_status === 'paid') {
                                                $paymentSummary = 'Plata incasata direct de seller';
                                            }
                                        @endphp
                                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-xs text-gray-700">
                                                    <div><span class="font-semibold text-gray-900">{{ $paymentSummary }}</span></div>
                                                    <div class="mt-1 text-gray-600">{{ $paymentDetail }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <a href="{{ route('seller.orders.show', $order->id) }}" class="rounded-xl bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-black">
                                                Detalii
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-600">
                                        Nu exista comenzi pentru produsele tale.
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
