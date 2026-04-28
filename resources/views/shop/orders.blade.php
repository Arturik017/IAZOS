<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Comenzile mele</h2>
            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">← Inapoi la Home</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4">

            @php
                $pretty = [
                    'pending_payment' => 'Asteapta plati',
                    'paid' => 'Platite',
                    'new' => 'Noi',
                    'confirmed' => 'Confirmate',
                    'processing' => 'In procesare',
                    'partial_shipped' => 'Partial expediate',
                    'shipped' => 'Expediate',
                    'completed' => 'Finalizate',
                    'delivered' => 'Livrate',
                    'cancelled' => 'Anulate',
                    'canceled' => 'Anulate',
                    'unknown' => 'Altele',
                ];

                $badge = [
                    'pending_payment' => 'bg-amber-100 text-amber-800',
                    'paid' => 'bg-emerald-100 text-emerald-800',
                    'new' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'partial_shipped' => 'bg-fuchsia-100 text-fuchsia-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'completed' => 'bg-emerald-100 text-emerald-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'canceled' => 'bg-red-100 text-red-800',
                    'unknown' => 'bg-gray-100 text-gray-800',
                ];

                $payPretty = [
                    'unpaid' => 'Neachitat',
                    'pending' => 'In procesare',
                    'paid' => 'Achitat',
                    'failed' => 'Esuat',
                    'refunded' => 'Returnat',
                ];

                $payBadge = [
                    'unpaid' => 'bg-gray-100 text-gray-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-green-100 text-green-800',
                    'failed' => 'bg-red-100 text-red-800',
                    'refunded' => 'bg-indigo-100 text-indigo-800',
                ];

                $payDot = [
                    'unpaid' => 'bg-gray-400',
                    'pending' => 'bg-yellow-500',
                    'paid' => 'bg-green-600',
                    'failed' => 'bg-red-600',
                    'refunded' => 'bg-indigo-600',
                ];
            @endphp

            @if($grouped->isEmpty())
                <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center shadow">
                    <div class="font-semibold text-gray-900">Nu ai inca comenzi.</div>
                    <div class="mt-1 text-sm text-gray-500">Dupa ce plasezi o comanda, o vei vedea aici.</div>
                </div>
            @else
                @foreach($statusOrder as $status)
                    @php $list = $grouped->get($status, collect()); @endphp
                    @if($list->isEmpty())
                        @continue
                    @endif

                    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $pretty[$status] ?? strtoupper($status) }}
                            </h3>
                            <span class="text-sm text-gray-500">{{ $list->count() }} comenzi</span>
                        </div>

                        <div class="mt-5 space-y-4">
                            @foreach($list as $order)
                                @php
                                    $payStatus = $order->payment_status ?? 'unpaid';
                                    $pd = $order->payment_details ?? [];
                                    if (is_string($pd)) {
                                        $pd = json_decode($pd, true) ?: [];
                                    }

                                    $txId = data_get($pd, 'result.payId', $order->pay_id);
                                    $isPaid = ($payStatus === 'paid');

                                    $sellerNames = $order->items
                                        ->map(fn ($item) => $item->seller?->sellerProfile?->shop_name ?? $item->seller?->name)
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp

                                <div class="rounded-2xl border border-gray-100 p-5 transition hover:border-gray-200">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <div class="font-semibold text-gray-900">
                                                    Comanda #{{ $order->order_number ?? $order->id }}
                                                </div>

                                                <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $badge[$order->status] ?? $badge['unknown'] }}">
                                                    {{ $pretty[$order->status] ?? $order->status }}
                                                </span>

                                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $payBadge[$payStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                                    <span class="inline-block h-3 w-3 rounded-full {{ $payDot[$payStatus] ?? 'bg-gray-400' }}"></span>
                                                    {{ $payPretty[$payStatus] ?? $payStatus }}
                                                </span>
                                            </div>

                                            <div class="mt-1 text-sm text-gray-500">
                                                Plasata: {{ optional($order->created_at)->format('d.m.Y H:i') }}
                                                @if($isPaid && $order->paid_at)
                                                    • Achitata: {{ $order->paid_at->format('d.m.Y H:i') }}
                                                @endif
                                            </div>

                                            @if($txId)
                                                <div class="mt-2 text-xs text-gray-500">
                                                    ID tranzactie:
                                                    <span class="break-all font-mono text-gray-700">{{ $txId }}</span>
                                                </div>
                                            @endif

                                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                                    <div class="text-xs uppercase tracking-wide text-gray-500">Produse</div>
                                                    <div class="mt-1 text-lg font-bold text-gray-900">{{ $order->items_count ?? $order->items->count() }}</div>
                                                </div>

                                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                                    <div class="text-xs uppercase tracking-wide text-gray-500">Selleri</div>
                                                    <div class="mt-1 text-lg font-bold text-gray-900">{{ $order->sellers_count ?? $order->items->pluck('seller_id')->filter()->unique()->count() }}</div>
                                                </div>

                                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                                    <div class="text-xs uppercase tracking-wide text-gray-500">Total</div>
                                                    <div class="mt-1 text-lg font-bold text-gray-900">{{ number_format((float) $order->subtotal, 2) }} MDL</div>
                                                </div>
                                            </div>

                                            @if($sellerNames->isNotEmpty())
                                                <div class="mt-4 text-sm text-gray-600">
                                                    <span class="font-semibold text-gray-900">Selleri:</span>
                                                    {{ $sellerNames->join(', ') }}
                                                </div>
                                            @endif

                                            @if(!empty($order->customer_note))
                                                <div class="mt-4 text-sm text-gray-600">
                                                    <span class="font-semibold text-gray-900">Nota:</span>
                                                    {{ $order->customer_note }}
                                                </div>
                                            @endif

                                            @if($order->payment_flow === 'seller_direct' && $order->payment_status !== 'paid' && $order->checkout_uuid)
                                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                                    Plata pentru aceasta comanda nu este finalizata inca. Poti reveni oricand in pagina de plati separate ca sa continui checkout-ul.
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col items-start gap-3 lg:items-end">
                                            <a href="{{ route('orders.show', $order) }}"
                                               class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-base font-semibold text-white hover:bg-black">
                                                Vezi comanda
                                                <span aria-hidden="true">→</span>
                                            </a>

                                            @if($order->payment_flow === 'seller_direct' && $order->payment_status !== 'paid' && $order->checkout_uuid)
                                                <a href="{{ route('checkout.payments.show', $order->checkout_uuid) }}"
                                                   class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-base font-semibold text-amber-900 hover:bg-amber-100">
                                                    Continua platile
                                                </a>
                                            @endif

                                            @if(!empty($order->pay_id))
                                                <a href="{{ route('pay.maib.receipt', ['payId' => $order->pay_id]) }}"
                                                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-base font-semibold text-gray-900 hover:bg-gray-50">
                                                    Detalii plata
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
