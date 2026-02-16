<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Produse
            </h2>

            <a href="{{ route('admin.products.create') }}"
                class="!inline-flex !items-center !px-4 !py-2 !bg-blue-600 !text-white !rounded-md !text-sm !font-semibold hover:!bg-blue-700">
                + Adaugă produs
            </a>

        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                <div class="p-6 border-b">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $products->count() }}</span> produse
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">ID</th>
                                <th class="px-6 py-3 text-left">Nume</th>
                                <th class="px-6 py-3 text-left">Preț</th>
                                <th class="px-6 py-3 text-left">Stoc</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-right">Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $product->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-4">{{ $product->final_price }} MDL</td>
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
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="px-3 py-1.5 rounded bg-gray-800 text-white text-xs hover:bg-black">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
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
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-600">
                                        Nu există produse încă. Apasă „Adaugă produs”.
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
