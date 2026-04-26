<x-app-layout>
    @php
        $activeFilterChips = collect();

        if (request()->boolean('promo')) {
            $activeFilterChips->push('Promotii');
        }

        if (request()->boolean('in_stock')) {
            $activeFilterChips->push('In stoc');
        }

        if (request('min_price')) {
            $activeFilterChips->push('Min ' . request('min_price') . ' MDL');
        }

        if (request('max_price')) {
            $activeFilterChips->push('Max ' . request('max_price') . ' MDL');
        }

        if (request('sort') && request('sort') !== 'new') {
            $activeFilterChips->push(request('sort') === 'price_asc' ? 'Pret crescator' : 'Pret descrescator');
        }
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $listingTitle ?? $category?->name }}</h2>
                <p class="text-sm text-gray-500">{{ $listingSubtitle ?? 'Produse in aceasta categorie' }}</p>
            </div>

            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Inapoi la Home
            </a>
        </div>
    </x-slot>

    <div class="market-page py-10">
        <div class="market-shell mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 lg:flex-row">
                <aside class="w-full lg:w-72" x-data="{ openCats: false }">
                    <div class="lg:hidden">
                        <button
                            type="button"
                            @click="openCats = !openCats"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-2xl bg-white shadow border border-gray-100"
                        >
                            <span class="font-semibold text-gray-900">Categorii</span>
                        </button>
                    </div>

                    <div class="mt-3 lg:mt-0 lg:sticky lg:top-24 lg:block" :class="openCats ? 'block' : 'hidden'">
                        @include('shop.partials.sidebar')
                    </div>
                </aside>

                <div class="flex-1 space-y-6">
                    @if($activeFilterChips->isNotEmpty())
                        <div class="market-section rounded-xl border border-gray-100 bg-white p-4 shadow">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">Filtre active</div>
                                    <div class="mt-1 text-xs text-gray-500">Selectiile curente sunt vizibile pentru comparatie rapida.</div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($activeFilterChips as $chip)
                                        <span class="market-chip">{{ $chip }}</span>
                                    @endforeach

                                    <a href="{{ $resetUrl ?? url()->current() }}" class="market-chip market-chip-cool">
                                        Reseteaza
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div x-data="{ openFilters: false }" class="market-section overflow-hidden rounded-xl border border-gray-100 bg-white shadow">
                        <button
                            type="button"
                            @click="openFilters = !openFilters"
                            class="w-full flex items-center justify-between px-6 py-5"
                        >
                            <div class="text-left py-2">
                                <div class="font-semibold text-gray-900">Filtre si sortare</div>
                                <div class="text-xs text-gray-500">Filtre generale plus atribute specifice categoriei</div>
                            </div>
                            <div class="text-2xl leading-none text-gray-500" x-text="openFilters ? '-' : '+'"></div>
                        </button>

                        <div
                            x-show="openFilters"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-[2200px]"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 max-h-[2200px]"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden border-t border-gray-100"
                        >
                            <div class="px-6 pt-5 pb-10">
                                <form method="GET" class="space-y-8">
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                        <label class="market-filter-option flex items-center gap-2 rounded-xl border border-transparent bg-white px-3 py-3 text-sm text-gray-700 transition">
                                            <input type="checkbox" name="promo" value="1" class="rounded border-gray-300" {{ request()->boolean('promo') ? 'checked' : '' }}>
                                            Doar promotii
                                        </label>

                                        <label class="market-filter-option flex items-center gap-2 rounded-xl border border-transparent bg-white px-3 py-3 text-sm text-gray-700 transition">
                                            <input type="checkbox" name="in_stock" value="1" class="rounded border-gray-300" {{ request()->boolean('in_stock') ? 'checked' : '' }}>
                                            Doar in stoc
                                        </label>

                                        <div>
                                            <label class="block text-xs text-gray-500">Pret minim</label>
                                            <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-500">Pret maxim</label>
                                            <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-1">
                                            <label class="block text-xs text-gray-500">Sortare</label>
                                            <select name="sort" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                                <option value="new" {{ request('sort','new') === 'new' ? 'selected' : '' }}>Cele mai noi</option>
                                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Pret crescator</option>
                                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Pret descrescator</option>
                                            </select>
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-3 flex flex-wrap items-end gap-3">
                                            <button type="submit" class="rounded-xl bg-gray-900 px-6 py-3 font-semibold text-white transition hover:bg-black">
                                                Aplica filtre
                                            </button>

                                            <a href="{{ $resetUrl ?? url()->current() }}" class="rounded-xl border border-gray-200 bg-white px-6 py-3 font-semibold text-gray-900 transition hover:bg-gray-50">
                                                Reseteaza
                                            </a>
                                        </div>
                                    </div>

                                    @if(!empty($filterDefinitions) && collect($filterDefinitions)->isNotEmpty())
                                        <div class="border-t border-gray-100 pt-6">
                                            <div class="mb-4">
                                                <div class="text-sm font-semibold text-gray-900">Filtre avansate pe atribut</div>
                                                <div class="text-xs text-gray-500">Generate din atributele reale ale categoriei selectate</div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                                @foreach($filterDefinitions as $filter)
                                                    @php
                                                        $slug = $filter['slug'];
                                                        $type = $filter['type'];
                                                        $selectedValues = (array) request("filters.$slug", []);
                                                    @endphp

                                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                                        <div class="text-sm font-semibold text-gray-900">{{ $filter['name'] }}</div>

                                                        @if(in_array($type, ['select', 'multiselect', 'text'], true))
                                                            @php
                                                                $items = $type === 'text'
                                                                    ? collect($filter['text_values'] ?? [])->map(fn ($value) => ['value' => $value, 'label' => $value])->values()->all()
                                                                    : ($filter['options'] ?? []);
                                                            @endphp

                                                            @if(!empty($items))
                                                                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                                                    @foreach($items as $item)
                                                                        <label class="market-filter-option flex items-center gap-2 rounded-xl border border-transparent bg-white px-3 py-2 text-sm text-gray-700 transition">
                                                                            <input
                                                                                type="checkbox"
                                                                                name="filters[{{ $slug }}][]"
                                                                                value="{{ $item['value'] }}"
                                                                                class="rounded border-gray-300"
                                                                                {{ in_array((string) $item['value'], array_map('strval', $selectedValues), true) ? 'checked' : '' }}
                                                                            >
                                                                            <span>{{ $item['label'] }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @elseif($type === 'number')
                                                            <div class="mt-3 grid grid-cols-2 gap-3">
                                                                <div>
                                                                    <label class="block text-xs text-gray-500">Min</label>
                                                                    <input type="number" step="0.01" name="filters[{{ $slug }}][min]" value="{{ request("filters.$slug.min") }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-gray-500">Max</label>
                                                                    <input type="number" step="0.01" name="filters[{{ $slug }}][max]" value="{{ request("filters.$slug.max") }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                                                </div>
                                                            </div>

                                                            @if(isset($filter['min_value']) && isset($filter['max_value']) && $filter['min_value'] !== null && $filter['max_value'] !== null)
                                                                <div class="mt-2 text-xs text-gray-500">
                                                                    Interval disponibil: {{ rtrim(rtrim(number_format((float) $filter['min_value'], 2, '.', ''), '0'), '.') }} - {{ rtrim(rtrim(number_format((float) $filter['max_value'], 2, '.', ''), '0'), '.') }}
                                                                </div>
                                                            @endif
                                                        @elseif($type === 'boolean')
                                                            <label class="market-filter-option mt-3 flex items-center gap-2 rounded-xl border border-transparent bg-white px-3 py-3 text-sm text-gray-700 transition">
                                                                <input type="checkbox" name="filters[{{ $slug }}]" value="1" class="rounded border-gray-300" {{ request()->boolean("filters.$slug") ? 'checked' : '' }}>
                                                                <span>Da</span>
                                                            </label>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    @if($products->count() === 0)
                        <div class="market-empty">
                            <div class="market-empty-mark">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 7h14M7 7l1 12h8l1-12M9 11h6" />
                                </svg>
                            </div>
                            <div class="mt-5 font-semibold text-gray-900">Nu exista produse pentru filtrele selectate.</div>
                            <div class="mt-1 text-sm text-gray-500">Incearca alte filtre sau reseteaza selectiile active.</div>
                            <a href="{{ $resetUrl ?? url()->current() }}" class="mt-5 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white">
                                Reseteaza filtrele
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                            @foreach($products as $product)
                                @include('shop.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
