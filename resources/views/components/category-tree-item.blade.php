@props([
    'category',
    'level' => 0,
])

@php
    $currentCategory = request()->route('category');
    $currentCategoryId = data_get($currentCategory, 'id', $currentCategory);
    $isActive = request()->routeIs('category.show') && $currentCategoryId && (int) $currentCategoryId === (int) $category->id;
@endphp

<div x-data="{ open: {{ $level < 1 ? 'false' : 'false' }} }" class="rounded-lg">
    <div class="flex items-center justify-between gap-2">
        <a
            href="{{ route('category.show', $category) }}"
            class="market-category-link flex-1 rounded-lg px-3 py-2.5 transition hover:bg-gray-50 {{ $isActive ? 'is-active' : '' }} {{ $level === 0 ? 'text-base font-medium text-gray-900' : 'text-sm text-gray-700' }}"
            style="padding-left: {{ 12 + ($level * 14) }}px;"
        >
            {{ $category->name }}
        </a>

        @if($category->childrenRecursive->count())
            <button
                type="button"
                @click="open = !open"
                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-50"
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

    @if($category->childrenRecursive->count())
        <div x-show="open" x-collapse class="space-y-1 pb-1">
            @foreach($category->childrenRecursive as $child)
                <x-category-tree-item :category="$child" :level="$level + 1" />
            @endforeach
        </div>
    @endif
</div>
