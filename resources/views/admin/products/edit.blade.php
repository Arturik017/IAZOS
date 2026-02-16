<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editează produs
        </h2>
    </x-slot>

    @php
        $subMap = $categories->mapWithKeys(function($cat){
            return [
                $cat->id => $cat->children->map(fn($s)=> ['id'=>$s->id,'name'=>$s->name])->values()
            ];
        });
        $selectedCat = old('category_id', $product->category_id);
        $selectedSub = old('subcategory_id', $product->subcategory_id);
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded">

                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume produs</label>
                        <input name="name" value="{{ old('name', $product->name) }}" class="mt-1 border w-full p-2 rounded" required>
                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descriere</label>
                        <textarea name="description" class="mt-1 border w-full p-2 rounded">{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Preț final (MDL)</label>
                        <input name="final_price" type="number" step="0.01" value="{{ old('final_price', $product->final_price) }}"
                               class="mt-1 border w-full p-2 rounded" required>
                        @error('final_price') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stoc</label>
                        <input name="stock" type="number" value="{{ old('stock', $product->stock) }}"
                               class="mt-1 border w-full p-2 rounded" required>
                        @error('stock') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="status" value="1" class="rounded border-gray-300"
                               {{ old('status', $product->status) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Activ</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_promo" value="1" class="rounded border-gray-300"
                               {{ old('is_promo', $product->is_promo) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Produs la promoție</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine nouă (opțional)</label>
                        <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
                        @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror

                        @if($product->image)
                            <div class="mt-2 text-sm text-gray-600">
                                Imagine curentă: <span class="font-medium">{{ $product->image }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categorie</label>
                        <select id="category_id" name="category_id" class="mt-1 border w-full p-2 rounded">
                            <option value="">— alege categoria —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected($selectedCat == $cat->id)>
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
                        Salvează modificările
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        const subMap = @json($subMap);
        const catSel = document.getElementById('category_id');
        const subSel = document.getElementById('subcategory_id');
        const selectedSub = @json($selectedSub);

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

        // init: populează în funcție de categoria curentă și selectează subcategoria curentă
        refreshSubcategories(selectedSub);
    </script>
</x-app-layout>
