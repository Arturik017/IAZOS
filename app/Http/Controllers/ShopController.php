<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Illuminate\Support\Facades\Storage;


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

        $promoProducts = Product::where('status', 1)
            ->where('is_promo', 1)
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

        $query = Product::where('status', 1)
            ->where('id', '!=', $product->id);

        if (!empty($product->subcategory_id)) {
            $query->where('subcategory_id', $product->subcategory_id);
        } elseif (!empty($product->category_id)) {
            $query->where('category_id', $product->category_id);
        } else {
            $query->whereRaw('1=0');
        }

        $similarProducts = $query->orderByDesc('id')->limit(6)->get();

        return view('shop.product', compact('categories', 'product', 'similarProducts'));
    }

    public function category(Request $request, Category $category)
{
    $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

    $q = Product::query()
        ->where('status', 1)
        ->where('category_id', $category->id);

    // ✅ promo
    if ($request->boolean('promo')) {
        $q->where('is_promo', 1);
    }

    // ✅ în stoc
    if ($request->boolean('in_stock')) {
        $q->where('stock', '>', 0);
    }

    // ✅ preț
    if ($request->filled('min_price')) {
        $q->where('final_price', '>=', (float)$request->min_price);
    }
    if ($request->filled('max_price')) {
        $q->where('final_price', '<=', (float)$request->max_price);
    }

    // ✅ sort
    $sort = $request->get('sort', 'new');
    if ($sort === 'price_asc') {
        $q->orderBy('final_price', 'asc');
    } elseif ($sort === 'price_desc') {
        $q->orderBy('final_price', 'desc');
    } else {
        $q->orderByDesc('id'); // new
    }

    $products = $q->paginate(24)->withQueryString();


    return view('shop.category', compact('categories', 'category', 'products'));
}

    public function subcategory(Request $request, Category $category)
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $q = Product::query()
            ->where('status', 1)
            ->where('subcategory_id', $category->id);

        // ✅ promo
        if ($request->boolean('promo')) {
            $q->where('is_promo', 1);
        }

        // ✅ în stoc
        if ($request->boolean('in_stock')) {
            $q->where('stock', '>', 0);
        }

        // ✅ preț
        if ($request->filled('min_price')) {
            $q->where('final_price', '>=', (float)$request->min_price);
        }
        if ($request->filled('max_price')) {
            $q->where('final_price', '<=', (float)$request->max_price);
        }

        // ✅ sort
        $sort = $request->get('sort', 'new');
        if ($sort === 'price_asc') {
            $q->orderBy('final_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $q->orderBy('final_price', 'desc');
        } else {
            $q->orderByDesc('id'); // new
        }

        $products = $q->paginate(24)->withQueryString();


        // subcategoria folosește aceeași pagină `shop.category`
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
            ->where('status', 1)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        return view('shop.search', compact('categories', 'products', 'q'));
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
        foreach ($cart as $item) $subtotal += ($item['price'] * $item['qty']);

        // ✅ load locations.json
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
