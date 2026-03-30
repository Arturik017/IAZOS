<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $banners = Banner::orderByDesc('id')->get()->map(function ($b) {
            return [
                'image' => $b->image ? asset('storage/' . $b->image) : null,
                'title' => $b->title ?? null,
                'subtitle' => $b->subtitle ?? null,
                'kicker' => $b->kicker ?? null,
            ];
        })->values();

        $promoProducts = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where('is_promo', 1)
            ->where(function ($q) {
                $q->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            })
            ->orderByDesc('id')
            ->get();

        return view('shop.home', compact('categories', 'banners', 'promoProducts'));
    }

    public function show(Product $product)
    {
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
        ]);

        $query = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where('id', '!=', $product->id)
            ->where(function ($q) {
                $q->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            });

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
            'similarProducts',
            'reviews',
            'questions',
            'canReview',
            'myReview'
        ));
    }

    public function category(Request $request, Category $category)
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $q = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where('category_id', $category->id)
            ->where(function ($q) {
                $q->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            });

        if ($request->boolean('promo')) {
            $q->where('is_promo', 1);
        }

        if ($request->boolean('in_stock')) {
            $q->where('stock', '>', 0);
        }

        if ($request->filled('min_price')) {
            $q->where('final_price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $q->where('final_price', '<=', (float) $request->max_price);
        }

        $sort = $request->get('sort', 'new');

        if ($sort === 'price_asc') {
            $q->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $q->orderBy('final_price', 'desc');
        } else {
            $q->orderByDesc('id');
        }

        $products = $q->paginate(24)->withQueryString();

        return view('shop.category', compact('categories', 'category', 'products'));
    }

    public function subcategory(Request $request, Category $category)
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $q = Product::query()
            ->with(['seller.sellerProfile'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 1)
            ->where('subcategory_id', $category->id)
            ->where(function ($q) {
                $q->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            });

        if ($request->boolean('promo')) {
            $q->where('is_promo', 1);
        }

        if ($request->boolean('in_stock')) {
            $q->where('stock', '>', 0);
        }

        if ($request->filled('min_price')) {
            $q->where('final_price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $q->where('final_price', '<=', (float) $request->max_price);
        }

        $sort = $request->get('sort', 'new');

        if ($sort === 'price_asc') {
            $q->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $q->orderBy('final_price', 'desc');
        } else {
            $q->orderByDesc('id');
        }

        $products = $q->paginate(24)->withQueryString();

        return view('shop.category', compact('categories', 'category', 'products'));
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
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('seller_id')
                    ->orWhere('is_approved', true);
            })
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
}