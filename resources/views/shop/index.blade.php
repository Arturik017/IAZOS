<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Magazin</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="border p-4 rounded">
                    <div class="font-bold">{{ $product->name }}</div>
                    <div>{{ $product->final_price }} MDL</div>
                    <div class="text-sm text-gray-600">Stoc: {{ $product->stock }}</div>
                </div>
            @empty
                <p>Nu există produse încă.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
