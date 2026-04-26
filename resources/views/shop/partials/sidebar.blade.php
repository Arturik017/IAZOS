<div class="market-sidebar rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="mb-5">
        <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">
            Navigare
        </div>

        <h3 class="mt-3 text-2xl font-medium text-gray-900">
            Categorii
        </h3>
    </div>

    <a
        href="{{ route('home') }}"
        class="market-category-link group mb-2 flex items-center justify-between rounded-lg px-3 py-3 text-base font-medium text-gray-900 transition hover:bg-gray-50 {{ request()->routeIs('home') ? 'is-active' : '' }}"
    >
        <span>Toate produsele</span>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 text-gray-500 transition"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    <div class="space-y-1">
        @foreach($categories as $category)
            <x-category-tree-item :category="$category" :level="0" />
        @endforeach
    </div>
</div>
