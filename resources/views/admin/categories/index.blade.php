<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categorii
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow rounded">
                <h3 class="font-semibold text-lg mb-4">Adaugă categorie / subcategorie</h3>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 border w-full p-2 rounded" required>
                        @error('name')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Este subcategorie pentru</label>
                        <select name="parent_id" class="mt-1 border w-full p-2 rounded">
                            <option value="">(Categorie principală)</option>
                            @foreach($allCategories as $c)
                                <option value="{{ $c->id }}" @selected(old('parent_id') == $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                        Salvează
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 shadow rounded">
                <h3 class="font-semibold text-lg mb-4">Lista categorii</h3>

                <div class="space-y-4">
                    @forelse($categories as $cat)
                        <div class="border rounded p-4">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold">{{ $cat->name }}</div>

                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                      onsubmit="return confirm('Sigur ștergi categoria?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        Șterge
                                    </button>
                                </form>
                            </div>

                            @if($cat->children->count())
                                <div class="mt-3 pl-4 border-l space-y-2">
                                    @foreach($cat->children as $sub)
                                        <div class="flex items-center justify-between">
                                            <div class="text-gray-700">— {{ $sub->name }}</div>

                                            <form method="POST" action="{{ route('admin.categories.destroy', $sub) }}"
                                                  onsubmit="return confirm('Sigur ștergi subcategoria?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                    Șterge
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-gray-600">Nu există categorii încă.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
