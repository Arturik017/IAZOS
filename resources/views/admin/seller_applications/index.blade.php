<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-bold text-gray-900">Moderare selleri</h2>
            <p class="text-sm text-gray-500">Aprobă sau respinge cererile sellerilor și filtrează rapid.</p>
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
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Căutare</label>
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
                        <button
                            type="submit"
                            class="px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black"
                        >
                            Aplică filtre
                        </button>

                        <a href="{{ route('admin.seller_applications.index') }}"
                           class="px-5 py-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Resetează
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
                @endphp

                <div class="bg-white p-6 rounded-2xl shadow border border-gray-100">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ $app->shop_name ?: 'Fără shop name' }}
                                </h3>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm text-gray-700">
                                <div>
                                    <span class="font-semibold text-gray-900">Persoană:</span>
                                    {{ $app->name }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Email:</span>
                                    {{ $app->email }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Telefon:</span>
                                    {{ $app->phone ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Legal name:</span>
                                    {{ $app->legal_name ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Tip seller:</span>
                                    {{ $app->seller_type ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Livrare:</span>
                                    {{ $app->delivery_type ?: '—' }}
                                </div>

                                <div class="md:col-span-2 xl:col-span-3">
                                    <span class="font-semibold text-gray-900">Adresă ridicare:</span>
                                    {{ $app->pickup_address ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">Curier:</span>
                                    {{ $app->courier_company ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">IDNP:</span>
                                    {{ $app->idnp ?: '—' }}
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-900">IDNO:</span>
                                    {{ $app->company_idno ?: '—' }}
                                </div>
                            </div>

                            @if($app->courier_contract_details)
                                <div class="mt-4 text-sm text-gray-700">
                                    <span class="font-semibold text-gray-900">Detalii contract curier:</span>
                                    {{ $app->courier_contract_details }}
                                </div>
                            @endif

                            @if($app->notes)
                                <div class="mt-4 rounded-xl bg-gray-50 border border-gray-100 p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900 mb-1">Notițe</div>
                                    {{ $app->notes }}
                                </div>
                            @endif

                            <div class="mt-4 text-xs text-gray-500 flex flex-wrap gap-4">
                                <span>Creată: {{ optional($app->created_at)->format('d.m.Y H:i') }}</span>

                                @if($app->approved_at)
                                    <span>Aprobată: {{ $app->approved_at->format('d.m.Y H:i') }}</span>
                                @endif

                                @if($app->rejected_at)
                                    <span>Respinsă: {{ $app->rejected_at->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($app->status === 'pending')
                            <div class="flex flex-col gap-3 w-full lg:w-auto">
                                <form method="POST" action="{{ route('admin.seller_applications.approve', $app->id) }}">
                                    @csrf
                                    <button class="w-full lg:w-40 bg-green-600 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-green-700">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.seller_applications.reject', $app->id) }}">
                                    @csrf
                                    <button class="w-full lg:w-40 bg-red-600 text-white px-4 py-3 rounded-xl text-sm font-semibold hover:bg-red-700">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-10 rounded-2xl shadow border border-gray-100 text-center text-gray-600">
                    Nu există cereri pentru filtrele selectate.
                </div>
            @endforelse

            <div>
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>