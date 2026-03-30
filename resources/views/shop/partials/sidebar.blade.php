<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
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
        class="group mb-2 flex items-center justify-between rounded-lg px-3 py-3 text-base font-medium text-gray-900 transition hover:bg-gray-50"
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
            <div x-data="{ open: false }" class="rounded-lg">
                <div class="flex items-center justify-between">
                    <a
                        href="{{ route('category.show', $category) }}"
                        class="flex-1 rounded-lg px-3 py-3 text-base font-medium text-gray-900 transition hover:bg-gray-50"
                    >
                        {{ $category->name }}
                    </a>

                    @if($category->children->count())
                        <button
                            @click="open = !open"
                            class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-50"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4 transition"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    @endif
                </div>

                @if($category->children->count())
                    <div x-show="open" x-collapse class="pl-3 pb-2">
                        @foreach($category->children as $child)
                            <a
                                href="{{ route('category.show', $child) }}"
                                class="block rounded-lg px-3 py-2.5 text-sm text-gray-600 transition hover:bg-gray-50 hover:text-gray-900"
                            >
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>