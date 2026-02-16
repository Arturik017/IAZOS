<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Adaugă banner</h2>
                <p class="text-sm text-gray-500">Imagine + texte opționale</p>
            </div>

            <a href="{{ route('admin.banners.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                Înapoi la listă
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine (obligatoriu)</label>
                        <input type="file" name="image" accept="image/*"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" required>
                        @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titlu</label>
                            <input name="title" class="mt-1 border w-full p-2 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subtitlu</label>
                            <input name="subtitle" class="mt-1 border w-full p-2 rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kicker (ex: SUPER OFERTĂ)</label>
                        <input name="kicker" class="mt-1 border w-full p-2 rounded-lg">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Link (opțional)</label>
                            <input name="link" class="mt-1 border w-full p-2 rounded-lg" placeholder="/product/1 sau https://...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sort order</label>
                            <input type="number" name="sort_order" class="mt-1 border w-full p-2 rounded-lg" value="0" min="0">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="status" class="rounded border-gray-300" checked>
                        <span class="text-sm text-gray-700">Activ</span>
                    </label>

                    <button class="w-full px-4 py-3 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700">
                        Salvează banner
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
