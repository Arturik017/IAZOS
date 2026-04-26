<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $subcategoryId = $request->query('subcategory_id');
        $categorySlug = $request->query('category_slug');
        $subcategorySlug = $request->query('subcategory_slug');
        $brand = trim((string) $request->query('brand', ''));
        $sort = (string) $request->query('sort', 'new');

        $products = Product::query()
            ->with([
                'seller.sellerProfile',
                'category',
                'subcategory',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($subcategoryId, function ($query) use ($subcategoryId) {
                $query->where('subcategory_id', $subcategoryId);
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $category = Category::where('slug', $categorySlug)->first();
                if (!$category) {
                    $query->whereRaw('1=0');
                    return;
                }

                $query->where(function ($innerQuery) use ($category) {
                    $innerQuery->where('category_id', $category->id)
                        ->orWhere('subcategory_id', $category->id);
                });
            })
            ->when($subcategorySlug, function ($query) use ($subcategorySlug) {
                $subcategory = Category::where('slug', $subcategorySlug)->first();
                if (!$subcategory) {
                    $query->whereRaw('1=0');
                    return;
                }

                $query->where('subcategory_id', $subcategory->id);
            })
            ->when($brand !== '', function ($query) use ($brand) {
                $query->where(function ($innerQuery) use ($brand) {
                    $innerQuery->whereRaw('LOWER(COALESCE(brand, "")) = ?', [Str::lower($brand)])
                        ->orWhereRaw('LOWER(COALESCE(brand, "")) = ?', [str_replace('-', ' ', Str::lower($brand))]);
                });
            });

        $this->applyDynamicAttributeFilters($request, $products);

        if ($sort === 'price_asc') {
            $products->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $products->orderBy('final_price', 'desc');
        } else {
            $products->orderByDesc('id');
        }

        $products = $products->paginate(20);

        return response()->json([
            'ok' => true,
            'products' => $products->getCollection()->map(fn ($product) => $this->productCard($product))->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->isVisibleInMarketplace()) {
            abort(404);
        }

        $product->load([
            'seller.sellerProfile',
            'category',
            'subcategory',
            'reviews.user',
            'reviews.images',
            'images',
            'attributes.attribute',
            'attributes.option',
            'variants.attributes.attribute',
            'variants.attributes.option',
        ]);

        return response()->json([
            'ok' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'final_price' => (float) $product->final_price,
                'stock' => (int) $product->stock,
                'status' => (int) $product->status,
                'brand' => $product->brand,
                'image_url' => MediaUrl::public($product->image),
                'primary_image_url' => MediaUrl::public($product->primary_image),
                'gallery' => $product->images->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => MediaUrl::public($image->path),
                    'is_primary' => (bool) $image->is_primary,
                    'sort_order' => (int) $image->sort_order,
                ])->values(),
                'is_promo' => (bool) $product->is_promo,
                'has_variants' => (bool) $product->has_variants,
                'average_rating' => $product->averageRating(),
                'reviews_count' => $product->reviewsCount(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'subcategory' => $product->subcategory ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->name,
                    'slug' => $product->subcategory->slug,
                ] : null,
                'seller' => $product->seller ? [
                    'id' => $product->seller->id,
                    'name' => $product->seller->name,
                    'shop_name' => $product->seller->sellerProfile->shop_name ?? null,
                    'avatar_url' => \App\Support\MediaUrl::public($product->seller->sellerProfile->avatar_path ?? null),
                ] : null,
                'attributes' => $product->attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'slug' => $attribute->attribute->slug ?? null,
                        'name' => $attribute->attribute->name ?? null,
                        'type' => $attribute->attribute->type ?? null,
                        'value' => $attribute->option->label
                            ?? $attribute->value_text
                            ?? $attribute->value_number
                            ?? $attribute->value_boolean,
                        'unit' => $attribute->unit,
                    ];
                })->values(),
                'variants' => $product->variants->where('is_active', true)->map(function ($variant) {
                    $attributes = $variant->attributes->map(fn ($attribute) => [
                        'slug' => $attribute->attribute->slug ?? null,
                        'name' => $attribute->attribute->name ?? null,
                        'value' => $attribute->option->label ?? $attribute->custom_value,
                    ])->filter(fn ($attribute) => !empty($attribute['name']) && !empty($attribute['value']))->values()->all();

                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => !is_null($variant->price) ? (float) $variant->price : null,
                        'stock' => (int) $variant->stock,
                        'image_url' => MediaUrl::public($variant->image),
                        'is_active' => (bool) $variant->is_active,
                        'attributes' => $attributes,
                    ];
                })->filter(fn ($variant) => !empty($variant['attributes']))->values(),
                'reviews' => $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => (int) $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at?->toDateTimeString(),
                        'user' => [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ],
                        'images' => $review->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => MediaUrl::public($image->image_path),
                            ];
                        })->values(),
                    ];
                })->values(),
            ],
        ]);
    }

    private function productCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand,
            'final_price' => (float) $product->final_price,
            'stock' => (int) $product->stock,
            'image_url' => MediaUrl::public($product->image),
            'is_promo' => (bool) $product->is_promo,
            'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'reviews_count' => (int) ($product->reviews_count ?? 0),
            'seller' => $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->name,
                'shop_name' => $product->seller->sellerProfile->shop_name ?? null,
                'avatar_url' => \App\Support\MediaUrl::public($product->seller->sellerProfile->avatar_path ?? null),
            ] : null,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'subcategory' => $product->subcategory ? [
                'id' => $product->subcategory->id,
                'name' => $product->subcategory->name,
            ] : null,
        ];
    }

    private function applyDynamicAttributeFilters(Request $request, $query): void
    {
        $filters = $request->input('filters', []);

        if (!is_array($filters) || empty($filters)) {
            return;
        }

        $definitions = CategoryAttribute::query()
            ->where('is_filterable', true)
            ->get()
            ->groupBy('slug')
            ->map(fn (Collection $group) => $group->first());

        foreach ($filters as $slug => $input) {
            /** @var CategoryAttribute|null $definition */
            $definition = $definitions->get($slug);

            if (!$definition) {
                continue;
            }

            if ($definition->type === 'boolean') {
                if (!filter_var($input, FILTER_VALIDATE_BOOLEAN)) {
                    continue;
                }

                $query->whereHas('attributes', function ($attributeQuery) use ($slug) {
                    $attributeQuery->whereHas('attribute', fn ($q) => $q->where('slug', $slug))
                        ->where('value_boolean', true);
                });

                continue;
            }

            if ($definition->type === 'number') {
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

                if (in_array($definition->type, ['select', 'multiselect'], true)) {
                    $attributeQuery->whereHas('option', fn ($q) => $q->whereIn('value', $values));
                    return;
                }

                $attributeQuery->whereIn('value_text', $values);
            });
        }
    }
}
