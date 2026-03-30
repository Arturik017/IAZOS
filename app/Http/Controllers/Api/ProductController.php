<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $subcategoryId = $request->query('subcategory_id');
        $sort = (string) $request->query('sort', 'new');

        $products = Product::query()
            ->with([
                'seller.sellerProfile',
                'category',
                'subcategory',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            })
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
            });

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
        if ((int) $product->status !== 1) {
            abort(404);
        }

        if (!is_null($product->seller_id) && !(bool) $product->is_approved) {
            abort(404);
        }

        $product->load([
            'seller.sellerProfile',
            'category',
            'subcategory',
            'reviews.user',
            'reviews.images',
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
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                'is_promo' => (bool) $product->is_promo,
                'average_rating' => $product->averageRating(),
                'reviews_count' => $product->reviewsCount(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
                'subcategory' => $product->subcategory ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->name,
                ] : null,
                'seller' => $product->seller ? [
                    'id' => $product->seller->id,
                    'name' => $product->seller->name,
                    'shop_name' => $product->seller->sellerProfile->shop_name ?? null,
                ] : null,
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
                                'url' => asset('storage/' . $image->image_path),
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
            'final_price' => (float) $product->final_price,
            'stock' => (int) $product->stock,
            'image_url' => $product->image ? asset('storage/' . $product->image) : null,
            'is_promo' => (bool) $product->is_promo,
            'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'reviews_count' => (int) ($product->reviews_count ?? 0),
            'seller' => $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->name,
                'shop_name' => $product->seller->sellerProfile->shop_name ?? null,
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
}