<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Comenzile mele</h2>
            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">← Înapoi la Home</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 space-y-8">

            @php
                $pretty = [
                    'new' => 'Noi',
                    'confirmed' => 'Confirmate',
                    'processing' => 'În procesare',
                    'shipped' => 'Expediate',
                    'delivered' => 'Livrate',
                    'canceled' => 'Anulate',
                    'unknown' => 'Altele',
                ];

                $badge = [
                    'new' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'canceled' => 'bg-red-100 text-red-800',
                    'unknown' => 'bg-gray-100 text-gray-800',
                ];

                $payPretty = [
                    'unpaid' => 'Neachitat',
                    'pending' => 'În procesare',
                    'paid' => 'Achitat',
                    'failed' => 'Eșuat',
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
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-10 text-center">
                    <div class="text-gray-900 font-semibold">Nu ai încă comenzi.</div>
                    <div class="text-gray-500 text-sm mt-1">După ce plasezi o comandă, o vei vedea aici.</div>
                </div>
            @else

                @foreach($statusOrder as $status)
                    @php $list = $grouped->get($status, collect()); @endphp
                    @if($list->isEmpty()) @continue @endif

                    <section class="bg-white rounded-2xl shadow border border-gray-100 p-6">
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
                                    if (is_string($pd)) { $pd = json_decode($pd, true) ?: []; }

                                    $txId = data_get($pd, 'result.payId', $order->pay_id);
                                    $isPaid = ($payStatus === 'paid');
                                @endphp

                                <div class="rounded-2xl border border-gray-100 p-5 hover:border-gray-200 transition">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <div class="font-semibold text-gray-900">
                                                    Comanda #{{ $order->order_number ?? $order->id }}
                                                </div>

                                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $badge[$order->status] ?? $badge['unknown'] }}">
                                                    {{ $pretty[$order->status] ?? $order->status }}
                                                </span>

                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $payBadge[$payStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                                    <span class="inline-block w-3 h-3 rounded-full {{ $payDot[$payStatus] ?? 'bg-gray-400' }}"></span>
                                                    {{ $payPretty[$payStatus] ?? $payStatus }}
                                                </span>
                                            </div>

                                            <div class="mt-1 text-sm text-gray-500">
                                                Plasată: {{ optional($order->created_at)->format('d.m.Y H:i') }}
                                                @if($isPaid && $order->paid_at)
                                                    • Achitată: {{ $order->paid_at->format('d.m.Y H:i') }}
                                                @endif
                                            </div>

                                            @if($txId)
                                                <div class="mt-2 text-xs text-gray-500">
                                                    ID tranzacție: <span class="font-mono break-all text-gray-700">{{ $txId }}</span>
                                                </div>
                                            @endif

                                            <div class="mt-4 text-sm text-gray-700">
                                                <div class="font-semibold text-gray-900 mb-2">Produse</div>
                                                <div class="space-y-1">
                                                    @foreach($order->items->take(3) as $it)
                                                        <div class="flex justify-between">
                                                            <div class="truncate pr-4">
                                                                {{ $it->product_name }}
                                                                <span class="text-gray-400">× {{ $it->qty }}</span>
                                                            </div>
                                                            <div class="font-semibold whitespace-nowrap">
                                                                {{ number_format($it->price * $it->qty, 2) }} MDL
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if($order->items->count() > 3)
                                                        <div class="text-gray-400 text-xs">
                                                            + încă {{ $order->items->count() - 3 }} produse…
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!empty($order->customer_note))
                                                <div class="mt-4 text-sm text-gray-600">
                                                    <span class="font-semibold text-gray-900">Notă:</span>
                                                    {{ $order->customer_note }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col items-start lg:items-end gap-3">
                                            <div class="text-lg font-extrabold text-gray-900">
                                                {{ number_format($order->subtotal, 2) }} MDL
                                            </div>

                                            @if(!empty($order->pay_id))
                                                <a href="{{ route('pay.maib.receipt', ['payId' => $order->pay_id]) }}"
                                                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-900 text-white text-base font-semibold hover:bg-black">
                                                    Detalii plată
                                                    <span aria-hidden="true">→</span>
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
