<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;

class SellerFollowController extends Controller
{
    public function store(User $user): JsonResponse
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if (!User::supportsSellerFollowers()) {
            return response()->json([
                'ok' => false,
                'message' => 'Functia de urmarit selleri va fi activa dupa rularea migrarilor noi.',
            ], 503);
        }

        if (auth()->id() === $user->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Nu te poți urmări pe tine ca seller.',
            ], 422);
        }

        auth()->user()->followedSellers()->syncWithoutDetaching([$user->id]);

        return response()->json([
            'ok' => true,
            'message' => 'Seller urmărit cu succes.',
            'followers_count' => $user->followers()->count(),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if (!User::supportsSellerFollowers()) {
            return response()->json([
                'ok' => false,
                'message' => 'Functia de urmarit selleri va fi activa dupa rularea migrarilor noi.',
            ], 503);
        }

        auth()->user()->followedSellers()->detach($user->id);

        return response()->json([
            'ok' => true,
            'message' => 'Seller eliminat din urmărite.',
            'followers_count' => $user->followers()->count(),
        ]);
    }

    public function promos(): JsonResponse
    {
        if (!User::supportsSellerFollowers()) {
            return response()->json([
                'ok' => true,
                'products' => [],
            ]);
        }

        $sellerIds = auth()->user()->followedSellers()->pluck('users.id');

        $products = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where('is_approved', true)
            ->where('is_promo', true)
            ->whereIn('seller_id', $sellerIds)
            ->orderByDesc('id')
            ->limit(36)
            ->get();

        return response()->json([
            'ok' => true,
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'final_price' => (float) $product->final_price,
                    'stock' => (int) $product->stock,
                    'is_promo' => (bool) $product->is_promo,
                    'image_url' => MediaUrl::public($product->image),
                    'seller' => [
                        'id' => $product->seller?->id,
                        'name' => $product->seller?->name,
                        'shop_name' => $product->seller?->sellerProfile?->shop_name,
                    ],
                    'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
                    'reviews_count' => (int) ($product->reviews_count ?? 0),
                ];
            })->values(),
        ]);
    }
}
