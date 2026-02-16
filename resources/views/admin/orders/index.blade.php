<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Comenzi (Admin)</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @php
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

                $allowed = ['new','confirmed','processing','shipped','delivered','canceled'];
            @endphp

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
                            <div class="rounded-2xl border border-gray-100 p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            Comanda #{{ $order->order_number ?? $order->id }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ optional($order->created_at)->format('d.m.Y H:i') }}
                                            • {{ $order->first_name ?? '' }} {{ $order->last_name ?? '' }}
                                            • {{ $order->phone ?? '' }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600">
                                            <span class="font-semibold">Adresă:</span>
                                            {{ $order->district ?? '' }}, {{ $order->locality ?? '' }}
                                            @if(!empty($order->postal_code)) ({{ $order->postal_code }}) @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge[$order->status] ?? $badge['unknown'] }}">
                                            {{ $pretty[$order->status] ?? $order->status }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $payBadge[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $payPretty[$order->payment_status] ?? ($order->payment_status ?? '—') }}
                                        </span>

                                        @if($order->pay_id)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Pay ID: <span class="font-mono">{{ $order->pay_id }}</span>
                                            </div>
                                        @endif


                                        <div class="text-lg font-extrabold text-gray-900">
                                            {{ number_format($order->subtotal, 2) }} MDL
                                        </div>

                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm font-semibold">
                                            Detalii
                                        </a>
                                    </div>
                                </div>

                                {{-- Items preview --}}
                                <div class="mt-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900 mb-2">Produse:</div>
                                    <div class="space-y-1">
                                        @foreach($order->items->take(4) as $it)
                                            <div class="flex justify-between">
                                                <div>{{ $it->product_name }} <span class="text-gray-400">× {{ $it->qty }}</span></div>
                                                <div class="font-semibold">{{ number_format($it->price * $it->qty, 2) }} MDL</div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="text-gray-400">+ încă {{ $order->items->count() - 4 }} produse…</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Change status --}}
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex flex-col sm:flex-row gap-3 sm:items-center">
                                        @csrf
                                        @method('PATCH')

                                        <label class="text-sm font-semibold text-gray-900">Schimbă status:</label>

                                        <select name="status" class="rounded-lg border-gray-300 shadow-sm">
                                            @foreach($allowed as $st)
                                                <option value="{{ $st }}" @selected($order->status === $st)>
                                                    {{ $pretty[$st] ?? $st }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit"
                                                class="px-5 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                                            Salvează
                                        </button>

                                        <div class="text-xs text-gray-500">
                                            După salvare, comanda va apărea automat în secțiunea statusului nou.
                                        </div>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

        </div>
    </div>
</x-app-layout>

