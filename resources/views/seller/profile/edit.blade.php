<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil Seller
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow rounded">
                <form method="POST" action="{{ route('seller.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume magazin</label>
                        <input name="shop_name" value="{{ old('shop_name', $profile->shop_name) }}" class="mt-1 border w-full p-2 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Denumire juridică / nume complet</label>
                        <input name="legal_name" value="{{ old('legal_name', $profile->legal_name) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input name="phone" value="{{ old('phone', $profile->phone) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresă ridicare</label>
                        <input name="pickup_address" value="{{ old('pickup_address', $profile->pickup_address) }}" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tip seller</label>
                        <select name="seller_type" class="mt-1 border w-full p-2 rounded">
                            <option value="individual" @selected(old('seller_type', $profile->seller_type) === 'individual')>Freelancer / Persoană fizică</option>
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
                        <label class="block text-sm font-medium text-gray-700">Metodă livrare</label>
                        <select name="delivery_type" class="mt-1 border w-full p-2 rounded">
                            <option value="">— alege —</option>
                            <option value="courier" @selected(old('delivery_type', $profile->delivery_type) === 'courier')>Curier</option>
                            <option value="personal" @selected(old('delivery_type', $profile->delivery_type) === 'personal')>Transport personal</option>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Despre magazin / descriere</label>
                        <textarea name="notes" class="mt-1 border w-full p-2 rounded" rows="5">{{ old('notes', $profile->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded">
                        Salvează profilul
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>