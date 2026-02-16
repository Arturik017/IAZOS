<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bannere</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow rounded">
                <h3 class="font-semibold text-lg mb-4">Adaugă banner</h3>

                <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Imagine (obligatoriu)</label>
                        <input type="file" name="image" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kicker (mic text deasupra)</label>
                        <input name="kicker" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titlu</label>
                        <input name="title" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subtitlu</label>
                        <input name="subtitle" class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link (opțional)</label>
                        <input name="link" placeholder="/category/1 sau https://..." class="mt-1 border w-full p-2 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordine (0 = primul)</label>
                        <input name="sort_order" type="number" class="mt-1 border w-full p-2 rounded" value="0">
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="status" type="checkbox" name="status" class="rounded">
                        <label for="status" class="text-sm text-gray-700">Activ</label>
                    </div>

                    <div class="md:col-span-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                            Salvează
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 shadow rounded">
                <h3 class="font-semibold text-lg mb-4">Lista bannere</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($banners as $b)
                        <div class="border rounded overflow-hidden">
                            <div class="h-40 bg-gray-100">
                                @if($b->image)
                                    <img src="{{ asset('storage/'.$b->image) }}" class="w-full h-40 object-cover" alt="">
                                @endif
                            </div>

                            <div class="p-4">
                                <div class="text-xs text-gray-500">#{{ $b->id }} • sort: {{ $b->sort_order }} • {{ $b->status ? 'Activ' : 'Inactiv' }}</div>
                                <div class="font-semibold">{{ $b->title }}</div>
                                <div class="text-sm text-gray-600">{{ $b->subtitle }}</div>

                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-xs text-gray-500">{{ $b->link }}</div>

                                    <form method="POST" action="{{ route('admin.banners.destroy', $b) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline" onclick="return confirm('Ștergi bannerul?')">
                                            Șterge
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-600">Nu ai bannere încă.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
