<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Produsele mele
            </h2>

            <a href="{{ route('seller.products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                + Adaugă produs
            </a>
        </div>
    </x-slot>

    <div class="market-page py-12">
        <div class="market-shell mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="market-section bg-white shadow rounded-xl overflow-hidden">

                <div class="p-6 border-b">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $products->count() }}</span> produse
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="market-table min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">ID</th>
                                <th class="px-6 py-3 text-left">Nume</th>
                                <th class="px-6 py-3 text-left">Preț</th>
                                <th class="px-6 py-3 text-left">Stoc</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Moderare</th>
                                <th class="px-6 py-3 text-right">Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $product->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-4">{{ number_format((float)$product->final_price, 2, '.', ',') }} MDL</td>
                                    <td class="px-6 py-4">{{ $product->stock }}</td>

                                    <td class="px-6 py-4">
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

                                    <td class="px-6 py-4">
                                        @if($product->is_approved)
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Aprobat
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                                În așteptare
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('seller.products.edit', $product->id) }}"
                                               class="px-3 py-1.5 rounded bg-gray-800 text-white text-xs hover:bg-black">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('seller.products.destroy', $product->id) }}"
                                                  onsubmit="return confirm('Sigur vrei să ștergi produsul?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-1.5 rounded bg-red-600 text-white text-xs hover:bg-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-600">
                                        Nu ai produse încă.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
