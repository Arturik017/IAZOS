<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500">Detalii produs</p>
            </div>

            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi
            </a>
        </div>
    </x-slot>

    @php
        $avgRating = $product->averageRating();
        $reviewsCount = $product->reviewsCount();
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

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

                    <div
                        class="mt-3 lg:mt-0 lg:sticky lg:top-6 lg:block"
                        :class="openCats ? 'block' : 'hidden'"
                    >
                        @include('shop.partials.sidebar')
                    </div>
                </aside>

                <div class="flex-1 space-y-8">

                    @if(session('success'))
                        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                            <ul class="list-disc ml-5 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="bg-gray-50">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="w-full h-[320px] sm:h-[420px] object-cover"
                                         alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-[320px] sm:h-[420px] flex items-center justify-center text-gray-400">
                                        Fără imagine
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 sm:p-8">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-3xl font-extrabold text-gray-900">
                                        {{ number_format($product->final_price, 2) }} MDL
                                    </div>

                                    @if((int)$product->stock > 0)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            În stoc
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Stoc epuizat
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 flex items-center gap-3">
                                    <div class="text-yellow-500 text-lg">
                                        {{ str_repeat('★', (int) round($avgRating)) }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $avgRating }} din 5 ({{ $reviewsCount }} review-uri)
                                    </div>
                                </div>

                                <div class="mt-3 text-sm text-gray-600">
                                    Stoc: <span class="font-semibold text-gray-900">{{ $product->stock }}</span>
                                </div>

                                @if($product->seller && $product->seller->sellerProfile)
                                    <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-sm text-gray-500">Vândut de</div>
                                        <a href="{{ route('seller.public.show', $product->seller->id) }}"
                                           class="mt-1 inline-block text-base font-semibold text-blue-600 hover:text-blue-700">
                                            {{ $product->seller->sellerProfile->shop_name }}
                                        </a>

                                        @if($product->seller->sellerProfile->phone)
                                            <div class="mt-2 text-sm text-gray-600">
                                                Telefon: {{ $product->seller->sellerProfile->phone }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if(!empty($product->description))
                                    <div class="mt-5">
                                        <div class="text-sm font-semibold text-gray-900">Descriere</div>
                                        <div class="mt-2 text-sm text-gray-600 leading-relaxed">
                                            {{ $product->description }}
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-6 flex gap-3">
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                                                @disabled((int)$product->stock <= 0 || (int)$product->status !== 1)>
                                            Adaugă în coș
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.buy', $product) }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition disabled:opacity-50"
                                                @disabled((int)$product->stock <= 0 || (int)$product->status !== 1)>
                                            Cumpără acum
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 text-xs text-gray-400">
                                    Prețurile includ vama și livrarea în Moldova.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Întrebări despre produs</h3>
                        <p class="text-sm text-gray-500">Guest și userii care nu au cumpărat pot pune doar întrebări.</p>

                        <form method="POST" action="{{ route('products.questions.store', $product) }}" class="mt-5 space-y-4">
                            @csrf

                            @guest
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nume</label>
                                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="w-full border rounded-xl p-3">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full border rounded-xl p-3">
                                    </div>
                                </div>
                            @endguest

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Întrebarea ta</label>
                                <textarea name="question" rows="4" class="w-full border rounded-xl p-3" placeholder="Scrie întrebarea despre produs...">{{ old('question') }}</textarea>
                            </div>

                            <button class="px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black">
                                Trimite întrebarea
                            </button>
                        </form>

                        <div class="mt-8 space-y-4">
                            @forelse($questions as $question)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="font-semibold text-gray-900">
                                            {{ $question->user?->name ?? $question->guest_name ?? 'Vizitator' }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $question->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>

                                    <div class="mt-2 text-sm text-gray-700">
                                        {{ $question->question }}
                                    </div>

                                    {{-- ✅ ANSWER --}}
                                    @if($question->answer)
                                        <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">
                                            <div class="text-sm font-semibold text-gray-900">Răspuns seller</div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                {{ $question->answer }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 🔥 FORM DE RĂSPUNS --}}
                                    @auth
                                        @if(auth()->id() === $product->seller_id || auth()->user()->role === 'admin')
                                            <form method="POST"
                                                action="{{ route('products.questions.answer', $question) }}"
                                                class="mt-4 space-y-3">
                                                @csrf

                                                <textarea
                                                    name="answer"
                                                    rows="3"
                                                    class="w-full border rounded-xl p-3"
                                                    placeholder="Scrie răspunsul..."
                                                >{{ old('answer', $question->answer) }}</textarea>

                                                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm">
                                                    {{ $question->answer ? 'Actualizează răspuns' : 'Răspunde' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endauth

                                </div>
                            @empty
                                <div class="text-sm text-gray-500">
                                    Nu există întrebări încă.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Review-uri produs</h3>
                        <p class="text-sm text-gray-500">Doar cumpărătorii reali pot lăsa rating, comentariu și poze.</p>

                        @auth
                            @if($canReview)
                                <form method="POST" action="{{ route('products.review.store', $product) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                                    @csrf

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                            <select name="rating" class="w-full border rounded-xl p-3">
                                                <option value="">Alege rating</option>
                                                <option value="5" @selected(old('rating', $myReview?->rating) == 5)>5 stele</option>
                                                <option value="4" @selected(old('rating', $myReview?->rating) == 4)>4 stele</option>
                                                <option value="3" @selected(old('rating', $myReview?->rating) == 3)>3 stele</option>
                                                <option value="2" @selected(old('rating', $myReview?->rating) == 2)>2 stele</option>
                                                <option value="1" @selected(old('rating', $myReview?->rating) == 1)>1 stea</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Poze produs (max 4)</label>
                                            <input type="file" name="images[]" multiple accept="image/*" class="w-full border rounded-xl p-3">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentariu</label>
                                        <textarea name="comment" rows="4" class="w-full border rounded-xl p-3" placeholder="Spune părerea ta despre produs...">{{ old('comment', $myReview?->comment) }}</textarea>
                                    </div>

                                    <button class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                                        {{ $myReview ? 'Actualizează review-ul' : 'Trimite review-ul' }}
                                    </button>
                                </form>
                            @else
                                <div class="mt-5 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3">
                                    Poți lăsa review doar dacă ai cumpărat și achitat acest produs.
                                </div>
                            @endif
                        @else
                            <div class="mt-5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 px-4 py-3">
                                Pentru review trebuie să fii logat și să fi cumpărat produsul.
                            </div>
                        @endauth

                        <div class="mt-8 space-y-5">
                            @forelse($reviews as $review)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                            <div class="text-yellow-500 text-sm">{{ str_repeat('★', (int) $review->rating) }}</div>
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            {{ $review->created_at->format('d.m.Y') }}
                                        </div>
                                    </div>

                                    @if($review->comment)
                                        <div class="mt-3 text-sm text-gray-700">
                                            {{ $review->comment }}
                                        </div>
                                    @endif

                                    @if($review->images->isNotEmpty())
                                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach($review->images as $image)
                                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                                                    <img
                                                        src="{{ asset('storage/' . $image->image_path) }}"
                                                        class="w-full h-28 object-cover rounded-xl border border-gray-200"
                                                        alt="Review image"
                                                    >
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">
                                    Nu există review-uri încă.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Produse similare</h3>
                        <p class="text-sm text-gray-500">Din aceeași categorie/subcategorie.</p>

                        @if($similarProducts->isEmpty())
                            <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                                Nu există produse similare momentan.
                            </div>
                        @else
                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($similarProducts as $p)
                                    @include('shop.partials.product-card', ['product' => $p])
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>