<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Comanda #{{ $order->order_number ?? $order->id }}
                </h2>
                <p class="text-sm text-gray-500">
                    Vezi sellerii separat și statusul fiecărui produs.
                </p>
            </div>

            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                ← Înapoi la comenzi
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            @php
                $orderStatusPretty = [
                    'new' => 'Nouă',
                    'confirmed' => 'Confirmată',
                    'processing' => 'În procesare',
                    'partial_shipped' => 'Parțial expediată',
                    'shipped' => 'Expediată',
                    'completed' => 'Finalizată',
                    'delivered' => 'Livrată',
                    'cancelled' => 'Anulată',
                    'canceled' => 'Anulată',
                ];

                $orderStatusBadge = [
                    'new' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'partial_shipped' => 'bg-fuchsia-100 text-fuchsia-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'completed' => 'bg-emerald-100 text-emerald-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'canceled' => 'bg-red-100 text-red-800',
                ];

                $paymentPretty = [
                    'unpaid' => 'Neachitat',
                    'pending' => 'În procesare',
                    'paid' => 'Achitat',
                    'failed' => 'Eșuat',
                    'refunded' => 'Returnat',
                ];

                $paymentBadge = [
                    'unpaid' => 'bg-gray-100 text-gray-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-green-100 text-green-800',
                    'failed' => 'bg-red-100 text-red-800',
                    'refunded' => 'bg-indigo-100 text-indigo-800',
                ];

                $sellerPretty = [
                    'pending' => 'În așteptare',
                    'accepted' => 'Acceptat',
                    'processing' => 'În procesare',
                    'partial_shipped' => 'Parțial expediat',
                    'shipped' => 'Expediat',
                    'delivered' => 'Livrat',
                    'cancelled' => 'Anulat',
                ];

                $sellerBadge = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'accepted' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'partial_shipped' => 'bg-fuchsia-100 text-fuchsia-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];

                $refundStatusPretty = [
                    'requested' => 'Trimisa de client',
                    'seller_reviewed' => 'Are raspuns seller',
                    'approved' => 'Aprobata de admin',
                    'rejected' => 'Respinsa de admin',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Status comandă</div>
                    <div class="mt-3">
                        <span class="inline-flex px-3 py-2 rounded-full text-sm font-semibold {{ $orderStatusBadge[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $orderStatusPretty[$order->status] ?? ($order->status ?? 'Necunoscut') }}
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Plată</div>
                    <div class="mt-3">
                        <span class="inline-flex px-3 py-2 rounded-full text-sm font-semibold {{ $paymentBadge[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $paymentPretty[$order->payment_status] ?? ($order->payment_status ?? 'Necunoscut') }}
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Selleri</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $sellerGroups->count() }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Total comandă</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">
                        {{ number_format((float) $order->subtotal, 2) }} MDL
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Date livrare</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-semibold text-gray-900">Nume:</span>
                            {{ $order->customer_name ?? trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')) }}
                        </div>

                        <div>
                            <span class="font-semibold text-gray-900">Telefon:</span>
                            {{ $order->customer_phone ?? $order->phone }}
                        </div>

                        <div>
                            <span class="font-semibold text-gray-900">Adresă:</span>
                            {{ $order->district }}, {{ $order->locality }}, {{ $order->street }}
                            @if($order->postal_code)
                                , {{ $order->postal_code }}
                            @endif
                        </div>

                        @if($order->customer_note)
                            <div>
                                <span class="font-semibold text-gray-900">Notă client:</span>
                                {{ $order->customer_note }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Date comandă</h3>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <span class="font-semibold text-gray-900">Număr comandă:</span>
                            #{{ $order->order_number ?? $order->id }}
                        </div>

                        <div>
                            <span class="font-semibold text-gray-900">Plasată la:</span>
                            {{ optional($order->created_at)->format('d.m.Y H:i') }}
                        </div>

                        @if($order->paid_at)
                            <div>
                                <span class="font-semibold text-gray-900">Achitată la:</span>
                                {{ $order->paid_at->format('d.m.Y H:i') }}
                            </div>
                        @endif

                        @if($order->pay_id)
                            <div>
                                <span class="font-semibold text-gray-900">Pay ID:</span>
                                <span class="font-mono break-all">{{ $order->pay_id }}</span>
                            </div>
                        @endif

                        @if($order->pay_id)
                            <div class="pt-2">
                                <a href="{{ route('pay.maib.receipt', ['payId' => $order->pay_id]) }}"
                                   class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                                    Vezi detalii plată
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($sellerGroups as $group)
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $group->seller_name }}
                                        </h3>

                                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold {{ $sellerBadge[$group->summary_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $sellerPretty[$group->summary_status] ?? $group->summary_status }}
                                        </span>
                                    </div>

                                    <div class="mt-2 text-sm text-gray-500">
                                        {{ $group->items_count }} produse • {{ number_format((float) $group->subtotal, 2) }} MDL
                                    </div>

                                    @if($group->seller && ($group->seller->role ?? null) === 'seller' && ($group->seller->seller_status ?? null) === 'approved')
                                        <div class="mt-3">
                                            <a href="{{ route('seller.public.show', $group->seller) }}"
                                               class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-black">
                                                Vezi pagina sellerului →
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <div class="text-xs uppercase tracking-wide text-gray-500">Subtotal</div>
                                        <div class="mt-1 font-bold text-gray-900">{{ number_format((float) $group->subtotal, 2) }} MDL</div>
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <div class="text-xs uppercase tracking-wide text-gray-500">Produse</div>
                                        <div class="mt-1 font-bold text-gray-900">{{ $group->items_count }}</div>
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <div class="text-xs uppercase tracking-wide text-gray-500">Status grup</div>
                                        <div class="mt-1 font-bold text-gray-900">{{ $sellerPretty[$group->summary_status] ?? $group->summary_status }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($group->items as $item)
                                <div class="px-6 py-5">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-3">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $item->product_name }}
                                                </div>

                                                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold {{ $sellerBadge[$item->seller_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $sellerPretty[$item->seller_status] ?? ($item->seller_status ?? 'Necunoscut') }}
                                                </span>
                                            </div>

                                            @if(!empty($item->variant_label))
                                                <div class="mt-2 text-sm font-medium text-blue-700">
                                                    {{ $item->variant_label }}
                                                </div>
                                            @endif

                                            <div class="mt-2 text-sm text-gray-600">
                                                Cantitate: {{ $item->qty }}
                                                • Preț: {{ number_format((float) $item->price, 2) }} MDL
                                                • Total: {{ number_format((float) $item->price * (int) $item->qty, 2) }} MDL
                                            </div>

                                            @if($item->seller_status_updated_at)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    Ultima actualizare status:
                                                    {{ $item->seller_status_updated_at->format('d.m.Y H:i') }}
                                                </div>
                                            @endif

                                            @if($item->refundRequest)
                                                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                                    <div class="font-semibold">Solicitare refund</div>
                                                    <div class="mt-1">Status: {{ $refundStatusPretty[$item->refundRequest->status] ?? $item->refundRequest->status }}</div>
                                                    <div class="mt-1">Cerere: {{ $item->refundRequest->target_status === 'cancelled' ? 'Anulare' : 'Rambursare' }}</div>
                                                    <div class="mt-1">Motiv: {{ $item->refundRequest->client_reason }}</div>
                                                    @if($item->refundRequest->seller_response)
                                                        <div class="mt-2 text-xs">Raspuns seller: {{ $item->refundRequest->seller_response }}</div>
                                                    @endif
                                                    @if($item->refundRequest->admin_decision_note)
                                                        <div class="mt-2 text-xs">Nota admin: {{ $item->refundRequest->admin_decision_note }}</div>
                                                    @endif
                                                </div>
                                            @elseif(!in_array($item->financial_status, ['cancelled', 'refunded'], true))
                                                <form method="POST"
                                                      action="{{ route('refund_requests.store', [$order, $item]) }}"
                                                      class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-4 space-y-3">
                                                    @csrf
                                                    <div class="text-sm font-semibold text-red-900">Solicita refund pentru acest produs</div>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <select name="target_status" class="rounded-xl border-gray-300 text-sm">
                                                            <option value="cancelled">Anulare</option>
                                                            <option value="refunded">Rambursare</option>
                                                        </select>
                                                        <input name="client_reason" required placeholder="Motiv scurt" class="rounded-xl border-gray-300 text-sm">
                                                    </div>
                                                    <textarea name="client_note" rows="3" placeholder="Explica pe scurt problema pentru seller si admin" class="w-full rounded-xl border-gray-300 text-sm"></textarea>
                                                    <button class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                        Solicita refund
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <div class="flex flex-col items-start lg:items-end gap-3">
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ number_format((float) $item->price * (int) $item->qty, 2) }} MDL
                                            </div>

                                            @if($item->product)
                                                <a href="{{ route('product.show', $item->product) }}"
                                                   class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                                                    Vezi produsul
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
