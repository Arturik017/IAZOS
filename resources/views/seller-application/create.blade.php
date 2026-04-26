<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Devino seller
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 md:p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-xl bg-red-100 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('seller.application.store') }}" class="space-y-6">
                    @csrf

                    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                        Dupa aprobare, adminul iti va seta parola si o vei primi pe email impreuna cu confirmarea accesului seller.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nume persoana de contact</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-gray-300" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-gray-300" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Telefon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-gray-300">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Numele magazinului</label>
                            <input type="text" name="shop_name" value="{{ old('shop_name') }}" class="w-full rounded-xl border-gray-300" required>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Denumire juridica / Nume complet</label>
                        <input type="text" name="legal_name" value="{{ old('legal_name') }}" class="w-full rounded-xl border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Tip seller</label>
                            <select name="seller_type" class="w-full rounded-xl border-gray-300" required>
                                <option value="individual" @selected(old('seller_type') === 'individual')>Persoana fizica</option>
                                <option value="freelancer" @selected(old('seller_type') === 'freelancer')>Freelancer</option>
                                <option value="company" @selected(old('seller_type') === 'company')>Companie</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Adresa de ridicare</label>
                            <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">IDNP pentru persoana fizica / freelancer</label>
                            <input type="text" name="idnp" value="{{ old('idnp') }}" class="w-full rounded-xl border-gray-300">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">IDNO pentru companie</label>
                            <input type="text" name="company_idno" value="{{ old('company_idno') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Metoda livrare</label>
                            <select name="delivery_type" class="w-full rounded-xl border-gray-300" required>
                                <option value="courier" @selected(old('delivery_type') === 'courier')>Companie de curierat</option>
                                <option value="personal" @selected(old('delivery_type') === 'personal')>Livrare proprie</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Companie curier</label>
                            <input type="text" name="courier_company" value="{{ old('courier_company') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Detalii contract curier</label>
                        <textarea name="courier_contract_details" rows="4" class="w-full rounded-xl border-gray-300">{{ old('courier_contract_details') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Note suplimentare</label>
                        <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-300">{{ old('notes') }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-emerald-900">Date pentru plati online</h3>
                            <p class="mt-1 text-sm text-emerald-800">
                                Pentru a primi plati online direct de la clienti, trebuie sa ai activat un serviciu e-commerce/payment link la un procesator de plati.
                                Platforma nu incaseaza banii produselor in locul vanzatorului.
                            </p>
                        </div>

                        <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-800">
                            <input type="checkbox" name="has_online_payments_enabled" value="1" class="rounded border-gray-300" @checked(old('has_online_payments_enabled'))>
                            <span>Am platile online activate si vreau sa primesc banii direct de la clienti</span>
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Payment provider</label>
                                <select name="payment_provider" class="w-full rounded-xl border-gray-300">
                                    <option value="none" @selected(old('payment_provider', 'none') === 'none')>none</option>
                                    <option value="maib" @selected(old('payment_provider') === 'maib')>maib</option>
                                    <option value="paynet" @selected(old('payment_provider') === 'paynet')>paynet</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Payment contact email</label>
                                <input type="email" name="payment_contact_email" value="{{ old('payment_contact_email') }}" class="w-full rounded-xl border-gray-300">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Merchant ID / Project ID</label>
                                <input type="text" name="merchant_id" value="{{ old('merchant_id') }}" class="w-full rounded-xl border-gray-300">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Terminal ID</label>
                                <input type="text" name="terminal_id" value="{{ old('terminal_id') }}" class="w-full rounded-xl border-gray-300">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">API key</label>
                                <input type="text" name="api_key" value="{{ old('api_key') }}" class="w-full rounded-xl border-gray-300">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Secret key</label>
                                <input type="text" name="secret_key" value="{{ old('secret_key') }}" class="w-full rounded-xl border-gray-300">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Settlement IBAN</label>
                            <input type="text" name="settlement_iban" value="{{ old('settlement_iban') }}" class="w-full rounded-xl border-gray-300">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Payment notes</label>
                            <textarea name="payment_notes" rows="4" class="w-full rounded-xl border-gray-300">{{ old('payment_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center rounded-xl bg-black text-white px-6 py-3 hover:opacity-90 transition">
                            Trimite cererea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
