<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Detalii comanda #{{ $order->order_number ?: $order->id }}</h2>
                <p class="mt-1 text-sm text-gray-500">Vezi strict produsele tale, banii aferenti si ce urmeaza in flow.</p>
            </div>

            <a href="{{ route('seller.orders.index') }}"
               class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                Inapoi la comenzi
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

        $refundRequestLabels = [
            'requested' => 'Clientul a trimis cererea',
            'seller_reviewed' => 'Ai raspuns deja',
            'approved' => 'Aprobata de admin',
            'rejected' => 'Respinsa de admin',
        ];
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Plata comenzii</p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $order->payment_status }}</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Status comanda</p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $order->status }}</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total brut</p>
                    <h3 class="mt-2 text-2xl font-bold text-blue-600">{{ number_format((float) $myGrossTotal, 2, '.', ',') }} MDL</h3>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total net estimat</p>
                    <h3 class="mt-2 text-2xl font-bold text-emerald-600">{{ number_format((float) $myNetTotal, 2, '.', ',') }} MDL</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Date client</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">Nume:</span>
                            {{ $order->customer_name ?? trim(($order->first_name ?? '').' '.($order->last_name ?? '')) }}
                        </div>

                        <div>
                            <span class="font-medium">Telefon:</span>
                            {{ $order->customer_phone ?? $order->phone }}
                        </div>

                        <div>
                            <span class="font-medium">Adresa:</span>
                            {{ $order->district }}, {{ $order->locality }}, {{ $order->street }}
                            @if($order->postal_code)
                                , {{ $order->postal_code }}
                            @endif
                        </div>

                        @if($order->customer_note)
                            <div>
                                <span class="font-medium">Nota client:</span>
                                {{ $order->customer_note }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Rezumat financiar</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">Comision marketplace:</span>
                            {{ number_format((float) $commissionPercent, 2) }}%
                        </div>

                        <div>
                            <span class="font-medium">Valoare bruta:</span>
                            {{ number_format((float) $myGrossTotal, 2, '.', ',') }} MDL
                        </div>

                        <div>
                            <span class="font-medium">Comision estimat:</span>
                            {{ number_format((float) $myCommission, 2, '.', ',') }} MDL
                        </div>

                        <div>
                            <span class="font-medium">Venit net estimat:</span>
                            {{ number_format((float) $myNetTotal, 2, '.', ',') }} MDL
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl bg-gray-50 px-4 py-4 text-sm text-gray-600">
                        In modelul nou, clientul achita direct catre tine. Platforma nu mai tine solduri pending/available pentru seller,
                        ci urmareste plata confirmata, comisionul datorat si eventualele refund-uri.
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Produsele mele din aceasta comanda</h3>
                        <p class="mt-1 text-sm text-gray-500">Fiecare item are status logistic separat si evidenta financiara separata.</p>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Produs</th>
                                <th class="px-4 py-3 text-left">Cantitate</th>
                                <th class="px-4 py-3 text-left">Valori</th>
                                <th class="px-4 py-3 text-left">Logistic</th>
                                <th class="px-4 py-3 text-left">Financiar</th>
                                <th class="px-4 py-3 text-left">Actualizare</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($myItems as $item)
                                @php
                                    $itemGross = (float) ($item->gross_amount ?? ((float) $item->price * (int) $item->qty));
                                    $itemCommission = (float) ($item->platform_commission_amount ?? ($itemGross * ($commissionPercent / 100)));
                                    $itemNet = (float) ($item->seller_net_amount ?? ($itemGross - $itemCommission));
                                @endphp
                                <tr class="align-top">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                        @if(!empty($item->variant_label))
                                            <div class="mt-1 text-xs font-medium text-blue-700">{{ $item->variant_label }}</div>
                                        @endif
                                        <div class="mt-2 text-xs text-gray-500">Item ID: {{ $item->id }}</div>
                                    </td>

                                    <td class="px-4 py-4">{{ $item->qty }}</td>

                                    <td class="px-4 py-4">
                                        <div class="space-y-1 text-sm">
                                            <div><span class="font-medium text-gray-900">Pret:</span> {{ number_format((float) $item->price, 2, '.', ',') }} MDL</div>
                                            <div><span class="font-medium text-gray-900">Brut:</span> {{ number_format($itemGross, 2, '.', ',') }} MDL</div>
                                            <div><span class="font-medium text-gray-900">Comision:</span> {{ number_format($itemCommission, 2, '.', ',') }} MDL</div>
                                            <div><span class="font-medium text-gray-900">Net:</span> {{ number_format($itemNet, 2, '.', ',') }} MDL</div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {{ $sellerStatusLabels[$item->seller_status] ?? $item->seller_status }}
                                        </span>
                                        @if($item->seller_status_updated_at)
                                            <div class="mt-2 text-xs text-gray-500">
                                                Actualizat: {{ $item->seller_status_updated_at->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        @php
                                            $paymentSummary = 'Plata client: ' . ($order->payment_status === 'paid' ? 'confirmata' : 'neconfirmata');
                                            $paymentDetail = 'Comisionul pentru acest produs este urmarit separat in perioada curenta.';

                                            if ($item->financial_status === 'refunded' || $item->refundRequest?->status === 'approved') {
                                                $paymentSummary = 'Rambursat clientului';
                                                $paymentDetail = 'Produsul a iesit din fluxul normal si nu mai ramane comanda activa pentru el.';
                                            } elseif ($item->seller_status === 'cancelled' || $item->financial_status === 'cancelled') {
                                                $paymentSummary = 'Anulat';
                                                $paymentDetail = 'Produsul a fost scos din fluxul acestei comenzi.';
                                            } elseif ($order->payment_status === 'paid') {
                                                $paymentSummary = 'Plata incasata direct de seller';
                                            }
                                        @endphp
                                        <div><span class="font-medium text-gray-900">{{ $paymentSummary }}</span></div>
                                        <div class="mt-1 text-gray-600">{{ $paymentDetail }}</div>

                                        @if($item->refundRequest)
                                            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs text-amber-900 space-y-2">
                                                <div class="font-semibold">Solicitare refund / anulare</div>
                                                <div>Clientul cere: {{ $item->refundRequest->target_status === 'cancelled' ? 'Anulare' : 'Rambursare' }}</div>
                                                <div>Status request: {{ $refundRequestLabels[$item->refundRequest->status] ?? $item->refundRequest->status }}</div>
                                                <div>Motiv client: {{ $item->refundRequest->client_reason }}</div>
                                                @if($item->refundRequest->client_note)
                                                    <div>Detalii client: {{ $item->refundRequest->client_note }}</div>
                                                @endif
                                                @if($item->refundRequest->admin_decision_note)
                                                    <div>Nota admin: {{ $item->refundRequest->admin_decision_note }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4">
                                        <form method="POST"
                                              action="{{ route('seller.orders.items.status', [$order->id, $item->id]) }}"
                                              class="space-y-2">
                                            @csrf
                                            @method('PATCH')

                                            <select name="seller_status" class="w-full rounded-lg border px-3 py-2 text-sm">
                                                @foreach($allowedStatuses as $status)
                                                    <option value="{{ $status }}" @selected($item->seller_status === $status)>
                                                        {{ $sellerStatusLabels[$status] ?? $status }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="submit"
                                                    class="w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                                Salveaza
                                            </button>
                                        </form>

                                        @if($item->refundRequest && !in_array($item->refundRequest->status, ['approved', 'rejected'], true))
                                            <form method="POST"
                                                  action="{{ route('seller.refund_requests.respond', $item->refundRequest) }}"
                                                  class="mt-3 space-y-2 rounded-xl border border-gray-200 bg-gray-50 p-3">
                                                @csrf
                                                @method('PATCH')
                                                <select name="seller_recommended_status" class="w-full rounded-lg border px-3 py-2 text-xs">
                                                    <option value="cancelled">Recomand anulare</option>
                                                    <option value="refunded">Recomand refund</option>
                                                </select>
                                                <textarea name="seller_response" rows="3" required placeholder="Spune adminului si clientului ce ai verificat" class="w-full rounded-lg border px-3 py-2 text-xs"></textarea>
                                                <button class="w-full rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-black">
                                                    Trimite raspuns
                                                </button>
                                            </form>
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
