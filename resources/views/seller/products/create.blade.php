<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">
                    Adaugă produs
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Flow simplu, clar și intuitiv pentru seller.
                </p>
            </div>

            <a href="{{ route('seller.products.index') }}"
               class="inline-flex items-center rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Înapoi
            </a>
        </div>
    </x-slot>

    @php
        $buildLeafMap = function ($nodes, $prefix = '') use (&$buildLeafMap) {
            $items = collect();

            foreach ($nodes as $node) {
                $label = $prefix ? $prefix . ' / ' . $node->name : $node->name;

                if ($node->childrenRecursive && $node->childrenRecursive->count()) {
                    $items = $items->merge($buildLeafMap($node->childrenRecursive, $label));
                } else {
                    $items->push([
                        'id' => $node->id,
                        'name' => $label,
                    ]);
                }
            }

            return $items;
        };

        $subMap = $categories->mapWithKeys(function ($root) use ($buildLeafMap) {
            return [
                $root->id => $buildLeafMap($root->childrenRecursive)->values(),
            ];
        });

        $selectedCat = old('category_id');
        $selectedSub = old('subcategory_id');
        $oldAttributes = old('attributes', []);
        $oldUnits = old('units', []);
        $oldVariants = old('variants', []);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                    <div class="text-sm font-semibold text-red-700">Există erori:</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="productForm"
                  method="POST"
                  action="{{ route('seller.products.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                <section class="rounded-3xl border border-blue-100 bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-3xl">
                            <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">
                                Publicare ghidata
                            </div>
                            <h3 class="mt-2 text-2xl font-semibold text-gray-900">
                                Structura trebuie sa fie simpla pentru seller si clara pentru client
                            </h3>
                            <p class="mt-2 text-sm leading-7 text-gray-600">
                                Mai intai alegi categoria corecta, apoi completezi baza produsului, iar variantele le folosesti doar daca produsul chiar are culoare, marime, memorie sau alt model diferit.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[420px]">
                            <div class="rounded-2xl border border-white/70 bg-white px-4 py-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">1</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900">Categorie finala</div>
                                <div class="mt-1 text-xs leading-6 text-gray-500">Alege locul corect si sistemul iti incarca automat campurile bune.</div>
                            </div>
                            <div class="rounded-2xl border border-white/70 bg-white px-4 py-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">2</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900">Baza produsului</div>
                                <div class="mt-1 text-xs leading-6 text-gray-500">Nume clar, descriere buna, pret si stoc.</div>
                            </div>
                            <div class="rounded-2xl border border-white/70 bg-white px-4 py-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">3</div>
                                <div class="mt-2 text-sm font-semibold text-gray-900">Varianta doar la nevoie</div>
                                <div class="mt-1 text-xs leading-6 text-gray-500">Foloseste variante doar cand clientul chiar trebuie sa aleaga intre optiuni.</div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-[220px_minmax(0,1fr)]">
                    {{-- sidebar steps --}}
                    <aside class="h-fit rounded-3xl border border-gray-200 bg-white p-5 shadow-sm xl:sticky xl:top-6">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-400">
                            Listare produs
                        </div>

                        <div class="mt-4 space-y-3">
                            <a href="#step-1" class="block rounded-2xl border border-gray-200 px-4 py-3 hover:bg-gray-50">
                                <div class="text-xs font-semibold text-blue-600">PASUL 1</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">Categorie</div>
                            </a>

                            <a href="#step-2" class="block rounded-2xl border border-gray-200 px-4 py-3 hover:bg-gray-50">
                                <div class="text-xs font-semibold text-blue-600">PASUL 2</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">Detalii produs</div>
                            </a>

                            <a href="#step-3" class="block rounded-2xl border border-gray-200 px-4 py-3 hover:bg-gray-50">
                                <div class="text-xs font-semibold text-blue-600">PASUL 3</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">Atribute & variante</div>
                            </a>

                            <a href="#step-4" class="block rounded-2xl border border-gray-200 px-4 py-3 hover:bg-gray-50">
                                <div class="text-xs font-semibold text-blue-600">PASUL 4</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">Imagini & publicare</div>
                            </a>
                        </div>

                        <div class="mt-5 rounded-2xl bg-gray-50 px-4 py-4 text-xs leading-6 text-gray-600">
                            Recomandare:
                            <br>1. alege categoria finală
                            <br>2. completează atributele
                            <br>3. adaugă variante doar dacă produsul chiar le are
                        </div>

                        <div id="listingReadinessPanel" class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">
                                    Stare formular
                                </div>
                                <span id="listingReadinessBadge" class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-blue-700">
                                    In lucru
                                </span>
                            </div>
                            <p id="listingReadinessText" class="mt-3 text-sm text-blue-900">
                                Alege categoria finala si completeaza detaliile esentiale pentru a activa verificarile inteligente.
                            </p>
                            <ul id="listingReadinessList" class="mt-3 space-y-2 text-xs text-blue-800"></ul>
                        </div>
                    </aside>

                    <div class="space-y-6">
                        {{-- STEP 1 --}}
                        <section id="step-1" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-6">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">
                                    Pasul 1
                                </div>
                                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                    Alege categoria finală
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Categoria corectă determină automat atributele și variantele disponibile.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Categorie principală
                                    </label>
                                    <select id="category_id"
                                            name="category_id"
                                            class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                            required>
                                        <option value="">— alege categoria —</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected((string) $selectedCat === (string) $cat->id)>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Categorie finală
                                    </label>
                                    <select id="subcategory_id"
                                            name="subcategory_id"
                                            class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                            disabled>
                                        <option value="">— alege categoria finală —</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        {{-- STEP 2 --}}
                        <section id="step-2" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-6">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">
                                    Pasul 2
                                </div>
                                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                    Detalii de bază
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Completează informațiile esențiale despre produs.
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="text-sm font-semibold text-gray-900">Tip listare</div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Incepe cu cea mai simpla alegere: produs simplu sau produs cu variante.
                                    </p>

                                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                        <label class="rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-gray-300" for="productModeSimple">
                                            <div class="flex items-start gap-3">
                                                <input
                                                    id="productModeSimple"
                                                    type="radio"
                                                    name="product_mode"
                                                    value="simple"
                                                    class="mt-1 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    {{ empty($oldVariants) ? 'checked' : '' }}
                                                >
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">Produs simplu</div>
                                                    <div class="mt-1 text-xs leading-6 text-gray-500">
                                                        Un singur pret si un singur stoc. Bun pentru produse fara selectie de culoare sau marime.
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-gray-300" for="productModeVariants">
                                            <div class="flex items-start gap-3">
                                                <input
                                                    id="productModeVariants"
                                                    type="radio"
                                                    name="product_mode"
                                                    value="variants"
                                                    class="mt-1 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    {{ !empty($oldVariants) ? 'checked' : '' }}
                                                >
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">Produs cu variante</div>
                                                    <div class="mt-1 text-xs leading-6 text-gray-500">
                                                        Clientul alege intre culoare, marime, memorie, volum sau alt model disponibil.
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Nume produs
                                    </label>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Ex: Tricou oversize bumbac premium"
                                           class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Descriere
                                    </label>
                                    <textarea name="description"
                                              rows="6"
                                              placeholder="Descriere clară, avantaje, materiale, utilizare, ce primește clientul."
                                              class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Preț de bază (MDL)
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="final_price"
                                               value="{{ old('final_price') }}"
                                               placeholder="Ex: 499"
                                               class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                               required>
                                        <p class="mt-2 text-xs text-gray-500">
                                            Folosit ca fallback dacă produsul nu are variante cu preț separat.
                                        </p>
                                    </div>

                                    <div id="baseStockWrap">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Stoc total
                                        </label>

                                        <input type="number"
                                            min="0"
                                            name="stock"
                                            value="{{ old('stock') }}"
                                            placeholder="Ex: 20"
                                            class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm">

                                        <p class="mt-2 text-xs text-gray-500">
                                            Dacă adaugi variante, acest câmp nu va fi folosit.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- STEP 3 --}}
                        <section id="step-3" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-6">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">
                                    Pasul 3
                                </div>
                                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                    Atribute și variante
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Atributele se încarcă automat după alegerea categoriei finale.
                                </p>
                            </div>

                            <div id="attributesPlaceholder" class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center text-sm text-gray-500">
                                Alege mai întâi categoria finală pentru a vedea atributele.
                            </div>

                            <div id="catalogAssistantPanel" class="mb-5 hidden rounded-2xl border px-4 py-4">
                                <div id="catalogAssistantTitle" class="text-sm font-semibold"></div>
                                <p id="catalogAssistantText" class="mt-2 text-sm"></p>
                                <ul id="catalogAssistantList" class="mt-3 space-y-2 text-xs"></ul>
                            </div>

                            <div id="dynamicAttributesWrap" class="hidden">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2" id="dynamicAttributes"></div>
                            </div>

                            <div id="variantConfiguratorWrap" class="mt-8 hidden rounded-3xl border border-amber-200 bg-amber-50 p-5">
                                <input type="hidden" name="has_variants_enabled" id="hasVariantsEnabledInput" value="{{ !empty($oldVariants) ? '1' : '0' }}">
                                <input
                                    type="checkbox"
                                    id="hasVariantsToggle"
                                    class="hidden"
                                    {{ !empty($oldVariants) ? 'checked' : '' }}
                                >

                                <div>
                                    <h4 class="text-base font-semibold text-gray-900">
                                        Variante produs
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Sectiunea apare doar cand alegi sus ca produsul are variante reale: culoare, marime, volum, memorie sau model.
                                    </p>
                                </div>

                                <div id="variantAttributesSelectors" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2"></div>

                                <div id="variantHelper" class="mt-4 rounded-2xl border border-amber-200 bg-white/80 px-4 py-3 text-sm text-amber-900">
                                    Daca produsul are variante, alege mai intai valorile importante, apoi genereaza combinatiile.
                                </div>

                                <div class="mt-5 flex flex-wrap gap-3">
                                    <button type="button"
                                            id="generateVariantsBtn"
                                            class="inline-flex items-center rounded-2xl bg-amber-600 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-700">
                                        Generează combinații
                                    </button>

                                    <button type="button"
                                            id="addVariantRowBtn"
                                            class="inline-flex items-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                        Adaugă manual
                                    </button>

                                    <button type="button"
                                            id="clearVariantsBtn"
                                            class="inline-flex items-center rounded-2xl border border-red-300 bg-white px-5 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                                        Șterge toate
                                    </button>
                                </div>
                            </div>

                            <div id="variantsTableWrap" class="mt-6 hidden rounded-3xl border border-gray-200 bg-gray-50 p-5">
                                <div class="mb-4">
                                    <h4 class="text-base font-semibold text-gray-900">
                                        Lista variantelor
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Completează preț, stoc, SKU și imagine pentru fiecare variantă.
                                    </p>
                                </div>

                                <div id="variantsRows" class="space-y-4"></div>
                            </div>
                        </section>

                        {{-- STEP 4 --}}
                        <section id="step-4" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-6">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">
                                    Pasul 4
                                </div>
                                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                    Imagini și publicare
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Adaugă imaginile principale și cele secundare ale produsului.
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div class="rounded-2xl border border-gray-200 p-5">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Imagine principală
                                    </label>
                                    <input id="image"
                                           type="file"
                                           name="image"
                                           accept="image/*"
                                           data-has-existing-image="0"
                                           class="mt-3 block w-full text-sm text-gray-700">

                                    <div id="imagePreviewWrap" class="mt-4 hidden">
                                        <div class="mb-2 text-sm font-medium text-gray-700">
                                            Preview imagine principală
                                        </div>
                                        <img id="imagePreview"
                                             class="h-56 w-56 rounded-2xl border border-gray-200 object-cover"
                                             alt="Preview">
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-gray-200 p-5">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Galerie imagini
                                    </label>
                                    <div class="mt-3 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm text-blue-900">
                                        <div class="font-semibold">Poti adauga pozele pe rand, fara sa le pierzi pe cele deja alese.</div>
                                        <p id="gallerySelectionNote" class="mt-1 text-sm text-blue-800">
                                            Poti selecta o poza acum si apoi sa revii cu inca una sau mai multe. Fiecare selectie noua se adauga la lista curenta.
                                        </p>
                                        <div id="gallerySelectionCount" class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                                            0/8 imagini selectate
                                        </div>
                                    </div>
                                    <input id="gallery"
                                           type="file"
                                           name="gallery[]"
                                           accept="image/*"
                                           multiple
                                           class="mt-3 block w-full text-sm text-gray-700">
                                    <p class="mt-2 text-xs text-gray-500">
                                        Poti adauga pana la 8 imagini in galerie. Daca selectezi iar alte poze, ele se adauga la cele existente, nu le inlocuiesc.
                                    </p>

                                    <div id="galleryPreviewWrap" class="mt-4 hidden">
                                        <div class="mb-2 text-sm font-medium text-gray-700">
                                            Imagini selectate pentru galerie
                                        </div>
                                        <div id="galleryPreviewGrid" class="grid grid-cols-2 gap-4 md:grid-cols-4"></div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row">
                                    <button type="submit"
                                            id="submitProductBtn"
                                            class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                        Publică produsul
                                    </button>

                                    <a href="{{ route('seller.products.index') }}"
                                       class="inline-flex items-center justify-center rounded-2xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                        Anulează
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('seller.products.partials.create-script')
</x-app-layout>


