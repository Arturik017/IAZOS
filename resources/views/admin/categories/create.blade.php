<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adaugă categorie / subcategorie
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded">

                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nume</label>
                        <input name="name" value="{{ old('name') }}"
                               class="border w-full p-2 rounded mt-1" />
                        @error('name')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Părinte (lasă gol pentru categorie principală)
                        </label>

                        <select name="parent_id" class="border w-full p-2 rounded mt-1">
                            <option value="">— Categorie principală —</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('parent_id')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded w-full">
                        Salvează
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
