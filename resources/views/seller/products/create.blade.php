<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Adaugă produs (Seller)
        </h2>
    </x-slot>

    @php
        $subMap = $categories->mapWithKeys(function ($cat) {
            return [
                $cat->id => $cat->children->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                ])->values(),
            ];
        });

        $selectedCat = old('category_id');
        $selectedSub = old('subcategory_id');
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded-2xl">

                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                        <div class="font-semibold mb-1">Există erori:</div>
                        <ul class="list-disc ml-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="productForm" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <input type="hidden" name="ai_generated_temp_path" id="ai_generated_temp_path" value="{{ old('ai_generated_temp_path') }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume produs</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ex: Laptop gaming ASUS"
                            class="mt-1 w-full border rounded-xl p-3"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descriere</label>
                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Descriere produs"
                            class="mt-1 w-full border rounded-xl p-3"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Preț (MDL)</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="final_price"
                                value="{{ old('final_price') }}"
                                placeholder="Ex: 6500"
                                class="mt-1 w-full border rounded-xl p-3"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stoc</label>
                            <input
                                type="number"
                                min="0"
                                name="stock"
                                value="{{ old('stock') }}"
                                placeholder="Ex: 10"
                                class="mt-1 w-full border rounded-xl p-3"
                                required
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categorie</label>
                            <select id="category_id" name="category_id" class="mt-1 w-full border rounded-xl p-3">
                                <option value="">— alege categoria —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string)$selectedCat === (string)$cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subcategorie</label>
                            <select id="subcategory_id" name="subcategory_id" class="mt-1 w-full border rounded-xl p-3" disabled>
                                <option value="">— alege subcategoria —</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine produs</label>
                        <input
                            id="image"
                            type="file"
                            name="image"
                            accept="image/*"
                            class="mt-1 block w-full"
                        >
                        <p class="mt-2 text-sm text-gray-500">
                            Această imagine va fi folosită și pentru bannerul AI.
                        </p>

                        <div id="imagePreviewWrap" class="mt-4 hidden">
                            <div class="text-sm font-medium text-gray-700 mb-2">Preview imagine</div>
                            <img id="imagePreview" class="w-48 h-48 object-cover rounded-xl border" alt="Preview">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                        <h3 class="text-lg font-semibold text-gray-900">AI banner assistant</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Încarci poza produsului, scrii promptul, apeși butonul și primești preview banner.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Prompt banner AI</label>
                            <textarea
                                name="ai_banner_prompt"
                                id="ai_banner_prompt"
                                rows="4"
                                placeholder="Ex: Fă un banner premium pentru acest laptop, fundal dark modern, lumină albastră, stil marketplace, produsul clar în centru."
                                class="mt-1 w-full border rounded-xl p-3"
                            >{{ old('ai_banner_prompt') }}</textarea>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <button
                                type="button"
                                id="generateBannerBtn"
                                class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700"
                            >
                                Generează banner AI
                            </button>

                            <button
                                type="button"
                                id="clearBannerBtn"
                                class="hidden px-5 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50"
                            >
                                Șterge preview banner
                            </button>
                        </div>

                        <div id="bannerStatus" class="mt-3 text-sm text-gray-600"></div>

                        <div id="bannerPreviewWrap" class="mt-5 hidden">
                            <div class="text-sm font-medium text-gray-700 mb-2">Preview banner AI</div>
                            <img id="bannerPreview" class="w-full max-w-3xl rounded-2xl border" alt="Banner AI">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700">
                            Salvează
                        </button>

                        <a href="{{ route('seller.products.index') }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Anulează
                        </a>
                    </div>
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
        refreshSubcategories(selectedSub);

        const imageInput = document.getElementById('image');
        const imagePreviewWrap = document.getElementById('imagePreviewWrap');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (!file) {
                imagePreviewWrap.classList.add('hidden');
                imagePreview.src = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                imagePreviewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        const generateBtn = document.getElementById('generateBannerBtn');
        const clearBtn = document.getElementById('clearBannerBtn');
        const bannerStatus = document.getElementById('bannerStatus');
        const bannerPreviewWrap = document.getElementById('bannerPreviewWrap');
        const bannerPreview = document.getElementById('bannerPreview');
        const promptInput = document.getElementById('ai_banner_prompt');
        const tempPathInput = document.getElementById('ai_generated_temp_path');

        async function clearTempBannerOnServer() {
            if (!tempPathInput.value) return;

            try {
                await fetch('{{ route('seller.products.ai_banner_preview.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        temp_path: tempPathInput.value
                    })
                });
            } catch (e) {
            }
        }

        function clearBannerPreviewUi() {
            bannerPreview.src = '';
            bannerPreviewWrap.classList.add('hidden');
            clearBtn.classList.add('hidden');
            tempPathInput.value = '';
        }

        clearBtn.addEventListener('click', async function () {
            await clearTempBannerOnServer();
            clearBannerPreviewUi();
            bannerStatus.textContent = 'Preview banner șters.';
        });

        generateBtn.addEventListener('click', async function () {
            const file = imageInput.files[0];
            const prompt = promptInput.value.trim();

            if (!file) {
                bannerStatus.textContent = 'Încarcă mai întâi imaginea produsului.';
                return;
            }

            if (!prompt) {
                bannerStatus.textContent = 'Scrie mai întâi promptul pentru banner AI.';
                return;
            }

            generateBtn.disabled = true;
            generateBtn.textContent = 'Se generează...';
            bannerStatus.textContent = 'Se generează bannerul AI...';

            await clearTempBannerOnServer();
            clearBannerPreviewUi();

            const fd = new FormData();
            fd.append('image', file);
            fd.append('ai_banner_prompt', prompt);

            try {
                const response = await fetch('{{ route('seller.products.ai_banner_preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: fd
                });

                const data = await response.json();

                if (!response.ok || !data.ok) {
                    throw new Error(data.message || 'Nu s-a putut genera bannerul.');
                }

                tempPathInput.value = data.temp_path;
                bannerPreview.src = data.url;
                bannerPreviewWrap.classList.remove('hidden');
                clearBtn.classList.remove('hidden');
                bannerStatus.textContent = 'Banner generat cu succes. Acum poți salva produsul.';
            } catch (error) {
                bannerStatus.textContent = error.message || 'Eroare la generare banner.';
            } finally {
                generateBtn.disabled = false;
                generateBtn.textContent = 'Generează banner AI';
            }
        });
    </script>
</x-app-layout>