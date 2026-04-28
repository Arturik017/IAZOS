<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Plati vanzatori</h2>
                <p class="text-sm text-gray-500">Fiecare vanzator incaseaza direct plata pentru produsele sale.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Vezi comenzile mele</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">Pasul 1</div>
                        <div class="mt-2 text-lg font-bold text-emerald-900">Date livrare</div>
                        <div class="mt-1 text-sm text-emerald-800">Completate.</div>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-700">Pasul 2</div>
                        <div class="mt-2 text-lg font-bold text-blue-900">Plati vanzatori</div>
                        <div class="mt-1 text-sm text-blue-800">Achiti separat fiecare comanda.</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Pasul 3</div>
                        <div class="mt-2 text-lg font-bold text-gray-800">Confirmare</div>
                        <div class="mt-1 text-sm text-gray-600">Toate comenzile sunt marcate achitate.</div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-5 text-amber-900 shadow-sm">
                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">Plati separate</div>
                <div class="mt-2 text-lg font-semibold">
                    Comanda ta contine produse de la {{ $sellerCount }} vanzatori. Pentru finalizare vor fi necesare {{ $sellerCount }} plati separate.
                </div>
                <p class="mt-2 text-sm leading-6">
                    Pentru transparenta si siguranta, fiecare vanzator incaseaza direct plata pentru produsele sale. De aceea, comanda ta a fost impartita in plati separate.
                </p>
                @if(!$allPaid)
                    <div class="mt-4 rounded-2xl border border-amber-300 bg-white/80 px-4 py-4 text-sm text-amber-900">
                        Nu inchide acest pas pana nu verifici toate cardurile de mai jos. Daca iesi din pagina, nu pierzi comenzile:
                        le gasesti in <span class="font-semibold">Comenzile mele</span>, iar pentru cele neachitate vei vedea butonul <span class="font-semibold">Continua platile</span>.
                    </div>
                @endif
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                @foreach($orders as $order)
                    @php
                        $sellerName = $order->seller?->sellerProfile?->shop_name ?? $order->seller?->name ?? 'Vanzator';
                        $paymentAccount = $order->seller?->sellerProfile?->paymentAccount;
                        $canPay = $paymentAccount?->isReadyForCheckout();
                    @endphp

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Seller</div>
                                <h3 class="mt-1 text-xl font-bold text-gray-900">{{ $sellerName }}</h3>
                                <div class="mt-2 text-sm text-gray-500">Comanda #{{ $order->order_number ?? $order->id }}</div>
                                <div class="mt-1 text-sm text-gray-500">
                                    Status plata:
                                    <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $order->payment_status === 'paid' ? 'Achitat' : 'Neachitat' }}
                                    </span>
                                </div>
                            </div>

                            @if($order->payment_status === 'paid')
                                <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">
                                    Achitat
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                                    Asteapta plata
                                </span>
                            @endif
                        </div>

                        <div class="mt-5 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">Aceasta plata va fi procesata direct catre vanzatorul {{ $sellerName }}.</div>
                        </div>

                        <div class="mt-5 space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-start justify-between gap-4 rounded-2xl border border-gray-100 px-4 py-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->variant_label)
                                            <div class="mt-1 text-sm text-blue-700">{{ $item->variant_label }}</div>
                                        @endif
                                        <div class="mt-1 text-sm text-gray-500">Cantitate: {{ $item->qty }}</div>
                                    </div>
                                    <div class="whitespace-nowrap text-base font-bold text-gray-900">
                                        {{ number_format((float) $item->price * (int) $item->qty, 2, '.', ',') }} MDL
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>Total comanda</span>
                                <span class="text-xl font-bold text-gray-900">{{ number_format((float) $order->subtotal, 2, '.', ',') }} MDL</span>
                            </div>
                        </div>

                        <div class="mt-5">
                            @if($order->payment_status === 'paid')
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-200 bg-white px-5 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
                                    Vezi comanda
                                </a>
                            @elseif($canPay)
                                <div class="space-y-3">
                                    <form method="POST" action="{{ route('checkout.payments.pay', $order) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gray-900 px-5 py-4 text-base font-semibold text-white hover:bg-black">
                                            Mergi la achitare
                                        </button>
                                    </form>

                                    @if(app()->environment('local'))
                                        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-4">
                                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Test local</div>
                                            <div class="mt-2 text-sm text-gray-600">
                                                Daca sandboxul procesatorului nu te lasa sa continui, poti simula local succesul sau esecul ca sa verifici flow-ul cap-coada.
                                            </div>
                                            <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                                                <form method="POST" action="{{ route('checkout.payments.simulate', [$order, 'success']) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                                        Simuleaza plata reusita
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('checkout.payments.simulate', [$order, 'fail']) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">
                                                        Simuleaza plata esuata
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <button type="button" disabled class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-gray-200 px-5 py-4 text-base font-semibold text-gray-500">
                                    Plata indisponibila
                                </button>
                                <p class="mt-3 text-sm text-red-600">
                                    Acest vanzator nu are inca platile online activate. Contacteaza suportul.
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($allPaid)
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-5 text-emerald-900 shadow-sm">
                    Toate platile din acest checkout sunt confirmate. Acum poti urmari fiecare comanda separat in "Comenzile mele".
                </div>
            @endif
        </div>
    </div>

    @if(!$allPaid)
        <script>
            (function () {
                let allowLeave = false;

                document.querySelectorAll('form').forEach(function (form) {
                    form.addEventListener('submit', function () {
                        allowLeave = true;
                    });
                });

                window.addEventListener('beforeunload', function (event) {
                    if (allowLeave) {
                        return;
                    }

                    event.preventDefault();
                    event.returnValue = '';
                });

                document.querySelectorAll('a[href]').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        const href = link.getAttribute('href') || '';

                        if (!href || href.startsWith('#') || allowLeave) {
                            return;
                        }

                        const confirmed = window.confirm('Ai plati nefinalizate in acest checkout. Daca iesi acum, comenzile raman salvate si le poti continua din "Comenzile mele". Vrei sa parasesti pagina?');

                        if (!confirmed) {
                            event.preventDefault();
                        }
                    });
                });
            })();
        </script>
    @endif
</x-app-layout>
