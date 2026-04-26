<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function tree()
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'ok' => true,
            'categories' => $categories->map(fn (Category $category) => $this->serializeCategoryNode($category))->values(),
        ]);
    }

    public function category(Category $category)
    {
        $category->loadMissing(['parent', 'childrenRecursive']);

        return response()->json([
            'ok' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
                'parent' => $category->parent ? [
                    'id' => $category->parent->id,
                    'name' => $category->parent->name,
                    'slug' => $category->parent->slug,
                ] : null,
                'is_leaf' => $category->childrenRecursive->isEmpty(),
                'children' => $category->childrenRecursive->map(fn (Category $child) => $this->serializeCategoryNode($child))->values(),
                'path' => $this->categoryPath($category),
            ],
        ]);
    }

    public function filters(Category $category)
    {
        $query = Product::query()
            ->visibleInMarketplace()
            ->whereIn('subcategory_id', $this->collectCategoryIds($category));

        if ((clone $query)->count() === 0) {
            $query = Product::query()
                ->visibleInMarketplace()
                ->where(function ($innerQuery) use ($category) {
                    $innerQuery->where('category_id', $category->id)
                        ->orWhere('subcategory_id', $category->id);
                });
        }

        $definitions = $this->buildFilterDefinitions($query, $category);

        return response()->json([
            'ok' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'filters' => $definitions->values(),
        ]);
    }

    public function brands()
    {
        return response()->json([
            'ok' => true,
            'brands' => $this->popularBrands(),
        ]);
    }

    public function brandProducts(Request $request, string $brand)
    {
        $brandLabel = $this->resolveBrandLabel($brand);

        $products = Product::query()
            ->with(['seller.sellerProfile', 'category', 'subcategory'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleInMarketplace()
            ->where(function ($query) use ($brandLabel, $brand) {
                $query->whereRaw('LOWER(COALESCE(brand, "")) = ?', [Str::lower($brandLabel)])
                    ->orWhereRaw('LOWER(COALESCE(brand, "")) = ?', [str_replace('-', ' ', Str::lower($brand))]);
            })
            ->orderByDesc('id')
            ->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'ok' => true,
            'brand' => [
                'slug' => $brand,
                'label' => $brandLabel,
                'logo_url' => $this->brandLogoUrl($brandLabel),
            ],
            'products' => $products->getCollection()->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'final_price' => (float) $product->final_price,
                'stock' => (int) $product->stock,
                'image_url' => MediaUrl::public($product->image),
                'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
                'reviews_count' => (int) ($product->reviews_count ?? 0),
                'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name, 'slug' => $product->category->slug] : null,
                'subcategory' => $product->subcategory ? ['id' => $product->subcategory->id, 'name' => $product->subcategory->name, 'slug' => $product->subcategory->slug] : null,
            ])->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    private function serializeCategoryNode(Category $category): array
    {
        $category->loadMissing('childrenRecursive');

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'is_leaf' => $category->childrenRecursive->isEmpty(),
            'children' => $category->childrenRecursive->map(fn (Category $child) => $this->serializeCategoryNode($child))->values(),
        ];
    }

    private function buildFilterDefinitions($query, Category $category): Collection
    {
        $categoryIds = $this->collectCategoryIds($category);

        $definitions = CategoryAttribute::query()
            ->with('options')
            ->whereIn('category_id', $categoryIds)
            ->where('is_filterable', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('slug')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'name' => $first->name,
                    'slug' => $first->slug,
                    'type' => $first->type,
                    'is_variant' => (bool) $first->is_variant,
                    'options' => $group->flatMap(fn ($attribute) => $attribute->options)
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

        return $definitions->map(function (array $definition) use ($productIds) {
            if ($productIds->isEmpty()) {
                $definition['text_values'] = [];
                $definition['min_value'] = null;
                $definition['max_value'] = null;
                return $definition;
            }

            if ($definition['type'] === 'text') {
                $definition['text_values'] = ProductAttribute::query()
                    ->join('category_attributes', 'category_attributes.id', '=', 'product_attributes.category_attribute_id')
                    ->whereIn('product_attributes.product_id', $productIds)
                    ->where('category_attributes.slug', $definition['slug'])
                    ->whereNotNull('product_attributes.value_text')
                    ->where('product_attributes.value_text', '!=', '')
                    ->distinct()
                    ->orderBy('product_attributes.value_text')
                    ->limit(24)
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

                $definition['min_value'] = $range?->min_value !== null ? (float) $range->min_value : null;
                $definition['max_value'] = $range?->max_value !== null ? (float) $range->max_value : null;
            }

            return $definition;
        });
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

    private function categoryPath(Category $category): array
    {
        $parts = [];
        $current = $category->loadMissing('parent');

        while ($current) {
            $parts[] = [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ];
            $current = $current->parent;
        }

        return array_reverse($parts);
    }

    private function popularBrands(): array
    {
        $brands = Product::query()
            ->visibleInMarketplace()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->get(['brand'])
            ->groupBy(fn (Product $product) => Str::slug($product->brand))
            ->map(function (Collection $group, string $slug) {
                $label = trim((string) $group->first()->brand);

                return [
                    'slug' => $slug,
                    'label' => $label,
                    'count' => $group->count(),
                    'logo_url' => $this->brandLogoUrl($label),
                ];
            })
            ->sortByDesc('count')
            ->take(24)
            ->values()
            ->all();

        if (!empty($brands)) {
            return $brands;
        }

        return [
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
        ];
    }

    private function resolveBrandLabel(string $brand): string
    {
        $match = collect($this->popularBrands())->firstWhere('slug', $brand);

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
}
