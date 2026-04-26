<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-bold text-gray-900">Moderare selleri</h2>
            <p class="text-sm text-gray-500">Aproba, respinge si verifica separat datele de plata ale sellerilor.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            @if(session('success'))
                <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Total cereri</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="mt-2 text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Approved</div>
                    <div class="mt-2 text-2xl font-bold text-green-700">{{ $stats['approved'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Rejected</div>
                    <div class="mt-2 text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <form method="GET" action="{{ route('admin.seller_applications.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Cautare</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Nume, email, shop, telefon..."
                            class="w-full rounded-xl border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm">
                            <option value="">Toate</option>
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="approved" @selected($status === 'approved')>Approved</option>
                            <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                            Aplica filtre
                        </button>

                        <a href="{{ route('admin.seller_applications.index') }}"
                           class="px-5 py-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Reseteaza
                        </a>
                    </div>
                </form>
            </div>

            @forelse($applications as $app)
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                    $approvedUser = $app->status === 'approved'
                        ? \App\Models\User::where('email', $app->email)->with('sellerProfile.paymentAccount')->first()
                        : null;
                    $approvedPaymentAccount = $approvedUser?->sellerProfile?->paymentAccount;
                @endphp

                <div class="bg-white p-6 rounded-2xl shadow border border-gray-100">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ $app->shop_name ?: 'Fara shop name' }}
                                </h3>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm text-gray-700">
                                <div><span class="font-semibold text-gray-900">Persoana:</span> {{ $app->name }}</div>
                                <div><span class="font-semibold text-gray-900">Email:</span> {{ $app->email }}</div>
                                <div><span class="font-semibold text-gray-900">Telefon:</span> {{ $app->phone ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">Legal name:</span> {{ $app->legal_name ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">Tip seller:</span> {{ $app->seller_type ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">Livrare:</span> {{ $app->delivery_type ?: '—' }}</div>
                                <div class="md:col-span-2 xl:col-span-3"><span class="font-semibold text-gray-900">Adresa ridicare:</span> {{ $app->pickup_address ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">Curier:</span> {{ $app->courier_company ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">IDNP:</span> {{ $app->idnp ?: '—' }}</div>
                                <div><span class="font-semibold text-gray-900">IDNO:</span> {{ $app->company_idno ?: '—' }}</div>
                            </div>

                            @if($app->courier_contract_details)
                                <div class="mt-4 text-sm text-gray-700">
                                    <span class="font-semibold text-gray-900">Detalii contract curier:</span>
                                    {{ $app->courier_contract_details }}
                                </div>
                            @endif

                            @if($app->notes)
                                <div class="mt-4 rounded-xl bg-gray-50 border border-gray-100 p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900 mb-1">Notite</div>
                                    {{ $app->notes }}
                                </div>
                            @endif

                            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-gray-700">
                                <div class="font-semibold text-emerald-900 mb-2">Date pentru plati online</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                                    <div><span class="font-semibold text-gray-900">Activate online:</span> {{ $app->has_online_payments_enabled ? 'Da' : 'Nu' }}</div>
                                    <div><span class="font-semibold text-gray-900">Provider:</span> {{ $app->payment_provider ?: 'none' }}</div>
                                    <div><span class="font-semibold text-gray-900">Contact plati:</span> {{ $app->payment_contact_email ?: '—' }}</div>
                                    <div><span class="font-semibold text-gray-900">Merchant ID:</span> {{ $app->merchant_id ?: '—' }}</div>
                                    <div><span class="font-semibold text-gray-900">Terminal ID:</span> {{ $app->terminal_id ?: '—' }}</div>
                                    <div><span class="font-semibold text-gray-900">IBAN:</span> {{ $app->settlement_iban ?: '—' }}</div>
                                </div>
                                @if($app->payment_notes)
                                    <div class="mt-3">
                                        <span class="font-semibold text-gray-900">Note plati:</span>
                                        {{ $app->payment_notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 text-xs text-gray-500 flex flex-wrap gap-4">
                                <span>Creata: {{ optional($app->created_at)->format('d.m.Y H:i') }}</span>

                                @if($app->approved_at)
                                    <span>Aprobata: {{ $app->approved_at->format('d.m.Y H:i') }}</span>
                                @endif

                                @if($app->rejected_at)
                                    <span>Respinsa: {{ $app->rejected_at->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($app->status === 'pending')
                            <div class="flex flex-col gap-3 w-full lg:w-auto">
                                <form method="POST" action="{{ route('admin.seller_applications.approve', $app->id) }}">
                                    @csrf
                                    <div class="mb-3 space-y-2">
                                        <input
                                            type="password"
                                            name="password"
                                            placeholder="Parola pentru seller"
                                            class="w-full lg:w-52 rounded-xl border-gray-300 text-sm shadow-sm"
                                            required
                                        >
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            placeholder="Confirmare"
                                            class="w-full lg:w-52 rounded-xl border-gray-300 text-sm shadow-sm"
                                            required
                                        >
                                        <p class="text-xs text-gray-500 lg:w-52">
                                            Parola se seteaza doar aici. Dupa aprobare, sellerul o primeste direct pe email.
                                        </p>
                                    </div>
                                    <button class="w-full lg:w-52 bg-green-600 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-green-700">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.seller_applications.reject', $app->id) }}">
                                    @csrf
                                    <button class="w-full lg:w-40 bg-red-600 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-red-700">
                                        Reject
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.seller_applications.destroy', $app->id) }}"
                                      onsubmit="return confirm('Stergi definitiv aceasta cerere? Operatiunea nu poate fi anulata.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full lg:w-40 bg-gray-900 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-black">
                                        Sterge definitiv
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex flex-col gap-3 w-full lg:w-auto">
                                @if($approvedPaymentAccount)
                                    <form method="POST" action="{{ route('admin.seller_applications.payment_account_status', $app) }}" class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <div class="text-sm font-semibold text-gray-900">Status cont plati</div>
                                        <select name="payment_account_status" class="w-full rounded-xl border-gray-300 text-sm shadow-sm">
                                            <option value="missing" @selected($approvedPaymentAccount->status === 'missing')>missing</option>
                                            <option value="pending" @selected($approvedPaymentAccount->status === 'pending')>pending</option>
                                            <option value="active" @selected($approvedPaymentAccount->status === 'active')>active</option>
                                            <option value="rejected" @selected($approvedPaymentAccount->status === 'rejected')>rejected</option>
                                        </select>
                                        <textarea name="payment_account_notes" rows="3" class="w-full rounded-xl border-gray-300 text-sm shadow-sm" placeholder="Nota admin pentru contul de plati">{{ old('payment_account_notes', $approvedPaymentAccount->notes) }}</textarea>
                                        <button class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                            Actualizeaza platile
                                        </button>
                                    </form>
                                @endif

                                <form method="POST"
                                      action="{{ route('admin.seller_applications.destroy', $app->id) }}"
                                      onsubmit="return confirm('Stergi definitiv aceasta intrare? Daca exista cont seller aprobat pe acest email, vor fi sterse si datele sellerului.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full lg:w-48 bg-gray-900 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-black">
                                        Sterge contul definitiv
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-10 rounded-2xl shadow border border-gray-100 text-center text-gray-600">
                    Nu exista cereri pentru filtrele selectate.
                </div>
            @endforelse

            <div>
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
