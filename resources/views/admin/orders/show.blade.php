<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Comanda #{{ $order->order_number ?? $order->id }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">Detaliu complet marketplace: client, itemi, statusuri logistice si financiare.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Inapoi la comenzi
            </a>
        </div>
    </x-slot>

    @php
        $sellerStatusLabels = [
            'pending' => 'In asteptare',
            'confirmed' => 'Confirmat',
            'processing' => 'In procesare',
            'shipped' => 'Expediat',
            'delivered_pending_review' => 'Livrat, asteapta review admin',
            'cancelled' => 'Anulat',
        ];

        $financialStatusLabels = [
            'unpaid' => 'Nealocat inca',
            'pending' => 'Blocat in pending',
            'available' => 'Disponibil pentru payout',
            'paid' => 'Platit sellerului',
            'cancelled' => 'Anulat',
            'refunded' => 'Rambursat',
        ];

        $releaseLabels = [
            'not_requested' => 'Nu a fost cerut release',
            'pending_review' => 'In review admin',
            'approved' => 'Aprobat',
            'rejected' => 'Respins',
        ];

        $refundRequestLabels = [
            'requested' => 'Trimisa de client',
            'seller_reviewed' => 'Sellerul a raspuns',
            'approved' => 'Aprobata',
            'rejected' => 'Respinsa',
        ];

        $totalGross = (float) $order->items->sum(fn ($item) => (float) ($item->gross_amount ?? ((float) $item->price * (int) $item->qty)));
        $totalCommission = (float) $order->items->sum(fn ($item) => (float) ($item->platform_commission_amount ?? 0));
        $totalSellerNet = (float) $order->items->sum(fn ($item) => (float) ($item->seller_net_amount ?? 0));
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <div class="font-semibold">Exista erori:</div>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr,0.8fr]">
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Date client</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-500">Nume</div>
                            <div class="font-semibold text-gray-900">{{ $order->first_name }} {{ $order->last_name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Telefon</div>
                            <div class="font-semibold text-gray-900">{{ $order->phone }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="text-sm text-gray-500">Adresa</div>
                            <div class="font-semibold text-gray-900">
                                {{ $order->district }}, {{ $order->locality }}, {{ $order->street }}
                            </div>
                            @if($order->postal_code)
                                <div class="mt-1 text-sm text-gray-500">Cod postal: {{ $order->postal_code }}</div>
                            @endif
                        </div>
                    </div>

                    @if($order->customer_note)
                        <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-sm font-semibold text-gray-900">Nota client</div>
                            <div class="mt-1 text-sm text-gray-700">{{ $order->customer_note }}</div>
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Rezumat comanda</h3>
                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-gray-50 p-4">
                                <div class="text-sm text-gray-500">Status comanda</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $order->status }}</div>
                            </div>
                            <div class="rounded-2xl bg-gray-50 p-4">
                                <div class="text-sm text-gray-500">Payment status</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $order->payment_status }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="text-xs uppercase tracking-[0.2em] text-gray-400">Brut itemi</div>
                                <div class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalGross, 2, '.', ',') }} MDL</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="text-xs uppercase tracking-[0.2em] text-gray-400">Comision platforma</div>
                                <div class="mt-2 text-xl font-bold text-sky-600">{{ number_format($totalCommission, 2, '.', ',') }} MDL</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="text-xs uppercase tracking-[0.2em] text-gray-400">Net selleri</div>
                                <div class="mt-2 text-xl font-bold text-emerald-600">{{ number_format($totalSellerNet, 2, '.', ',') }} MDL</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="rounded-2xl border border-gray-200 p-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Status general comanda</label>
                                    <select name="status" class="mt-1 w-full rounded-xl border-gray-300 shadow-sm">
                                        @foreach(['new','confirmed','processing','shipped','delivered','canceled'] as $st)
                                            <option value="{{ $st }}" @selected($order->status === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="rounded-xl bg-gray-900 px-5 py-3 font-semibold text-white hover:bg-black">
                                    Salveaza status
                                </button>
                            </div>
                        </form>

                        @if($order->pay_id)
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="text-sm text-gray-500">Pay ID</div>
                                <div class="mt-1 break-all font-mono text-sm text-gray-900">{{ $order->pay_id }}</div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.orders.maib_refresh', $order) }}">
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                                            Actualizeaza status MAIB
                                        </button>
                                    </form>
                                    @if($order->payment_status === 'paid')
                                        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" class="flex flex-wrap items-end gap-2">
                                            @csrf
                                            <input type="number" step="0.01" name="amount" value="{{ number_format($order->subtotal, 2, '.', '') }}" class="rounded-xl border-gray-300 text-sm shadow-sm">
                                            <input name="reason" placeholder="Motiv refund MAIB" class="rounded-xl border-gray-300 text-sm shadow-sm">
                                            <button type="submit" onclick="return confirm('Sigur faci refund total prin MAIB?')" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                Refund MAIB
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Itemi marketplace</h3>
                        <p class="mt-1 text-sm text-gray-500">Fiecare produs este urmarit separat logistic si financiar.</p>
                    </div>
                    <div class="text-sm text-gray-500">{{ $order->items->count() }} itemi</div>
                </div>

                <div class="mt-6 space-y-5">
                    @foreach($order->items as $item)
                        <div class="rounded-3xl border border-gray-200 p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->variant_label)
                                            <div class="mt-1 text-sm text-gray-500">{{ $item->variant_label }}</div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                        <div class="rounded-2xl bg-gray-50 p-3">
                                            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Seller</div>
                                            <div class="mt-2 font-semibold text-gray-900">{{ $item->seller?->sellerProfile?->shop_name ?? $item->seller?->name ?? '—' }}</div>
                                        </div>
                                        <div class="rounded-2xl bg-gray-50 p-3">
                                            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Logistic</div>
                                            <div class="mt-2 font-semibold text-gray-900">{{ $sellerStatusLabels[$item->seller_status] ?? $item->seller_status }}</div>
                                        </div>
                                        <div class="rounded-2xl bg-gray-50 p-3">
                                            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Financiar</div>
                                            <div class="mt-2 font-semibold text-gray-900">{{ $financialStatusLabels[$item->financial_status] ?? $item->financial_status }}</div>
                                        </div>
                                        <div class="rounded-2xl bg-gray-50 p-3">
                                            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Review admin</div>
                                            <div class="mt-2 font-semibold text-gray-900">{{ $releaseLabels[$item->admin_release_status] ?? $item->admin_release_status }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="rounded-2xl border border-gray-200 p-4">
                                            <div class="text-sm text-gray-500">Brut</div>
                                            <div class="mt-1 text-xl font-bold text-gray-900">{{ number_format((float) ($item->gross_amount ?? ((float) $item->price * (int) $item->qty)), 2, '.', ',') }} MDL</div>
                                        </div>
                                        <div class="rounded-2xl border border-gray-200 p-4">
                                            <div class="text-sm text-gray-500">Comision platforma</div>
                                            <div class="mt-1 text-xl font-bold text-sky-600">{{ number_format((float) ($item->platform_commission_amount ?? 0), 2, '.', ',') }} MDL</div>
                                        </div>
                                        <div class="rounded-2xl border border-gray-200 p-4">
                                            <div class="text-sm text-gray-500">Net seller</div>
                                            <div class="mt-1 text-xl font-bold text-emerald-600">{{ number_format((float) ($item->seller_net_amount ?? 0), 2, '.', ',') }} MDL</div>
                                        </div>
                                    </div>

                                    @if($item->admin_release_note || $item->refund_reason)
                                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                            @if($item->admin_release_note)
                                                <div><span class="font-semibold">Nota admin release:</span> {{ $item->admin_release_note }}</div>
                                            @endif
                                            @if($item->refund_reason)
                                                <div class="mt-1"><span class="font-semibold">Motiv anulare/refund:</span> {{ $item->refund_reason }}</div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($item->refundRequest)
                                        <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-900">
                                            <div class="font-semibold">Solicitare refund de la client</div>
                                            <div class="mt-2">Status: {{ $refundRequestLabels[$item->refundRequest->status] ?? $item->refundRequest->status }}</div>
                                            <div class="mt-1">Tip cerere: {{ $item->refundRequest->target_status === 'cancelled' ? 'Anulare' : 'Rambursare' }}</div>
                                            <div class="mt-1">Motiv client: {{ $item->refundRequest->client_reason }}</div>
                                            @if($item->refundRequest->client_note)
                                                <div class="mt-1">Detalii client: {{ $item->refundRequest->client_note }}</div>
                                            @endif
                                            @if($item->refundRequest->seller_response)
                                                <div class="mt-2 rounded-xl bg-white/70 px-3 py-2">
                                                    <div class="font-semibold">Raspuns seller</div>
                                                    <div class="mt-1">{{ $item->refundRequest->seller_response }}</div>
                                                    @if($item->refundRequest->seller_recommended_status)
                                                        <div class="mt-1 text-xs">Recomandare seller: {{ $item->refundRequest->seller_recommended_status }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if($item->refundRequest->admin_decision_note)
                                                <div class="mt-2 text-xs">Nota decizie: {{ $item->refundRequest->admin_decision_note }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="w-full max-w-md space-y-4 xl:w-[420px]">
                                    @if($item->admin_release_status === 'pending_review')
                                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                            <div class="text-sm font-semibold text-emerald-900">Release pending review</div>
                                            <p class="mt-1 text-sm text-emerald-800">Sellerul a raportat livrarea. Banii sunt inca blocati pana la decizia adminului.</p>
                                            <div class="mt-3 flex flex-col gap-3">
                                                <form method="POST" action="{{ route('admin.finance.order_items.approve_release', $item) }}" class="space-y-2">
                                                    @csrf
                                                    <input name="admin_release_note" placeholder="Nota optionala" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                    <button class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                                        Aproba si muta in available
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.finance.order_items.reject_release', $item) }}" class="space-y-2">
                                                    @csrf
                                                    <input name="admin_release_note" required placeholder="Motiv respingere" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                    <button class="w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white hover:bg-amber-600">
                                                        Respinge si cere reverificare
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!in_array($item->financial_status, ['cancelled', 'refunded'], true))
                                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                                            <div class="text-sm font-semibold text-red-900">Anulare / refund pe item</div>
                                            <p class="mt-1 text-sm text-red-800">Foloseste anulare inainte de livrare si refund dupa plata/livrare, fara sa afectezi ceilalti selleri din comanda.</p>
                                            <form method="POST" action="{{ route('admin.finance.order_items.refund', $item) }}" class="mt-3 space-y-2">
                                                @csrf
                                                <select name="target_status" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                    <option value="cancelled">Anulat</option>
                                                    <option value="refunded">Rambursat</option>
                                                </select>
                                                <input name="refund_reason" required placeholder="Motiv anulare sau refund" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                <button class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">
                                                    Aplica ajustarea pe item
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                                            Itemul este deja inchis financiar ca <span class="font-semibold">{{ $financialStatusLabels[$item->financial_status] ?? $item->financial_status }}</span>.
                                        </div>
                                    @endif

                                    @if($item->refundRequest && !in_array($item->refundRequest->status, ['approved', 'rejected'], true))
                                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                                            <div class="text-sm font-semibold text-indigo-900">Decizie pe solicitarea clientului</div>
                                            <div class="mt-3 flex flex-col gap-3">
                                                <form method="POST" action="{{ route('admin.refund_requests.approve', $item->refundRequest) }}" class="space-y-2">
                                                    @csrf
                                                    <select name="target_status" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                                        <option value="{{ $item->refundRequest->seller_recommended_status ?: $item->refundRequest->target_status }}">
                                                            {{ ($item->refundRequest->seller_recommended_status ?: $item->refundRequest->target_status) === 'cancelled' ? 'Aproba ca anulare' : 'Aproba ca refund' }}
                                                        </option>
                                                        <option value="{{ ($item->refundRequest->seller_recommended_status ?: $item->refundRequest->target_status) === 'cancelled' ? 'refunded' : 'cancelled' }}">
                                                            {{ (($item->refundRequest->seller_recommended_status ?: $item->refundRequest->target_status) === 'cancelled') ? 'Aproba ca refund' : 'Aproba ca anulare' }}
                                                        </option>
                                                    </select>
                                                    <textarea name="admin_decision_note" rows="3" placeholder="Nota decizie admin" class="w-full rounded-xl border-gray-300 text-sm shadow-sm"></textarea>
                                                    <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                                                        Aproba solicitarea clientului
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.refund_requests.reject', $item->refundRequest) }}" class="space-y-2">
                                                    @csrf
                                                    <textarea name="admin_decision_note" rows="3" required placeholder="De ce respingi cererea" class="w-full rounded-xl border-gray-300 text-sm shadow-sm"></textarea>
                                                    <button class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-black">
                                                        Respinge solicitarea
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
