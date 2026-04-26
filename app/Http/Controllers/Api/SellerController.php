<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $sellers = User::query()
            ->where('role', 'seller')
            ->where('seller_status', 'approved')
            ->whereHas('sellerProfile')
            ->with('sellerProfile')
            ->withAvg('sellerReviewsReceived', 'rating')
            ->withCount('sellerReviewsReceived')
            ->withCount([
                'products as public_products_count' => function ($query) {
                    $query->where('status', 1)
                        ->where('is_approved', true);
                },
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhereHas('sellerProfile', function ($profileQuery) use ($q) {
                            $profileQuery->where('shop_name', 'like', "%{$q}%")
                                ->orWhere('legal_name', 'like', "%{$q}%")
                                ->orWhere('notes', 'like', "%{$q}%")
                                ->orWhere('pickup_address', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('public_products_count')
            ->paginate(20);

        if (User::supportsSellerFollowers()) {
            $sellers->getCollection()->loadCount('followers');
        }

        return response()->json([
            'ok' => true,
            'sellers' => $sellers->getCollection()->map(fn ($seller) => $this->sellerCard($seller))->values(),
            'meta' => [
                'current_page' => $sellers->currentPage(),
                'last_page' => $sellers->lastPage(),
                'per_page' => $sellers->perPage(),
                'total' => $sellers->total(),
            ],
        ]);
    }

    public function show(User $user)
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        $user->load([
            'sellerProfile',
            'sellerReviewsReceived.user',
        ]);
        if (User::supportsSellerFollowers()) {
            $user->loadCount('followers');
        }

        $isFollowing = User::supportsSellerFollowers() && auth('sanctum')->check()
            ? auth('sanctum')->user()->isFollowingSeller((int) $user->id)
            : false;

        $products = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('seller_id', $user->id)
            ->where('status', 1)
            ->where('is_approved', true)
            ->orderByDesc('id')
            ->limit(24)
            ->get();

        return response()->json([
            'ok' => true,
            'seller' => [
                'id' => $user->id,
                'name' => $user->name,
                'shop_name' => $user->sellerProfile->shop_name ?? null,
                'avatar_url' => MediaUrl::public($user->sellerProfile->avatar_path ?? null),
                'legal_name' => $user->sellerProfile->legal_name ?? null,
                'phone' => $user->sellerProfile->phone ?? null,
                'pickup_address' => $user->sellerProfile->pickup_address ?? null,
                'seller_type' => $user->sellerProfile->seller_type ?? null,
                'delivery_type' => $user->sellerProfile->delivery_type ?? null,
                'notes' => $user->sellerProfile->notes ?? null,
                'followers_count' => (int) ($user->followers_count ?? 0),
                'is_following' => $isFollowing,
                'average_rating' => $user->averageSellerRating(),
                'reviews_count' => $user->sellerReviewsCount(),
                'reviews' => $user->sellerReviewsReceived->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => (int) $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at?->toDateTimeString(),
                        'user' => [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ],
                    ];
                })->values(),
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'final_price' => (float) $product->final_price,
                        'stock' => (int) $product->stock,
                        'image_url' => MediaUrl::public($product->image),
                        'is_promo' => (bool) $product->is_promo,
                        'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
                        'reviews_count' => (int) ($product->reviews_count ?? 0),
                    ];
                })->values(),
            ],
        ]);
    }

    private function sellerCard(User $seller): array
    {
        $isFollowing = User::supportsSellerFollowers() && auth('sanctum')->check()
            ? auth('sanctum')->user()->isFollowingSeller((int) $seller->id)
            : false;

        return [
            'id' => $seller->id,
            'name' => $seller->name,
            'shop_name' => $seller->sellerProfile->shop_name ?? null,
            'avatar_url' => MediaUrl::public($seller->sellerProfile->avatar_path ?? null),
            'legal_name' => $seller->sellerProfile->legal_name ?? null,
            'pickup_address' => $seller->sellerProfile->pickup_address ?? null,
            'seller_type' => $seller->sellerProfile->seller_type ?? null,
            'delivery_type' => $seller->sellerProfile->delivery_type ?? null,
            'followers_count' => (int) ($seller->followers_count ?? 0),
            'is_following' => $isFollowing,
            'average_rating' => round((float) ($seller->seller_reviews_received_avg_rating ?? 0), 1),
            'reviews_count' => (int) ($seller->seller_reviews_received_count ?? 0),
            'public_products_count' => (int) ($seller->public_products_count ?? 0),
        ];
    }
}
