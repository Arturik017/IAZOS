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
        $hasVariants = isset($variants) && $variants->count();
        $canShowSellerFollowButton = auth()->check()
            && \App\Models\User::supportsSellerFollowers()
            && auth()->id() !== $product->seller_id;
        $isFollowingSeller = $canShowSellerFollowButton
            ? auth()->user()->isFollowingSeller((int) $product->seller_id)
            : false;
        $sellerAvatar = $product->seller?->sellerProfile?->avatar_path
            ? \App\Support\MediaUrl::public($product->seller->sellerProfile->avatar_path)
            : null;
        $sellerInitial = strtoupper(mb_substr($product->seller?->sellerProfile?->shop_name ?? $product->seller?->name ?? 'S', 0, 1));
        $sellerStoryIds = collect($sellerActiveStoryIds ?? [])->values()->all();
        $sellerHasStories = count($sellerStoryIds) > 0;
        $isWishlisted = auth()->check() ? \App\Support\WishlistState::has((int) $product->id) : false;
        $wishlistedVariantIds = auth()->check()
            ? \App\Support\WishlistState::items()
                ->where('product_id', (int) $product->id)
                ->pluck('variant_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : [];
    @endphp

    <div class="market-page py-10">
        <div class="market-shell mx-auto px-4 sm:px-6 lg:px-8">
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
                        class="mt-3 lg:mt-0 lg:sticky lg:top-24 lg:block"
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

                    <div class="market-section bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="market-gallery-frame bg-gray-50">
                                @if($product->image)
                                    <img id="mainImage" src="{{ \App\Support\MediaUrl::public($product->image) }}"
                                         class="w-full h-[320px] sm:h-[420px] object-cover"
                                         alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-[320px] sm:h-[420px] flex items-center justify-center text-gray-400">
                                        Fără imagine
                                    </div>
                                @endif
                            </div>

                            <div class="market-buy-panel p-6 sm:p-8 lg:sticky lg:top-28">
                                <div class="flex items-center justify-between gap-3">
                                    <div id="priceBox" class="market-price text-3xl font-extrabold text-gray-900">
                                        {{ number_format($product->final_price, 2) }} MDL
                                    </div>

                                    @if((int)$product->stock > 0)
                                        <span id="stockBadge" class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            În stoc
                                        </span>
                                    @else
                                        <span id="stockBadge" class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
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
                                    Stoc:
                                    <span id="stockValue" class="font-semibold text-gray-900">{{ $product->stock }}</span>
                                </div>

                                @if($hasVariants)
                                    <div class="mt-5">
                                        <div class="text-sm font-semibold text-gray-900 mb-2">Alege variantă</div>

                                        <select id="variantSelect" class="w-full border rounded-xl p-3">
                                            <option value="">— Selectează —</option>

                                            @foreach($variants as $variant)
                                                <option
                                                    value="{{ $variant['id'] }}"
                                                    data-price="{{ $variant['price'] }}"
                                                    data-stock="{{ $variant['stock'] }}"
                                                    data-image="{{ $variant['image'] }}"
                                                >
                                                    {{ $variant['label'] ?? collect($variant['attributes'])->map(fn($a) => ($a['name'] ?? '') . ': ' . ($a['value'] ?? ''))->implode(' / ') }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <p class="mt-2 text-xs text-gray-500">
                                            Selectarea variantei actualizează prețul, stocul și imaginea.
                                        </p>
                                    </div>
                                @endif

                                @if($product->seller && $product->seller->sellerProfile)
    <div class="market-trust-card mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <div class="market-eyebrow">Vandut de</div>
        <div class="mt-3 flex items-center justify-between gap-3">
            @if($sellerHasStories)
                <a
                    href="{{ route('seller.public.show', ['user' => $product->seller->id, 'story' => 1]) }}"
                    class="story-ring h-14 w-14 shrink-0 rounded-full p-[3px]"
                    data-story-ids='@json($sellerStoryIds)'
                    aria-label="Deschide story-urile sellerului"
                >
                    <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-white">
                        @if($sellerAvatar)
                            <img src="{{ $sellerAvatar }}"
                                 alt="{{ $product->seller->sellerProfile->shop_name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-lg font-bold text-gray-500">
                                {{ $sellerInitial }}
                            </div>
                        @endif
                    </div>
                </a>
            @else
                <div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 bg-white">
                    @if($sellerAvatar)
                        <img src="{{ $sellerAvatar }}"
                             alt="{{ $product->seller->sellerProfile->shop_name }}"
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-lg font-bold text-gray-500">
                            {{ $sellerInitial }}
                        </div>
                    @endif
                </div>
            @endif

            <div>
                <a href="{{ route('seller.public.show', $product->seller->id) }}"
                   class="inline-block text-base font-semibold text-blue-600 hover:text-blue-700">
                    {{ $product->seller->sellerProfile->shop_name }}
                </a>

                @if($product->seller->sellerProfile->phone)
                    <div class="mt-1 text-sm text-gray-600">
                        Telefon: {{ $product->seller->sellerProfile->phone }}
                    </div>
                @endif

                <div class="mt-1 text-sm text-gray-500">
                    {{ $product->seller->followers_count ?? 0 }} urmaritori
                </div>
            </div>

            @if($canShowSellerFollowButton)
                <div class="shrink-0 space-y-2">
                    @if($isFollowingSeller)
                        <form method="POST" action="{{ route('seller.follow.destroy', $product->seller->id) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                            >
                                Nu mai urmari
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('seller.follow.store', $product->seller->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black"
                            >
                                Urmareste
                            </button>
                        </form>
                    @endif

                    @if(auth()->check() && \App\Models\User::supportsMessaging() && auth()->user()->role !== 'seller')
                        <form method="POST" action="{{ route('messages.start_seller', $product->seller->id) }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                            >
                                Mesaj
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
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
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="w-full" id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="variant_id" id="addToCartVariantId">

                                        <button
                                            type="submit"
                                            id="addToCartBtn"
                                            class="w-full px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                                            {{ $hasVariants ? 'disabled' : (((int)$product->stock <= 0 || (int)$product->status !== 1) ? 'disabled' : '') }}
                                        >
                                            Adaugă în coș
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.buy', $product) }}" class="w-full" id="buyNowForm">
                                        @csrf
                                        <input type="hidden" name="variant_id" id="buyNowVariantId">

                                        <button
                                            type="submit"
                                            id="buyNowBtn"
                                            class="w-full px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition disabled:opacity-50"
                                            {{ $hasVariants ? 'disabled' : (((int)$product->stock <= 0 || (int)$product->status !== 1) ? 'disabled' : '') }}
                                        >
                                            Cumpără acum
                                        </button>
                                    </form>
                                </div>

                                @auth
                                    <div class="mt-3">
                                        <form method="POST" action="{{ route('wishlist.store', $product) }}" id="wishlistStoreForm" class="{{ $hasVariants ? '' : ($isWishlisted ? 'hidden' : '') }}">
                                            @csrf
                                            @if($hasVariants)
                                                <input type="hidden" name="variant_id" id="wishlistStoreVariantId">
                                            @endif
                                            <button type="submit" id="wishlistStoreBtn" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50" {{ $hasVariants ? 'disabled' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                                                </svg>
                                                <span id="wishlistStoreText">{{ $hasVariants ? 'Alege varianta pentru favorite' : 'Adauga la favorite' }}</span>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('wishlist.destroy', $product) }}" id="wishlistDestroyForm" class="{{ $hasVariants ? 'hidden' : ($isWishlisted ? '' : 'hidden') }}">
                                            @csrf
                                            @method('DELETE')
                                            @if($hasVariants)
                                                <input type="hidden" name="variant_id" id="wishlistDestroyVariantId">
                                            @endif
                                            <button type="submit" id="wishlistDestroyBtn" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-gray-50" {{ $hasVariants ? 'disabled' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z"/>
                                                </svg>
                                                Scoate din favorite
                                            </button>
                                        </form>
                                    </div>
                                @endauth

                                @if($hasVariants)
                                    <div id="variantHelpText" class="mt-3 text-xs text-amber-600 font-medium">
                                        Alege mai întâi o variantă pentru a continua.
                                    </div>
                                @endif

                                <div class="mt-4 text-xs text-gray-400">
                                    Prețurile includ vama și livrarea în Moldova.
                                </div>

                                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="rounded-xl border border-gray-200 bg-white px-3 py-3">
                                        <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#066e97]">Info</div>
                                        <div class="mt-1 text-xs text-gray-600">Pret vizibil si actualizat.</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-white px-3 py-3">
                                        <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#066e97]">Seller</div>
                                        <div class="mt-1 text-xs text-gray-600">Profil verificabil in marketplace.</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-white px-3 py-3">
                                        <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#066e97]">Review</div>
                                        <div class="mt-1 text-xs text-gray-600">Rating separat si usor de scanat.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="market-section bg-white rounded-xl shadow border border-gray-100 p-5 sm:p-6">
                        <div class="market-section-header">
                            <div class="market-eyebrow">Q&A</div>
                            <h3 class="text-lg font-semibold text-gray-900">Întrebări despre produs</h3>
                            <p class="text-sm text-gray-500">Guest și userii care nu au cumpărat pot pune doar întrebări.</p>
                        </div>

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

                                    @if($question->answer)
                                        <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">
                                            <div class="text-sm font-semibold text-gray-900">Răspuns seller</div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                {{ $question->answer }}
                                            </div>
                                        </div>
                                    @endif

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

                    <div class="market-section bg-white rounded-xl shadow border border-gray-100 p-5 sm:p-6">
                        <div class="market-section-header">
                            <div class="market-eyebrow">Rating</div>
                            <h3 class="text-lg font-semibold text-gray-900">Review-uri produs</h3>
                            <p class="text-sm text-gray-500">Doar cumpărătorii reali pot lăsa rating, comentariu și poze.</p>
                        </div>

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
                                                @php
                                                    $reviewImageUrl = \App\Support\MediaUrl::public($image->image_path);
                                                @endphp

                                                @if($reviewImageUrl)
                                                <a href="{{ $reviewImageUrl }}" target="_blank">
                                                    <img
                                                        src="{{ $reviewImageUrl }}"
                                                        class="w-full h-28 object-cover rounded-xl border border-gray-200"
                                                        alt="Review image"
                                                    >
                                                </a>
                                                @endif
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

                    <div class="market-section bg-white rounded-xl shadow border border-gray-100 p-5 sm:p-6">
                        <div class="market-section-header">
                            <div class="market-eyebrow">Descopera</div>
                            <h3 class="text-lg font-semibold text-gray-900">Produse similare</h3>
                            <p class="text-sm text-gray-500">Din aceeași categorie/subcategorie.</p>
                        </div>

                        @if($similarProducts->isEmpty())
                            <div class="market-empty mt-5">
                                <div class="market-empty-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M6 11h12M8 15h8" />
                                    </svg>
                                </div>
                                <div class="mt-5 font-semibold text-gray-900">Nu există produse similare momentan.</div>
                                <div class="mt-1 text-sm text-gray-500">Revino mai tarziu pentru recomandari din aceeasi zona.</div>
                            </div>
                        @else
                            <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
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

    @if($hasVariants)
        <script>
            const variantSelect = document.getElementById('variantSelect');
            const priceBox = document.getElementById('priceBox');
            const mainImage = document.getElementById('mainImage');
            const stockValue = document.getElementById('stockValue');
            const stockBadge = document.getElementById('stockBadge');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const buyNowBtn = document.getElementById('buyNowBtn');
            const addToCartVariantId = document.getElementById('addToCartVariantId');
            const buyNowVariantId = document.getElementById('buyNowVariantId');
            const variantHelpText = document.getElementById('variantHelpText');
            const addToCartForm = document.getElementById('addToCartForm');
            const buyNowForm = document.getElementById('buyNowForm');
            const wishlistStoreForm = document.getElementById('wishlistStoreForm');
            const wishlistDestroyForm = document.getElementById('wishlistDestroyForm');
            const wishlistStoreVariantId = document.getElementById('wishlistStoreVariantId');
            const wishlistDestroyVariantId = document.getElementById('wishlistDestroyVariantId');
            const wishlistStoreBtn = document.getElementById('wishlistStoreBtn');
            const wishlistDestroyBtn = document.getElementById('wishlistDestroyBtn');
            const wishlistStoreText = document.getElementById('wishlistStoreText');

            const defaultPrice = @json((float) $product->final_price);
            const defaultStock = @json((int) $product->stock);
            const defaultImage = @json($product->image ? \App\Support\MediaUrl::public($product->image) : null);
            const wishlistedVariantIds = @json($wishlistedVariantIds);

            function setButtonsDisabled(disabled) {
                addToCartBtn.disabled = disabled;
                buyNowBtn.disabled = disabled;
            }

            function syncWishlistState(variantId) {
                if (!wishlistStoreForm || !wishlistDestroyForm || !wishlistStoreBtn || !wishlistDestroyBtn) {
                    return;
                }

                if (!variantId) {
                    wishlistStoreForm.classList.remove('hidden');
                    wishlistDestroyForm.classList.add('hidden');
                    wishlistStoreBtn.disabled = true;
                    wishlistDestroyBtn.disabled = true;

                    if (wishlistStoreVariantId) {
                        wishlistStoreVariantId.value = '';
                    }

                    if (wishlistDestroyVariantId) {
                        wishlistDestroyVariantId.value = '';
                    }

                    if (wishlistStoreText) {
                        wishlistStoreText.textContent = 'Alege varianta pentru favorite';
                    }

                    return;
                }

                const numericVariantId = Number(variantId);
                const alreadyWishlisted = wishlistedVariantIds.includes(numericVariantId);

                if (wishlistStoreVariantId) {
                    wishlistStoreVariantId.value = numericVariantId;
                }

                if (wishlistDestroyVariantId) {
                    wishlistDestroyVariantId.value = numericVariantId;
                }

                wishlistStoreBtn.disabled = false;
                wishlistDestroyBtn.disabled = false;

                if (alreadyWishlisted) {
                    wishlistStoreForm.classList.add('hidden');
                    wishlistDestroyForm.classList.remove('hidden');
                } else {
                    wishlistStoreForm.classList.remove('hidden');
                    wishlistDestroyForm.classList.add('hidden');

                    if (wishlistStoreText) {
                        wishlistStoreText.textContent = 'Adauga varianta la favorite';
                    }
                }
            }

            function setStockBadge(stock) {
                if (parseInt(stock) > 0) {
                    stockBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                    stockBadge.textContent = 'În stoc';
                } else {
                    stockBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
                    stockBadge.textContent = 'Stoc epuizat';
                }
            }

            function requireVariantBeforeSubmit(e) {
                if (!variantSelect.value) {
                    e.preventDefault();
                    if (variantHelpText) {
                        variantHelpText.textContent = 'Alege mai întâi o variantă pentru a continua.';
                    }
                    variantSelect.focus();
                    return false;
                }

                addToCartVariantId.value = variantSelect.value;
                buyNowVariantId.value = variantSelect.value;
                return true;
            }

            variantSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const variantId = selected.value;
                const price = selected.getAttribute('data-price');
                const stock = selected.getAttribute('data-stock');
                const image = selected.getAttribute('data-image');

                if (!variantId) {
                    priceBox.innerText = Number(defaultPrice).toFixed(2) + ' MDL';
                    stockValue.innerText = defaultStock;
                    setStockBadge(defaultStock);
                    if (defaultImage && mainImage) {
                        mainImage.src = defaultImage;
                    }
                    addToCartVariantId.value = '';
                    buyNowVariantId.value = '';
                    syncWishlistState('');
                    setButtonsDisabled(true);
                    if (variantHelpText) {
                        variantHelpText.textContent = 'Alege mai întâi o variantă pentru a continua.';
                    }
                    return;
                }

                priceBox.innerText = Number(price).toFixed(2) + ' MDL';
                stockValue.innerText = stock;
                setStockBadge(stock);

                if (image && mainImage) {
                    mainImage.src = image;
                }

                addToCartVariantId.value = variantId;
                buyNowVariantId.value = variantId;
                syncWishlistState(variantId);

                const outOfStock = parseInt(stock) <= 0;
                setButtonsDisabled(outOfStock);

                if (variantHelpText) {
                    variantHelpText.textContent = outOfStock
                        ? 'Varianta selectată nu este în stoc.'
                        : 'Varianta selectată este gata pentru comandă.';
                }
            });

            addToCartForm.addEventListener('submit', requireVariantBeforeSubmit);
            buyNowForm.addEventListener('submit', requireVariantBeforeSubmit);

            setButtonsDisabled(true);
            syncWishlistState('');
        </script>
    @endif

    <script>
        (function () {
            const seen = JSON.parse(window.localStorage.getItem('iazos_seen_stories') || '{}');
            document.querySelectorAll('.story-ring[data-story-ids]').forEach((element) => {
                try {
                    const ids = JSON.parse(element.dataset.storyIds || '[]');
                    const allSeen = ids.length > 0 && ids.every((id) => !!seen[String(id)]);
                    element.classList.add(allSeen ? 'bg-gray-300' : 'bg-gradient-to-br', ...(allSeen ? [] : ['from-orange-400', 'via-fuchsia-500', 'to-blue-500']));
                } catch (e) {
                    element.classList.add('bg-gradient-to-br', 'from-orange-400', 'via-fuchsia-500', 'to-blue-500');
                }
            });
        })();
    </script>
</x-app-layout>

