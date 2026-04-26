<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil Seller
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow rounded">
                <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-col gap-5 md:flex-row md:items-center">
                            <div class="h-24 w-24 overflow-hidden rounded-full border border-gray-200 bg-white">
                                @if($profile->avatar_path)
                                    <img src="{{ \App\Support\MediaUrl::public($profile->avatar_path) }}"
                                         alt="{{ $profile->shop_name }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-gray-500">
                                        {{ strtoupper(mb_substr($profile->shop_name ?: auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Poza profil seller</label>
                                <input type="file" name="avatar" accept="image/*" class="mt-2 block w-full rounded">
                                <p class="mt-2 text-xs text-gray-500">
                                    Imagine patrata sau apropiata de patrat pentru afisare rotunda pe site.
                                </p>

                                @if($profile->avatar_path)
                                    <label class="mt-3 inline-flex items-center gap-2 text-sm text-red-600">
                                        <input type="checkbox" name="remove_avatar" value="1">
                                        <span>Sterge poza actuala</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume magazin</label>
                        <input name="shop_name" value="{{ old('shop_name', $profile->shop_name) }}" class="mt-1 border w-full p-2 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Denumire juridica / nume complet</label>
                        <input name="legal_name" value="{{ old('legal_name', $profile->legal_name) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input name="phone" value="{{ old('phone', $profile->phone) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresa ridicare</label>
                        <input name="pickup_address" value="{{ old('pickup_address', $profile->pickup_address) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tip seller</label>
                        <select name="seller_type" class="mt-1 border w-full p-2 rounded">
                            <option value="individual" @selected(old('seller_type', $profile->seller_type) === 'individual')>Persoana fizica</option>
                            <option value="freelancer" @selected(old('seller_type', $profile->seller_type) === 'freelancer')>Freelancer</option>
                            <option value="company" @selected(old('seller_type', $profile->seller_type) === 'company')>Companie</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">IDNP</label>
                        <input name="idnp" value="{{ old('idnp', $profile->idnp) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">IDNO</label>
                        <input name="company_idno" value="{{ old('company_idno', $profile->company_idno) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Metoda livrare</label>
                        <select name="delivery_type" class="mt-1 border w-full p-2 rounded">
                            <option value="">— alege —</option>
                            <option value="courier" @selected(old('delivery_type', $profile->delivery_type) === 'courier')>Curier</option>
                            <option value="personal" @selected(old('delivery_type', $profile->delivery_type) === 'personal')>Livrare proprie</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Companie de curierat</label>
                        <input name="courier_company" value="{{ old('courier_company', $profile->courier_company) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Detalii contract curierat</label>
                        <textarea name="courier_contract_details" class="mt-1 border w-full p-2 rounded">{{ old('courier_contract_details', $profile->courier_contract_details) }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-emerald-900">Date pentru plati online</h3>
                                <p class="mt-1 text-sm text-emerald-800">
                                    Clientii platesc direct catre tine. Completeaza datele procesatorului tau de plati si ale contului de decontare.
                                </p>
                            </div>
                            <div class="rounded-xl bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700">
                                Status: {{ strtoupper($paymentAccount->status ?? 'missing') }}
                            </div>
                        </div>

                        <div class="mt-4 space-y-4">
                            <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-800">
                                <input type="checkbox" name="has_online_payments_enabled" value="1" class="rounded border-gray-300" @checked(old('has_online_payments_enabled', ($paymentAccount->provider ?? 'none') !== 'none'))>
                                <span>Am platile online activate si vreau sa primesc plata direct de la clienti</span>
                            </label>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment provider</label>
                                    <select name="payment_provider" class="mt-1 border w-full p-2 rounded">
                                        <option value="none" @selected(old('payment_provider', $paymentAccount->provider ?? 'none') === 'none')>none</option>
                                        <option value="maib" @selected(old('payment_provider', $paymentAccount->provider) === 'maib')>maib</option>
                                        <option value="paynet" @selected(old('payment_provider', $paymentAccount->provider) === 'paynet')>paynet</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment contact email</label>
                                    <input name="payment_contact_email" value="{{ old('payment_contact_email', $paymentAccount->payment_contact_email) }}" class="mt-1 border w-full p-2 rounded">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Merchant ID / Project ID</label>
                                    <input name="merchant_id" value="{{ old('merchant_id', $paymentAccount->merchant_id) }}" class="mt-1 border w-full p-2 rounded">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Terminal ID</label>
                                    <input name="terminal_id" value="{{ old('terminal_id', $paymentAccount->terminal_id) }}" class="mt-1 border w-full p-2 rounded">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">API key</label>
                                    <input name="api_key" value="" class="mt-1 border w-full p-2 rounded" placeholder="{{ $paymentAccount->api_key ? 'Completata deja - lasa gol pentru a o pastra' : '' }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Secret key</label>
                                    <input name="secret_key" value="" class="mt-1 border w-full p-2 rounded" placeholder="{{ $paymentAccount->secret_key ? 'Completata deja - lasa gol pentru a o pastra' : '' }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Settlement IBAN</label>
                                <input name="settlement_iban" value="{{ old('settlement_iban', $paymentAccount->settlement_iban) }}" class="mt-1 border w-full p-2 rounded" placeholder="MD..">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment notes</label>
                                <textarea name="payment_notes" class="mt-1 border w-full p-2 rounded" rows="4">{{ old('payment_notes', $paymentAccount->notes) }}</textarea>
                            </div>

                            <div class="rounded-xl border border-emerald-100 bg-white px-4 py-3 text-sm text-gray-700">
                                Dupa orice schimbare importanta, statusul platilor revine in <strong>pending</strong> pana cand adminul confirma configurarea.
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Despre magazin / descriere</label>
                        <textarea name="notes" class="mt-1 border w-full p-2 rounded" rows="5">{{ old('notes', $profile->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded">
                        Salveaza profilul
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
