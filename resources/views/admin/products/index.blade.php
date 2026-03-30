<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between w-full">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                    Moderare produse
                </h2>
                <p class="text-sm text-gray-500">
                    Administrează produsele platformei și produsele sellerilor.
                </p>
            </div>

            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-black">
                + Adaugă produs
            </a>
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
                    <div class="text-sm text-gray-500">Total produse</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">În așteptare</div>
                    <div class="mt-2 text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Produse seller aprobate</div>
                    <div class="mt-2 text-2xl font-bold text-green-700">{{ $stats['approved_seller_products'] }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Produse admin</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['admin_products'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                    <div class="xl:col-span-2">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Căutare</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Produs, seller, shop..."
                            class="w-full rounded-xl border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Owner</label>
                        <select name="owner" class="w-full rounded-xl border-gray-300 shadow-sm">
                            <option value="">Toți</option>
                            <option value="admin" @selected($owner === 'admin')>Admin</option>
                            <option value="seller" @selected($owner === 'seller')>Seller</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Moderare</label>
                        <select name="moderation" class="w-full rounded-xl border-gray-300 shadow-sm">
                            <option value="">Toate</option>
                            <option value="pending" @selected($moderation === 'pending')>În așteptare</option>
                            <option value="approved" @selected($moderation === 'approved')>Aprobate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm">
                            <option value="">Toate</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 xl:col-span-5 flex flex-wrap gap-3 pt-2">
                        <button
                            type="submit"
                            class="px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black"
                        >
                            Aplică filtre
                        </button>

                        <a href="{{ route('admin.products.index') }}"
                           class="px-5 py-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Resetează
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <p class="text-sm text-gray-600">
                        Rezultate: <span class="font-semibold">{{ $products->total() }}</span> produse
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Produs</th>
                                <th class="px-6 py-4 text-left">Seller</th>
                                <th class="px-6 py-4 text-left">Categorie</th>
                                <th class="px-6 py-4 text-left">Preț</th>
                                <th class="px-6 py-4 text-left">Stoc</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Moderare</th>
                                <th class="px-6 py-4 text-right">Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($products as $product)
                                @php
                                    $sellerName = $product->seller?->sellerProfile?->shop_name
                                        ?? $product->seller?->name
                                        ?? null;
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 align-top">{{ $product->id }}</td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="font-semibold text-gray-900">{{ $product->name }}</div>

                                        @if($product->description)
                                            <div class="mt-1 text-xs text-gray-500 line-clamp-2">
                                                {{ $product->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        @if($product->seller_id)
                                            <div class="inline-flex px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                                {{ $sellerName ?: 'Seller #' . $product->seller_id }}
                                            </div>

                                            @if($product->seller?->email)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $product->seller->email }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                                Admin
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        {{ $product->category->name ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 align-top font-semibold">
                                        {{ number_format((float) $product->final_price, 2) }} MDL
                                    </td>

                                    <td class="px-6 py-4 align-top">{{ $product->stock }}</td>

                                    <td class="px-6 py-4 align-top">
                                        @if($product->status)
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Activ
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                                Inactiv
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        @if(is_null($product->seller_id))
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                                Produs admin
                                            </span>
                                        @elseif($product->is_approved)
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Aprobat
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                                În așteptare
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex justify-end gap-2 flex-wrap">
                                            @if(!is_null($product->seller_id) && !$product->is_approved)
                                                <form method="POST" action="{{ route('admin.products.approve', $product->id) }}">
                                                    @csrf
                                                    <button class="px-3 py-2 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="px-3 py-2 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-black">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.products.destroy', $product) }}"
                                                  onsubmit="return confirm('Sigur vrei să ștergi produsul?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-600">
                                        Nu există produse pentru filtrele selectate.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>