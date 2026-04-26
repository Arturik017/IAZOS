<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Finalizeaza comanda</h2>
                <p class="text-sm text-gray-500">Completezi o singura data livrarea, iar dupa aceea mergi pe platile separate ale vanzatorilor.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">Pasul 1</div>
                        <div class="mt-2 text-lg font-bold text-emerald-900">Date livrare</div>
                        <div class="mt-1 text-sm text-emerald-800">Completezi datele o singura data.</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Pasul 2</div>
                        <div class="mt-2 text-lg font-bold text-gray-800">Plati vanzatori</div>
                        <div class="mt-1 text-sm text-gray-600">Dupa plasare, comanda se imparte automat pe selleri.</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Pasul 3</div>
                        <div class="mt-2 text-lg font-bold text-gray-800">Confirmare</div>
                        <div class="mt-1 text-sm text-gray-600">Revii aici dupa ce sellerii confirma platile.</div>
                    </div>
                </div>
            </div>

            @if(($sellerCount ?? 1) > 1)
                <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-5 text-amber-900 shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">Atentie la plati</div>
                    <div class="mt-2 text-lg font-semibold">
                        Comanda ta contine produse de la {{ $sellerCount }} vanzatori.
                    </div>
                    <p class="mt-2 text-sm leading-6">
                        Pentru transparenta si siguranta, fiecare vanzator incaseaza direct plata pentru produsele sale.
                        Dupa ce apesi pe "Plaseaza comanda", vei vedea {{ $sellerCount }} carduri separate de achitare.
                    </p>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1.3fr,0.9fr]">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900">Date de livrare</h3>
                    <p class="mt-1 text-sm text-gray-500">Aceste date vor fi copiate automat pe fiecare comanda creata per seller.</p>

                    <form method="POST" action="{{ route('checkout.store') }}" class="mt-6 space-y-5">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nume</label>
                                <input name="first_name" value="{{ old('first_name') }}" class="w-full rounded-2xl border-gray-300 shadow-sm">
                                @error('first_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Prenume</label>
                                <input name="last_name" value="{{ old('last_name') }}" class="w-full rounded-2xl border-gray-300 shadow-sm">
                                @error('last_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Telefon</label>
                            <input name="phone" value="{{ old('phone') }}" placeholder="+373..." class="w-full rounded-2xl border-gray-300 shadow-sm">
                            @error('phone') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <div x-data="mdLocations(@js($districts), @js($localitiesMap))" class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Raion</label>
                                <select name="district" x-model="district" x-on:change="onDistrictChange()" class="w-full rounded-2xl border-gray-300 shadow-sm">
                                    <option value="">Alege raionul...</option>
                                    <template x-for="d in districts" :key="d">
                                        <option :value="d" x-text="d"></option>
                                    </template>
                                </select>
                                @error('district') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Oras / Sat</label>
                                <input type="text" x-model="search" :disabled="!district" placeholder="Cauta localitatea..." class="w-full rounded-2xl border-gray-300 shadow-sm disabled:bg-gray-100">
                                <select name="locality" x-model="locality" :disabled="!district" class="mt-2 w-full rounded-2xl border-gray-300 shadow-sm disabled:bg-gray-100">
                                    <option value="">Alege localitatea...</option>
                                    <template x-for="l in filteredLocalities" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                                @error('locality') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Strada / Adresa</label>
                            <input name="street" value="{{ old('street') }}" placeholder="Ex: Stefan cel Mare 10, ap. 5" class="w-full rounded-2xl border-gray-300 shadow-sm">
                            @error('street') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Cod postal (optional)</label>
                            <input name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-2xl border-gray-300 shadow-sm">
                            @error('postal_code') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Comentariu (optional)</label>
                            <textarea name="customer_note" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm">{{ old('customer_note') }}</textarea>
                            @error('customer_note') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                            <input id="accept_terms" name="accept_terms" type="checkbox" value="1" class="mt-1 rounded border-gray-300">
                            <span class="text-sm leading-6 text-gray-700">
                                Sunt de acord cu
                                <a href="{{ route('terms') }}" target="_blank" class="font-semibold text-gray-900 underline">
                                    Termenii si conditiile
                                </a>
                                si inteleg ca produsele pot fi achitate separat in functie de vanzator.
                            </span>
                        </label>
                        @error('accept_terms') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gray-900 px-6 py-4 text-base font-semibold text-white hover:bg-black">
                            Plaseaza comanda
                        </button>
                    </form>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm lg:sticky lg:top-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">Rezumat cos</h3>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ count($cart) }} produse</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach($cart as $item)
                            <div class="flex items-start justify-between gap-4 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900">{{ $item['name'] }}</div>
                                    @if(!empty($item['variant_label']))
                                        <div class="mt-1 text-sm text-blue-700">{{ $item['variant_label'] }}</div>
                                    @endif
                                    <div class="mt-1 text-sm text-gray-500">Cantitate: {{ $item['qty'] }}</div>
                                </div>
                                <div class="whitespace-nowrap text-base font-bold text-gray-900">
                                    {{ number_format((float) $item['price'] * (int) $item['qty'], 2, '.', ',') }} MDL
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Subtotal cos</span>
                            <span>{{ number_format((float) $subtotal, 2, '.', ',') }} MDL</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xl font-bold text-gray-900">
                            <span>Total de impartit pe selleri</span>
                            <span>{{ number_format((float) $subtotal, 2, '.', ',') }} MDL</span>
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm text-blue-900">
                        Dupa plasare vei vedea carduri separate de plata, cate unul pentru fiecare seller din cos.
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    return this.localities.filter(x => (x || '').toLowerCase().includes(q));
                },
                onDistrictChange() {
                    this.search = '';
                    this.locality = '';
                },
            }
        }
    </script>
</x-app-layout>
