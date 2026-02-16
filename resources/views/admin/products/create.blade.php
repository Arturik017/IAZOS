<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adaugă produs
        </h2>
    </x-slot>

    @php
        // Map: category_id => [{id,name}, ...]
        $subMap = $categories->mapWithKeys(function($cat){
            return [
                $cat->id => $cat->children->map(fn($s)=> ['id'=>$s->id,'name'=>$s->name])->values()
            ];
        });
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded">

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume produs</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 border w-full p-2 rounded" required>
                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descriere</label>
                        <textarea name="description" class="mt-1 border w-full p-2 rounded">{{ old('description') }}</textarea>
                        @error('description') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Preț final (MDL)</label>
                        <input name="final_price" type="number" step="0.01" value="{{ old('final_price') }}"
                               class="mt-1 border w-full p-2 rounded" required>
                        @error('final_price') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stoc</label>
                        <input name="stock" type="number" value="{{ old('stock') }}"
                               class="mt-1 border w-full p-2 rounded" required>
                        @error('stock') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="status" value="1" class="rounded border-gray-300" {{ old('status') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Activ</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_promo" value="1" class="rounded border-gray-300" {{ old('is_promo') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Produs la promoție</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine produs</label>
                        <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
                        @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categorie</label>
                        <select id="category_id" name="category_id" class="mt-1 border w-full p-2 rounded">
                            <option value="">— alege categoria —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subcategorie</label>
                        <select id="subcategory_id" name="subcategory_id" class="mt-1 border w-full p-2 rounded" disabled>
                            <option value="">— alege subcategoria —</option>
                        </select>
                        @error('subcategory_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded">
                        Salvează produs
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        const subMap = @json($subMap);
        const catSel = document.getElementById('category_id');
        const subSel = document.getElementById('subcategory_id');
        const oldSub = @json(old('subcategory_id'));

        function refreshSubcategories(selectedSubId = null) {
            const catId = catSel.value;

            subSel.innerHTML = '<option value="">— alege subcategoria —</option>';

            if (!catId || !subMap[catId] || subMap[catId].length === 0) {
                subSel.disabled = true;
                return;
            }

            subSel.disabled = false;

            subMap[catId].forEach(sc => {
                const opt = document.createElement('option');
                opt.value = sc.id;
                opt.textContent = sc.name;
                if (selectedSubId && String(selectedSubId) === String(sc.id)) {
                    opt.selected = true;
                }
                subSel.appendChild(opt);
            });
        }

        catSel.addEventListener('change', () => refreshSubcategories());

        // init
        refreshSubcategories(oldSub);
    </script>
</x-app-layout>
