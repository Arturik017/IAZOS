<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Detalii despre plată</h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la comenzi
            </a>
        </div>
    </x-slot>

    @php
        $pd = $order->payment_details ?? [];
        if (is_string($pd)) { $pd = json_decode($pd, true) ?: []; }

        $isPaid = (($order->payment_status ?? '') === 'paid');

        $status = data_get($pd, 'result.status');
        $statusCode = data_get($pd, 'result.statusCode');

        $amount = data_get($pd, 'result.amount');
        $currency = data_get($pd, 'result.currency', 'MDL');

        $payId = data_get($pd, 'result.payId', $order->pay_id);
        $maibOrderId = data_get($pd, 'result.orderId');

        $displayAmount = $amount ? ($amount . ' ' . $currency) : (number_format($order->subtotal, 2) . ' MDL');
        $displayDate = $order->paid_at ? $order->paid_at->format('d.m.Y H:i') : '—';
        $merchant = config('app.name');
        $website = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        $desc = 'Plata comanda #' . ($order->order_number ?? $order->id);
    @endphp

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 space-y-6">

            {{-- Header card --}}
            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center {{ $isPaid ? 'bg-green-100' : 'bg-yellow-100' }}">
                            <span class="text-2xl">{{ $isPaid ? '✅' : '⏳' }}</span>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xl font-bold text-gray-900">
                                {{ $isPaid ? 'Plată efectuată cu succes' : 'Plata se procesează' }}
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                {{ $isPaid ? 'Confirmarea plății este salvată în sistem.' : 'Te rugăm să revii peste câteva momente.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Comerciant</div>
                            <div class="font-semibold text-gray-900">{{ $merchant }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Website</div>
                            <div class="font-semibold text-gray-900">{{ $website }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Sumă</div>
                            <div class="font-semibold text-gray-900">{{ $displayAmount }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Data achitării</div>
                            <div class="font-semibold text-gray-900">{{ $displayDate }}</div>
                        </div>
                    </div>

                    {{-- Receipt details --}}
                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <dl class="space-y-3 text-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">ID tranzacție</dt>
                                <dd class="font-mono font-semibold text-gray-900 break-all">{{ $payId ?: '—' }}</dd>
                            </div>

                            @if($maibOrderId)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <dt class="text-gray-500">Order ID (MAIB)</dt>
                                    <dd class="font-mono font-semibold text-gray-900 break-all">{{ $maibOrderId }}</dd>
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">Descriere</dt>
                                <dd class="font-semibold text-gray-900">{{ $desc }}</dd>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">Status (MAIB)</dt>
                                <dd class="font-mono font-semibold text-gray-900">{{ $status ?? '—' }} / {{ $statusCode ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('orders.index') }}"
                           class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl bg-gray-900 text-white text-base font-semibold hover:bg-black">
                            Înapoi la comenzi
                        </a>
                    
                        @if($payId)
                            <button type="button"
                                    onclick="navigator.clipboard?.writeText(@js((string)$payId)); this.innerText='Copiat ✅';"
                                    class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl border border-gray-200 text-base font-semibold hover:bg-gray-50">
                                Copiază ID tranzacție
                            </button>
                        @endif
                    </div>

                </div>
            </div>

            <div class="text-xs text-gray-500">
                Păstrează ID-ul tranzacției pentru suport: <span class="font-mono">{{ $payId ?: '—' }}</span>
            </div>

        </div>
    </div>
</x-app-layout>
