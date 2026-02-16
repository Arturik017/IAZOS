<div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50">
        <div class="font-bold text-gray-900">Categorii</div>
        <div class="text-xs text-gray-500">Alege o categorie</div>
    </div>

    <div class="p-4">
        @if(empty($categories) || $categories->count() === 0)
            <div class="text-sm text-gray-500">Nu există categorii încă.</div>
        @else
            <ul class="space-y-2">
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('category.show', $cat) }}"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-900 font-medium">
                            {{ $cat->name }}
                        </a>

                        @if($cat->children && $cat->children->count())
                            <ul class="mt-1 ml-3 border-l pl-3 space-y-1">
                                @foreach($cat->children as $sub)
                                    <li>
                                        <a href="{{ route('subcategory.show', $sub) }}"
                                           class="block px-3 py-1.5 rounded-lg hover:bg-gray-100 text-sm text-gray-700">
                                            {{ $sub->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
