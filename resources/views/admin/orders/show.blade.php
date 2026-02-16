<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Comanda #{{ $order->order_number ?? $order->id }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi la comenzi
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            {{-- INFO COMANDĂ --}}
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Client</div>
                        <div class="font-semibold text-gray-900">
                            {{ $order->first_name }} {{ $order->last_name }}
                        </div>
                        <div class="text-sm text-gray-600">{{ $order->phone }}</div>
                    </div>

                    <div>
                        <div class="font-semibold text-gray-900">
                            {{ $order->district }}, {{ $order->locality }}, {{ $order->street }}
                        </div>

                        @if($order->postal_code)
                            <div class="text-sm text-gray-600">
                                Cod poștal: {{ $order->postal_code }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            {{ number_format($order->subtotal, 2) }} MDL
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="font-semibold text-gray-900">{{ $order->status }}</div>
                    </div>
                </div>

                @if($order->customer_note)
                    <div class="mt-4 p-4 rounded-xl bg-gray-50 border">
                        <div class="text-sm font-semibold text-gray-900">Notă client</div>
                        <div class="text-sm text-gray-700 mt-1">
                            {{ $order->customer_note }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- PRODUSE --}}
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Produse</h3>

                <div class="mt-4 divide-y">
                    @foreach($order->items as $it)
                        <div class="py-3 flex justify-between">
                            <div>
                                <div class="font-semibold">{{ $it->product_name }}</div>
                                <div class="text-sm text-gray-500">Cantitate: {{ $it->qty }}</div>
                            </div>
                            <div class="font-bold">
                                {{ number_format($it->price * $it->qty, 2) }} MDL
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- STATUS COMANDĂ --}}
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <form method="POST"
                      action="{{ route('admin.orders.status', $order) }}"
                      class="flex flex-col sm:flex-row gap-3 items-center">
                    @csrf
                    @method('PATCH')

                    <label class="text-sm font-semibold">Schimbă status:</label>

                    <select name="status" class="rounded-lg border-gray-300 shadow-sm">
                        @foreach(['new','confirmed','processing','shipped','delivered','canceled'] as $st)
                            <option value="{{ $st }}" @selected($order->status === $st)>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>

                    <button class="px-6 py-2 rounded-lg bg-gray-900 text-white font-semibold">
                        Salvează
                    </button>
                </form>
            </div>

            {{-- PLATĂ --}}
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Plată</div>
                <div class="font-semibold">{{ $order->payment_status }}</div>

                @if($order->pay_id)
                    <div class="text-sm text-gray-600">
                        Pay ID: <span class="font-mono">{{ $order->pay_id }}</span>
                    </div>
                @endif
                
                @if($order->pay_id)
                    <form method="POST" action="{{ route('admin.orders.maib_refresh', $order) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm font-semibold">
                            Actualizează status MAIB
                        </button>
                    </form>
                @endif
                
                @if($order->refund_status)
                    <div class="mt-2 text-sm text-gray-600">
                        Refund status: <span class="font-semibold">{{ $order->refund_status }}</span>
                        @if($order->refunded_at) • {{ $order->refunded_at->format('d.m.Y H:i') }} @endif
                    </div>
                @endif

            </div>

            {{-- REFUND --}}
            @if($order->payment_status === 'paid')
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Refund (MAIB)</h3>

                    <form method="POST"
                          action="{{ route('admin.orders.refund', $order) }}"
                          class="mt-4 flex flex-col sm:flex-row gap-3 items-end">
                        @csrf

                        <div class="w-full sm:w-48">
                            <label class="block text-sm font-medium">Sumă (MDL)</label>
                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   value="{{ number_format($order->subtotal, 2, '.', '') }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium">Motiv (opțional)</label>
                            <input name="reason"
                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm"
                                   placeholder="Ex: client a anulat comanda">
                        </div>

                        <button type="submit"
                                onclick="return confirm('Sigur faci refund?')"
                                class="px-6 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">
                            Refund
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
