<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Cosul meu</h2>
                <p class="text-sm text-gray-500">
                    Verifica produsele inainte de checkout.
                </p>
            </div>

            <a href="{{ route('home') }}"
               class="text-sm font-semibold text-gray-700 hover:text-gray-900">
                &larr; Inapoi la magazin
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if(empty($cart))
                <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center shadow">
                    <div class="text-lg font-semibold text-gray-900">
                        Cosul este gol.
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        Adauga produse din magazin.
                    </div>

                    <a href="{{ route('home') }}"
                       class="mt-5 inline-block rounded-xl bg-gray-900 px-6 py-3 font-semibold text-white hover:bg-black transition">
                        Mergi la produse
                    </a>
                </div>

            @else
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <div class="space-y-4 lg:col-span-2">
                        @foreach($cart as $rowId => $item)
                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow">
                                <div class="flex flex-col md:flex-row md:items-start gap-4">
                                    <div class="w-full md:w-28 shrink-0">
                                        @php
                                            $cartImage = \App\Support\MediaUrl::public($item['image'] ?? null);
                                        @endphp

                                        @if($cartImage)
                                            <img
                                                src="{{ $cartImage }}"
                                                alt="{{ $item['name'] }}"
                                                class="w-full h-28 object-cover rounded-xl border border-gray-200"
                                            >
                                        @else
                                            <div class="w-full h-28 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center text-sm text-gray-400">
                                                Fara imagine
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $item['name'] }}
                                                </h3>

                                                @if(!empty($item['variant_label']))
                                                    <p class="mt-1 text-sm text-blue-700 font-medium">
                                                        {{ $item['variant_label'] }}
                                                    </p>
                                                @endif

                                                <p class="mt-2 text-sm text-gray-500">
                                                    Pret:
                                                    <span class="font-semibold text-gray-900">
                                                        {{ number_format($item['price'], 2) }} MDL
                                                    </span>
                                                </p>

                                                <p class="mt-1 text-xs text-gray-400">
                                                    Stoc disponibil: {{ $item['stock'] }}
                                                </p>
                                            </div>

                                            <div class="text-right">
                                                <p class="text-sm text-gray-500">Total produs</p>
                                                <p class="text-lg font-bold text-gray-900">
                                                    {{ number_format($item['price'] * $item['qty'], 2) }} MDL
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                            <form method="POST"
                                                  action="{{ route('cart.update', $rowId) }}"
                                                  class="cart-update-form flex items-center gap-2">
                                                @csrf

                                                <label class="text-sm text-gray-600">
                                                    Cantitate
                                                </label>

                                                <input
                                                    type="number"
                                                    name="qty"
                                                    min="1"
                                                    max="{{ $item['stock'] }}"
                                                    value="{{ $item['qty'] }}"
                                                    class="cart-qty-input w-24 rounded-lg border-gray-300 focus:border-gray-400 focus:ring-gray-400"
                                                />

                                                <span class="cart-update-state hidden text-xs font-medium text-gray-500">
                                                    Se actualizeaza...
                                                </span>
                                            </form>

                                            <form method="POST"
                                                  action="{{ route('cart.remove', $rowId) }}">
                                                @csrf
                                                <button
                                                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                                                    Sterge
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="h-fit rounded-2xl border border-gray-100 bg-white p-6 shadow">
                        <h3 class="text-lg font-bold text-gray-900">
                            Sumar comanda
                        </h3>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900">
                                    {{ number_format($subtotal, 2) }} MDL
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Livrare</span>
                                <span class="font-semibold text-green-700">Inclus</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Vama</span>
                                <span class="font-semibold text-green-700">Inclus</span>
                            </div>
                        </div>

                        <div class="my-4 border-t"></div>

                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700">Total</span>
                            <span class="text-2xl font-extrabold text-gray-900">
                                {{ number_format($subtotal, 2) }} MDL
                            </span>
                        </div>

                        <a href="{{ auth()->check() ? route('checkout.index') : route('register') }}"
                           class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-3 font-semibold text-white hover:bg-black transition">
                            Finalizeaza comanda
                        </a>

                        <form method="POST"
                              action="{{ route('cart.clear') }}"
                              class="mt-3">
                            @csrf
                            <button
                                class="w-full rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-900 hover:bg-gray-200 transition">
                                Goleste cosul
                            </button>
                        </form>

                        <p class="mt-4 text-xs text-gray-400">
                            In pasul urmator colectam datele de livrare si confirmam comanda.
                        </p>
                    </div>

                </div>
            @endif
        </div>
    </div>

</x-app-layout>

<script>
    (function () {
        const forms = document.querySelectorAll('.cart-update-form');

        forms.forEach((form) => {
            const input = form.querySelector('.cart-qty-input');
            const state = form.querySelector('.cart-update-state');
            let submitted = false;

            if (!input) {
                return;
            }

            const submitForm = () => {
                if (submitted) {
                    return;
                }

                submitted = true;

                if (state) {
                    state.classList.remove('hidden');
                }

                input.setAttribute('readonly', 'readonly');
                form.requestSubmit();
            };

            input.addEventListener('change', submitForm);
            input.addEventListener('blur', submitForm);
        });
    })();
</script>
