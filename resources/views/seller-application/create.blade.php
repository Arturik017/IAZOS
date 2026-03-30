<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Devino seller
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nume persoană de contact</label>
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
                        <label class="block mb-2 text-sm font-medium text-gray-700">Denumire juridică / Nume complet</label>
                        <input type="text" name="legal_name" value="{{ old('legal_name') }}" class="w-full rounded-xl border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Tip seller</label>
                            <select name="seller_type" class="w-full rounded-xl border-gray-300" required>
                                <option value="individual" @selected(old('seller_type') === 'individual')>Freelancer / Persoană fizică</option>
                                <option value="company" @selected(old('seller_type') === 'company')>Companie</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Adresă de ridicare</label>
                            <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">IDNP (pentru freelancer)</label>
                            <input type="text" name="idnp" value="{{ old('idnp') }}" class="w-full rounded-xl border-gray-300">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">IDNO (pentru companie)</label>
                            <input type="text" name="company_idno" value="{{ old('company_idno') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Metodă livrare</label>
                            <select name="delivery_type" class="w-full rounded-xl border-gray-300" required>
                                <option value="courier" @selected(old('delivery_type') === 'courier')>Companie de curierat</option>
                                <option value="personal" @selected(old('delivery_type') === 'personal')>Transport personal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Companie de curierat</label>
                            <input type="text" name="courier_company" value="{{ old('courier_company') }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Detalii contract curierat</label>
                        <textarea name="courier_contract_details" rows="4" class="w-full rounded-xl border-gray-300">{{ old('courier_contract_details') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Note suplimentare</label>
                        <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-300">{{ old('notes') }}</textarea>
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