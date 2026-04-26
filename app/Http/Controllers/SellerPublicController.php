<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerStory;
use App\Models\User;
use Illuminate\Http\Request;

class SellerPublicController extends Controller
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
                        ->orWhereHas('sellerProfile', function ($sellerProfileQuery) use ($q) {
                            $sellerProfileQuery->where('shop_name', 'like', "%{$q}%")
                                ->orWhere('legal_name', 'like', "%{$q}%")
                                ->orWhere('notes', 'like', "%{$q}%")
                                ->orWhere('pickup_address', 'like', "%{$q}%")
                                ->orWhere('seller_type', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('public_products_count')
            ->orderBy('name')
            ->paginate(18)
            ->withQueryString();

        $activeStoryIdsBySeller = collect();
        if (User::supportsSellerStories()) {
            $sellerIds = $sellers->getCollection()->pluck('id');
            $sellers->getCollection()->loadCount([
                'stories as active_stories_count' => fn ($query) => $query->active(),
            ]);

            $activeStoryIdsBySeller = SellerStory::query()
                ->whereIn('seller_id', $sellerIds)
                ->active()
                ->orderByDesc('created_at')
                ->get(['id', 'seller_id'])
                ->groupBy('seller_id')
                ->map(fn ($items) => $items->pluck('id')->values()->all());
        }

        if (User::supportsSellerFollowers()) {
            $sellers->getCollection()->loadCount('followers');

            if (auth()->check()) {
                $authUser = auth()->user();

                $sellers->getCollection()->transform(function (User $seller) use ($authUser) {
                    $seller->is_following = $authUser->id !== $seller->id
                        && $authUser->isFollowingSeller((int) $seller->id);

                    return $seller;
                });
            }
        }

        $sellers->getCollection()->transform(function (User $seller) use ($activeStoryIdsBySeller) {
            $seller->active_story_ids = $activeStoryIdsBySeller->get($seller->id, []);
            return $seller;
        });

        return view('shop.sellers', compact('sellers', 'q'));
    }

    public function show(Request $request, User $user)
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

        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            abort(404);
        }

        $q = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'new');

        $productsQuery = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('seller_id', $user->id)
            ->where('status', 1)
            ->where('is_approved', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            });

        if ($sort === 'price_asc') {
            $productsQuery->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $productsQuery->orderBy('final_price', 'desc');
        } else {
            $productsQuery->orderByDesc('id');
        }

        $products = $productsQuery
            ->paginate(24)
            ->withQueryString();

        $stats = [
            'products_count' => Product::query()
                ->where('seller_id', $user->id)
                ->where('status', 1)
                ->where('is_approved', true)
                ->count(),

            'in_stock_count' => Product::query()
                ->where('seller_id', $user->id)
                ->where('status', 1)
                ->where('is_approved', true)
                ->where('stock', '>', 0)
                ->count(),

            'promo_count' => Product::query()
                ->where('seller_id', $user->id)
                ->where('status', 1)
                ->where('is_approved', true)
                ->where('is_promo', 1)
                ->count(),
        ];

        $sellerReviews = $user->sellerReviewsReceived()
            ->with('user')
            ->latest()
            ->get();
        $sellerStories = User::supportsSellerStories()
            ? (User::supportsSellerStoryLikes()
                ? SellerStory::query()->withCount('likes')
                : SellerStory::query())
                ->where('seller_id', $user->id)
                ->active()
                ->latest()
                ->get()
            : collect();
        $storyGroups = User::supportsSellerStories()
            ? (User::supportsSellerStoryLikes()
                ? SellerStory::query()->with(['seller.sellerProfile'])->withCount('likes')
                : SellerStory::query()->with(['seller.sellerProfile']))
                ->active()
                ->latest()
                ->get()
                ->groupBy('seller_id')
                ->map(function ($items) {
                    $seller = $items->first()->seller;

                    return [
                        'seller_id' => $seller->id,
                        'seller_name' => $seller->sellerProfile->shop_name ?? $seller->name,
                        'seller_avatar' => \App\Support\MediaUrl::public($seller->sellerProfile->avatar_path ?? null),
                        'seller_url' => route('seller.public.show', $seller),
                        'stories' => $items->map(function (SellerStory $story) {
                            $viewer = auth()->user();
                            return [
                                'id' => $story->id,
                                'media_type' => $story->media_type,
                                'media_url' => \App\Support\MediaUrl::public($story->media_path),
                                'thumbnail_url' => \App\Support\MediaUrl::public($story->thumbnail_path ?: $story->media_path),
                                'caption' => $story->caption,
                                'expires_at' => $story->expires_at?->format('d.m H:i'),
                                'likes_count' => (int) ($story->likes_count ?? 0),
                                'is_liked' => $viewer && User::supportsSellerStoryLikes()
                                    ? $story->likes()->where('user_id', $viewer->id)->exists()
                                    : false,
                            ];
                        })->values()->all(),
                    ];
                })
                ->sortByDesc(fn (array $group) => (int) ($group['seller_id'] === $user->id))
                ->values()
            : collect();

        $canReviewSeller = false;
        $mySellerReview = null;
        $isFollowingSeller = false;

        if (auth()->check()) {
            $canReviewSeller = OrderItem::query()
                ->where('seller_id', $user->id)
                ->whereHas('order', function ($query) {
                    $query->where('user_id', auth()->id())
                        ->where('payment_status', 'paid');
                })
                ->exists();

            $mySellerReview = $sellerReviews->firstWhere('user_id', auth()->id());
            $isFollowingSeller = auth()->id() !== $user->id
                && User::supportsSellerFollowers()
                && auth()->user()->isFollowingSeller((int) $user->id);
        }

        return view('shop.seller', compact(
            'user',
            'sellerProfile',
            'products',
            'stats',
            'q',
            'sort',
            'sellerReviews',
            'sellerStories',
            'storyGroups',
            'canReviewSeller',
            'mySellerReview',
            'isFollowingSeller'
        ));
    }
}
