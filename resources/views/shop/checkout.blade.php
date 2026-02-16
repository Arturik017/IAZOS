<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">
            Finalizează comanda
        </h2>
    </x-slot>

    {{-- ================= PAGE CONTENT ================= --}}
    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ================================================== --}}
            {{-- LEFT COLUMN: CHECKOUT FORM --}}
            {{-- ================================================== --}}
            <div class="lg:col-span-7 space-y-6">

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Date livrare
                    </h3>

                    {{-- ================= FORM ================= --}}
                    <form
                        method="POST"
                        action="{{ route('checkout.store') }}"
                        class="mt-5 space-y-4"
                    >
                        @csrf

                        {{-- ===== NUME + PRENUME ===== --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Nume
                                </label>
                                <input
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                >
                                @error('first_name')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Prenume
                                </label>
                                <input
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                >
                                @error('last_name')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== TELEFON ===== --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Număr de telefon
                            </label>
                            <input
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="+373..."
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            >
                            @error('phone')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== RAION + LOCALITATE ===== --}}
                        <div
                            x-data="mdLocations(@js($districts), @js($localitiesMap))"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                        >
                            {{-- RAION --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Raion
                                </label>
                        
                                <select
                                    name="district"
                                    x-model="district"
                                    @change="onDistrictChange()"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                    required
                                >
                                    <option value="">Alege raionul...</option>
                                    <template x-for="d in districts" :key="d">
                                        <option :value="d" x-text="d"></option>
                                    </template>
                                </select>
                        
                                @error('district')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        
                            {{-- LOCALITATE --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Oraș / Sat
                                </label>
                        
                                <input
                                    type="text"
                                    x-model="search"
                                    :disabled="!district"
                                    placeholder="Caută după litere..."
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200 disabled:bg-gray-100"
                                />
                        
                                <select
                                    name="locality"
                                    x-model="locality"
                                    :disabled="!district"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200 disabled:bg-gray-100"
                                    required
                                >
                                    <option value="">Alege localitatea...</option>
                                    <template x-for="l in filteredLocalities" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                        
                                @error('locality')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== STRADA ===== --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Stradă / Adresă
                            </label>
                            <input
                                name="street"
                                value="{{ old('street') }}"
                                placeholder="Ex: str. Ștefan cel Mare 10, ap. 5"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                required
                            >
                            @error('street')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== COD POSTAL ===== --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Cod poștal (opțional)
                            </label>
                            <input
                                name="postal_code"
                                value="{{ old('postal_code') }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            >
                            @error('postal_code')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== COMENTARIU ===== --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Comentariu (opțional)
                            </label>
                            <textarea
                                name="customer_note"
                                rows="3"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            >{{ old('customer_note') }}</textarea>
                            @error('customer_note')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== TERMENI ===== --}}
                        <div class="flex items-start gap-3">
                            <input
                                id="accept_terms"
                                name="accept_terms"
                                type="checkbox"
                                value="1"
                                class="mt-1 rounded border-gray-300"
                                required
                            >
                            <label for="accept_terms" class="text-sm text-gray-700">
                                Sunt de acord cu
                                <a
                                    href="{{ route('terms') }}"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                >
                                    Termenii și condițiile
                                </a>
                                (inclusiv condițiile MAIB).
                            </label>
                        </div>
                        @error('accept_terms')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror

                        {{-- ===== SUBMIT ===== --}}
                        <button
                            type="submit"
                            class="w-full px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
                        >
                            Plasează comanda
                        </button>
                    </form>

                    {{-- ===== PAYMENT ICONS ===== --}}
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <img src="{{ asset('images/payments/visa.svg') }}" alt="Visa" class="h-7">
                        <img src="{{ asset('images/payments/mastercard.svg') }}" alt="Mastercard" class="h-7">
                        <img src="{{ asset('images/payments/google-pay.svg') }}" alt="Google Pay" class="h-7">
                        <img src="{{ asset('images/payments/apple-pay.svg') }}" alt="Apple Pay" class="h-7">
                    </div>
                </div>
                
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

            </div>

            {{-- ================================================== --}}
            {{-- RIGHT COLUMN: CART SUMMARY --}}
            {{-- ================================================== --}}
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 lg:sticky lg:top-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Coșul tău
                    </h3>

                    <div class="mt-4 space-y-3 max-h-[420px] overflow-auto pr-1">
                        @foreach($cart as $item)
                            <div class="flex items-start justify-between gap-3 border-b pb-3">
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $item['name'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Cantitate: {{ $item['qty'] }}
                                    </div>
                                </div>

                                <div class="font-bold text-gray-900 whitespace-nowrap">
                                    {{ number_format($item['price'] * $item['qty'], 2) }} MDL
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-between text-lg font-extrabold">
                        <span>Total</span>
                        <span>{{ number_format($subtotal, 2) }} MDL</span>
                    </div>

                    <div class="mt-3 text-xs text-gray-500">
                        * Livrare în Republica Moldova (fără Transnistria).
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= ALPINE SCRIPT ================= --}}
    <script>
        function mdLocations(districts, localitiesMap) {
            return {
                districts: Array.isArray(districts) ? districts : [],
                localitiesMap: localitiesMap && typeof localitiesMap === 'object' ? localitiesMap : {},

                district: @js(old('district', '')),
                locality: @js(old('locality', '')),
                search: '',

                get localities() {
                    if (!this.district) return [];
                    return this.localitiesMap[this.district] || [];
                },

                get filteredLocalities() {
                    const q = (this.search || '').toLowerCase().trim();
                    if (!q) return this.localities;
                    return this.localities.filter(x =>
                        (x || '').toLowerCase().includes(q)
                    );
                },

                onDistrictChange() {
                    this.search = '';
                    this.locality = '';
                },
            }
        }
                document.addEventListener('DOMContentLoaded', function () {
            if (!window.grecaptcha) return;
        
            grecaptcha.ready(function () {
                grecaptcha.execute("{{ config('recaptcha.site_key') }}", {action: 'checkout'}).then(function (token) {
                    const el = document.getElementById('recaptcha_token');
                    if (el) el.value = token;
                });
            });
        });
    </script>

</x-app-layout>
