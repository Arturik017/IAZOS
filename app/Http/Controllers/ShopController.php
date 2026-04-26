<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\CategoryAttribute;
use App\Models\ProductAttribute;
use App\Models\SellerStory;
use App\Models\User;
use App\Models\OrderItem;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index()
    {
        $categories = $this->shopCategories();

        $banners = Banner::orderByDesc('id')->get()->map(function ($b) {
            return [
                'image' => MediaUrl::public($b->image),
                'title' => $b->title ?? null,
                'subtitle' => $b->subtitle ?? null,
                'kicker' => $b->kicker ?? null,
            ];
        })->values();

        $promoProducts = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace()
            ->where('is_promo', 1)
            ->orderByDesc('id')
            ->get();

        $followedPromoProducts = collect();

        if (auth()->check() && User::supportsSellerFollowers()) {
            $followedSellerIds = auth()->user()->followedSellers()->pluck('users.id');

            if ($followedSellerIds->isNotEmpty()) {
                $followedPromoProducts = Product::query()
                    ->with(['seller.sellerProfile'])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->visibleInMarketplace()
                    ->where('is_promo', 1)
                    ->whereIn('seller_id', $followedSellerIds)
                    ->orderByDesc('id')
                    ->get();
            }
        }

        $popularBrands = $this->popularBrands();
        $sellerStories = $this->homeSellerStories();
        $recommendedProducts = $followedPromoProducts->isNotEmpty() ? $followedPromoProducts : $promoProducts;
        $recommendedTitle = $followedPromoProducts->isNotEmpty()
            ? 'Promoții de la sellerii urmăriți'
            : 'Recomandate pentru tine';
        $recommendedSubtitle = $followedPromoProducts->isNotEmpty()
            ? 'Vezi rapid produsele promoționale de la sellerii pe care îi urmărești.'
            : 'Un grid mare de produse pentru browsing real de marketplace, clar si usor de parcurs.';

        return view('shop.home', compact(
            'categories',
            'banners',
            'promoProducts',
            'popularBrands',
            'sellerStories',
            'recommendedProducts',
            'recommendedTitle',
            'recommendedSubtitle'
        ));
    }

    public function show(Product $product)
    {
        if (!$product->isVisibleInMarketplace()) {
            abort(404);
        }

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $product->load([
            'seller.sellerProfile',
            'category',
            'subcategory',
            'reviews.user',
            'reviews.images',
            'questions.user',
            'questions.answeredBy',
            'images',
            'variants.attributes.attribute',
            'variants.attributes.option',
        ]);
        if (User::supportsSellerFollowers()) {
            $product->seller?->loadCount('followers');
        }

        $sellerActiveStoryIds = collect();
        if (User::supportsSellerStories() && $product->seller_id) {
            $sellerActiveStoryIds = SellerStory::query()
                ->where('seller_id', $product->seller_id)
                ->active()
                ->latest()
                ->pluck('id');
        }

        $variants = $product->variants
            ->where('is_active', true)
            ->map(function ($variant) {
                $attributes = $variant->attributes->map(function ($attr) {
                    $value = $attr->option->label
                        ?? $attr->custom_value
                        ?? '';

                    return [
                        'name' => $attr->attribute->name ?? '',
                        'value' => $value,
                    ];
                })
                    ->filter(fn ($item) => ($item['name'] ?? '') !== '' && ($item['value'] ?? '') !== '')
                    ->values()
                    ->toArray();

                $label = collect($attributes)
                    ->map(fn ($item) => ($item['name'] ?? '') . ': ' . ($item['value'] ?? ''))
                    ->implode(' / ');

                return [
                    'id' => $variant->id,
                    'price' => !is_null($variant->price) ? (float) $variant->price : null,
                    'stock' => (int) $variant->stock,
                    'image' => MediaUrl::public($variant->image),
                    'attributes' => $attributes,
                    'label' => $label !== '' ? $label : ('Varianta #' . $variant->id),
                ];
            })
            ->filter(fn ($variant) => !empty($variant['attributes']))
            ->values();

        $query = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace()
            ->where('id', '!=', $product->id)
            ;

        if (!empty($product->subcategory_id)) {
            $query->where('subcategory_id', $product->subcategory_id);
        } elseif (!empty($product->category_id)) {
            $query->where('category_id', $product->category_id);
        } else {
            $query->whereRaw('1=0');
        }

        $similarProducts = $query->orderByDesc('id')->limit(6)->get();

        $reviews = $product->reviews()
            ->with(['user', 'images'])
            ->latest()
            ->get();

        $questions = $product->questions()
            ->with(['user', 'answeredBy'])
            ->latest()
            ->get();

        $canReview = false;
        $myReview = null;

        if (auth()->check()) {
            $canReview = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', auth()->id())
                        ->where('payment_status', 'paid');
                })
                ->exists();

            $myReview = $reviews->firstWhere('user_id', auth()->id());
        }

        return view('shop.product', compact(
            'categories',
            'product',
            'variants',
            'similarProducts',
            'reviews',
            'questions',
            'canReview',
            'myReview',
            'sellerActiveStoryIds'
        ));
    }

    public function category(Request $request, Category $category)
    {
        return $this->renderProductListing(
            $request,
            $this->baseCatalogQuery()->where(function ($query) use ($category) {
                $query->where('category_id', $category->id)
                    ->orWhere('subcategory_id', $category->id);
            }),
            $category,
            route('category.show', $category),
            $category->name,
            'Produse in aceasta categorie'
        );
    }

    public function subcategory(Request $request, Category $category)
    {
        return $this->renderProductListing(
            $request,
            $this->baseCatalogQuery()->where('subcategory_id', $category->id),
            $category,
            route('subcategory.show', $category),
            $category->name,
            'Produse in aceasta subcategorie'
        );
    }

    public function brand(Request $request, string $brand)
    {
        $brandLabel = $this->resolveBrandLabel($brand);

        $query = $this->baseCatalogQuery()->where(function ($query) use ($brandLabel, $brand) {
            $query->whereRaw('LOWER(COALESCE(brand, "")) = ?', [Str::lower($brandLabel)])
                ->orWhereRaw('LOWER(COALESCE(brand, "")) = ?', [str_replace('-', ' ', Str::lower($brand))])
                ->orWhereHas('attributes', function ($attributeQuery) use ($brandLabel, $brand) {
                    $attributeQuery->whereHas('attribute', fn ($q) => $q->where('slug', 'brand'))
                        ->where(function ($valueQuery) use ($brandLabel, $brand) {
                            $valueQuery->whereRaw('LOWER(COALESCE(value_text, "")) = ?', [Str::lower($brandLabel)])
                                ->orWhereHas('option', function ($optionQuery) use ($brandLabel, $brand) {
                                    $optionQuery->whereRaw('LOWER(label) = ?', [Str::lower($brandLabel)])
                                        ->orWhere('value', Str::slug($brand))
                                        ->orWhere('value', Str::slug($brandLabel));
                                });
                        });
                });
        });

        return $this->renderProductListing(
            $request,
            $query,
            null,
            route('brand.show', $brand),
            $brandLabel,
            'Brand popular in marketplace'
        );
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        $sellers = collect();

        if ($q !== '') {
            $sellers = User::query()
                ->where('role', 'seller')
                ->where('seller_status', 'approved')
                ->whereHas('sellerProfile', function ($query) use ($q) {
                    $query->where('shop_name', 'like', "%{$q}%")
                        ->orWhere('legal_name', 'like', "%{$q}%")
                        ->orWhere('notes', 'like', "%{$q}%")
                        ->orWhere('pickup_address', 'like', "%{$q}%");
                })
                ->with('sellerProfile')
                ->withAvg('sellerReviewsReceived', 'rating')
                ->withCount('sellerReviewsReceived')
                ->orderByDesc('id')
                ->limit(12)
                ->get();
        }

        return view('shop.search', compact('categories', 'products', 'q', 'sellers'));
    }

    public function create()
    {
        if (!auth()->check()) {
            session()->put('url.intended', url()->current());
            return redirect()->route('register')->with('error', 'Pentru a finaliza comanda, creează cont.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Coșul este gol.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['qty']);
        }

        $districts = [];
        $localitiesMap = [];

        if (Storage::exists('md/locations.json')) {
            $locations = json_decode(Storage::get('md/locations.json'), true);
            $districts = $locations['districts'] ?? [];
            $localitiesMap = $locations['localities'] ?? [];
        }

        return view('shop.checkout', compact('cart', 'subtotal', 'districts', 'localitiesMap'));
    }

    private function shopCategories()
    {
        return Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function baseCatalogQuery()
    {
        return Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace();
    }

    private function renderProductListing(
        Request $request,
        $query,
        ?Category $category,
        string $resetUrl,
        string $listingTitle,
        string $listingSubtitle
    ) {
        $categories = $this->shopCategories();
        $filterDefinitions = $this->buildFilterDefinitions($query, $category);

        $this->applyBaseFilters($request, $query);
        $this->applyAttributeFilters($request, $query, $filterDefinitions);
        $this->applySort($request, $query);

        $products = $query->paginate(24)->withQueryString();

        return view('shop.category', compact(
            'categories',
            'category',
            'products',
            'filterDefinitions',
            'listingTitle',
            'listingSubtitle',
            'resetUrl'
        ));
    }

    private function applyBaseFilters(Request $request, $query): void
    {
        if ($request->boolean('promo')) {
            $query->where('is_promo', 1);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->filled('min_price')) {
            $query->where('final_price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('final_price', '<=', (float) $request->max_price);
        }
    }

    private function applySort(Request $request, $query): void
    {
        $sort = $request->get('sort', 'new');

        if ($sort === 'price_asc') {
            $query->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('final_price', 'desc');
        } else {
            $query->orderByDesc('id');
        }
    }

    private function buildFilterDefinitions($query, ?Category $category): Collection
    {
        $categoryIds = $category ? $this->collectCategoryIds($category) : [];

        if (empty($categoryIds)) {
            return collect();
        }

        $definitions = CategoryAttribute::query()
            ->with('options')
            ->whereIn('category_id', $categoryIds)
            ->where('is_filterable', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('slug')
            ->map(function (Collection $group) {
                /** @var CategoryAttribute $first */
                $first = $group->first();

                return [
                    'name' => $first->name,
                    'slug' => $first->slug,
                    'type' => $first->type,
                    'options' => $group->flatMap(fn ($attr) => $attr->options)
                        ->unique('value')
                        ->sortBy('sort_order')
                        ->values()
                        ->map(fn ($option) => [
                            'value' => $option->value,
                            'label' => $option->label,
                        ])->all(),
                ];
            })
            ->values();

        $productIds = (clone $query)->select('products.id')->pluck('products.id');

        if ($productIds->isEmpty()) {
            return $definitions;
        }

        return $definitions->map(function (array $definition) use ($productIds) {
            if ($definition['type'] === 'text') {
                $definition['text_values'] = ProductAttribute::query()
                    ->join('category_attributes', 'category_attributes.id', '=', 'product_attributes.category_attribute_id')
                    ->whereIn('product_attributes.product_id', $productIds)
                    ->where('category_attributes.slug', $definition['slug'])
                    ->whereNotNull('product_attributes.value_text')
                    ->where('product_attributes.value_text', '!=', '')
                    ->distinct()
                    ->orderBy('product_attributes.value_text')
                    ->limit(18)
                    ->pluck('product_attributes.value_text')
                    ->values()
                    ->all();
            }

            if ($definition['type'] === 'number') {
                $range = ProductAttribute::query()
                    ->join('category_attributes', 'category_attributes.id', '=', 'product_attributes.category_attribute_id')
                    ->whereIn('product_attributes.product_id', $productIds)
                    ->where('category_attributes.slug', $definition['slug'])
                    ->selectRaw('MIN(product_attributes.value_number) as min_value, MAX(product_attributes.value_number) as max_value')
                    ->first();

                $definition['min_value'] = $range?->min_value;
                $definition['max_value'] = $range?->max_value;
            }

            return $definition;
        });
    }

    private function applyAttributeFilters(Request $request, $query, Collection $definitions): void
    {
        foreach ($definitions as $definition) {
            $slug = $definition['slug'];
            $input = $request->input("filters.$slug");

            if ($definition['type'] === 'boolean') {
                if (!$request->boolean("filters.$slug")) {
                    continue;
                }

                $query->whereHas('attributes', function ($attributeQuery) use ($slug) {
                    $attributeQuery->whereHas('attribute', fn ($q) => $q->where('slug', $slug))
                        ->where('value_boolean', true);
                });

                continue;
            }

            if ($definition['type'] === 'number') {
                $min = data_get($input, 'min');
                $max = data_get($input, 'max');

                if ($min === null && $max === null) {
                    continue;
                }

                $query->whereHas('attributes', function ($attributeQuery) use ($slug, $min, $max) {
                    $attributeQuery->whereHas('attribute', fn ($q) => $q->where('slug', $slug));

                    if ($min !== null && $min !== '') {
                        $attributeQuery->where('value_number', '>=', (float) $min);
                    }

                    if ($max !== null && $max !== '') {
                        $attributeQuery->where('value_number', '<=', (float) $max);
                    }
                });

                continue;
            }

            $values = array_values(array_filter((array) $input, fn ($value) => $value !== null && $value !== ''));

            if (empty($values)) {
                continue;
            }

            $query->whereHas('attributes', function ($attributeQuery) use ($slug, $definition, $values) {
                $attributeQuery->whereHas('attribute', fn ($q) => $q->where('slug', $slug));

                if (in_array($definition['type'], ['select', 'multiselect'], true)) {
                    $attributeQuery->whereHas('option', fn ($q) => $q->whereIn('value', $values));
                    return;
                }

                $attributeQuery->whereIn('value_text', $values);
            });
        }
    }

    private function collectCategoryIds(Category $category): array
    {
        $category->loadMissing('childrenRecursive');

        $ids = [$category->id];
        foreach ($category->childrenRecursive as $child) {
            $ids = array_merge($ids, $this->collectCategoryIds($child));
        }

        return array_values(array_unique($ids));
    }

    private function popularBrands(): Collection
    {
        $brands = ProductAttribute::query()
            ->join('products', 'products.id', '=', 'product_attributes.product_id')
            ->leftJoin('users as product_sellers', 'product_sellers.id', '=', 'products.seller_id')
            ->join('category_attributes', 'category_attributes.id', '=', 'product_attributes.category_attribute_id')
            ->leftJoin('category_attribute_options', 'category_attribute_options.id', '=', 'product_attributes.option_id')
            ->where('products.status', 1)
            ->where('category_attributes.slug', 'brand')
            ->where(function ($query) {
                $query
                    ->where(function ($sellerQuery) {
                        $sellerQuery
                            ->whereNotNull('products.seller_id')
                            ->where('products.is_approved', true)
                            ->where('product_sellers.role', 'seller')
                            ->where('product_sellers.seller_status', 'approved');
                    })
                    ->orWhere(function ($legacyAdminQuery) {
                        $legacyAdminQuery
                            ->whereNull('products.seller_id')
                            ->whereNull('products.primary_image')
                            ->whereNull('products.proof_path')
                            ->where(function ($variantsQuery) {
                                $variantsQuery
                                    ->whereNull('products.has_variants')
                                    ->orWhere('products.has_variants', false);
                            });
                    });
            })
            ->get([
                'category_attribute_options.label as option_label',
                'product_attributes.value_text',
            ])
            ->map(function ($row) {
                $label = trim((string) ($row->option_label ?: $row->value_text));

                return [
                    'label' => $label,
                    'slug' => Str::slug($label),
                ];
            })
            ->filter(fn ($brand) => $brand['label'] !== '')
            ->groupBy('slug')
            ->map(function (Collection $group, string $slug) {
                $label = $group->first()['label'];

                return [
                    'slug' => $slug,
                    'label' => $label,
                    'count' => $group->count(),
                    'logo_url' => $this->brandLogoUrl($label),
                ];
            })
            ->sortByDesc('count')
            ->take(18)
            ->values();

        if ($brands->isNotEmpty()) {
            return $brands;
        }

        return collect([
            ['slug' => 'apple', 'label' => 'Apple', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Apple')],
            ['slug' => 'samsung', 'label' => 'Samsung', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Samsung')],
            ['slug' => 'xiaomi', 'label' => 'Xiaomi', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Xiaomi')],
            ['slug' => 'huawei', 'label' => 'Huawei', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Huawei')],
            ['slug' => 'lenovo', 'label' => 'Lenovo', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Lenovo')],
            ['slug' => 'hp', 'label' => 'HP', 'count' => 0, 'logo_url' => $this->brandLogoUrl('HP')],
            ['slug' => 'dell', 'label' => 'Dell', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Dell')],
            ['slug' => 'asus', 'label' => 'ASUS', 'count' => 0, 'logo_url' => $this->brandLogoUrl('ASUS')],
            ['slug' => 'sony', 'label' => 'Sony', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Sony')],
            ['slug' => 'lg', 'label' => 'LG', 'count' => 0, 'logo_url' => $this->brandLogoUrl('LG')],
            ['slug' => 'bosch', 'label' => 'Bosch', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Bosch')],
            ['slug' => 'nike', 'label' => 'Nike', 'count' => 0, 'logo_url' => $this->brandLogoUrl('Nike')],
        ]);
    }

    private function resolveBrandLabel(string $brand): string
    {
        $match = $this->popularBrands()->firstWhere('slug', $brand);

        return $match['label'] ?? Str::headline(str_replace('-', ' ', $brand));
    }

    private function brandLogoUrl(string $brand): ?string
    {
        $logos = [
            'apple' => 'https://cdn.simpleicons.org/apple/111111',
            'samsung' => 'https://cdn.simpleicons.org/samsung/1428A0',
            'xiaomi' => 'https://cdn.simpleicons.org/xiaomi/FF6900',
            'huawei' => 'https://cdn.simpleicons.org/huawei/FF0000',
            'google' => 'https://cdn.simpleicons.org/google/4285F4',
            'nothing' => 'https://cdn.simpleicons.org/nothing/111111',
            'lenovo' => 'https://cdn.simpleicons.org/lenovo/E2231A',
            'hp' => 'https://cdn.simpleicons.org/hp/0096D6',
            'dell' => 'https://cdn.simpleicons.org/dell/0076CE',
            'asus' => 'https://cdn.simpleicons.org/asus/000000',
            'acer' => 'https://cdn.simpleicons.org/acer/83B81A',
            'msi' => 'https://cdn.simpleicons.org/msi/FF0000',
            'lg' => 'https://cdn.simpleicons.org/lg/A50034',
            'sony' => 'https://cdn.simpleicons.org/sony/111111',
            'philips' => 'https://cdn.simpleicons.org/philips/0D47A1',
            'bosch' => 'https://cdn.simpleicons.org/bosch/EA0016',
            'beko' => 'https://cdn.simpleicons.org/beko/005AA9',
            'whirlpool' => 'https://cdn.simpleicons.org/whirlpool/F6AF2D',
            'nike' => 'https://cdn.simpleicons.org/nike/111111',
            'adidas' => 'https://cdn.simpleicons.org/adidas/111111',
            'puma' => 'https://cdn.simpleicons.org/puma/111111',
            'lego' => 'https://cdn.simpleicons.org/lego/D01012',
            'canon' => 'https://cdn.simpleicons.org/canon/EF323D',
            'nikon' => 'https://cdn.simpleicons.org/nikon/FFE100',
            'dji' => 'https://cdn.simpleicons.org/dji/111111',
            'jbl' => 'https://cdn.simpleicons.org/jbl/FF3300',
            'bose' => 'https://cdn.simpleicons.org/bose/111111',
            'logitech' => 'https://cdn.simpleicons.org/logitech/00B8FC',
            'razer' => 'https://cdn.simpleicons.org/razer/44D62C',
        ];

        return $logos[Str::slug($brand)] ?? null;
    }

    private function homeSellerStories(): Collection
    {
        if (!User::supportsSellerStories()) {
            return collect();
        }

        $stories = SellerStory::query()
            ->with(['seller.sellerProfile'])
            ->active()
            ->latest()
            ->get()
            ->groupBy('seller_id');

        if ($stories->isEmpty()) {
            return collect();
        }

        $followedIds = collect();
        if (auth()->check() && User::supportsSellerFollowers()) {
            $followedIds = auth()->user()->followedSellers()->pluck('users.id');
        }

        return $stories->map(function (Collection $items) use ($followedIds) {
            $seller = $items->first()->seller;

            return [
                'seller_id' => $seller->id,
                'seller_name' => $seller->sellerProfile->shop_name ?? $seller->name,
                'seller_avatar' => MediaUrl::public($seller->sellerProfile->avatar_path ?? null),
                'seller_url' => route('seller.public.show', $seller),
                'is_followed_priority' => $followedIds->contains($seller->id),
                'stories_count' => $items->count(),
                'stories' => $items->take(5)->map(function (SellerStory $story) {
                    return [
                        'id' => $story->id,
                        'media_type' => $story->media_type,
                        'media_url' => MediaUrl::public($story->media_path),
                        'caption' => $story->caption,
                        'expires_at' => $story->expires_at?->format('d.m H:i'),
                    ];
                })->values(),
            ];
        })->sortByDesc('is_followed_priority')->values();
    }
}
