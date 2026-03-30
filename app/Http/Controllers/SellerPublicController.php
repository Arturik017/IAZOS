<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
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

        $canReviewSeller = false;
        $mySellerReview = null;

        if (auth()->check()) {
            $canReviewSeller = OrderItem::query()
                ->where('seller_id', $user->id)
                ->whereHas('order', function ($query) {
                    $query->where('user_id', auth()->id())
                        ->where('payment_status', 'paid');
                })
                ->exists();

            $mySellerReview = $sellerReviews->firstWhere('user_id', auth()->id());
        }

        return view('shop.seller', compact(
            'user',
            'sellerProfile',
            'products',
            'stats',
            'q',
            'sort',
            'sellerReviews',
            'canReviewSeller',
            'mySellerReview'
        ));
    }
}