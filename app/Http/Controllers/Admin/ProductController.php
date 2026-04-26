<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $owner = (string) $request->query('owner', '');
        $moderation = (string) $request->query('moderation', '');
        $status = (string) $request->query('status', '');

        $products = Product::query()
            ->with(['seller.sellerProfile', 'category'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhereHas('seller', function ($sellerQuery) use ($q) {
                            $sellerQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%")
                                ->orWhereHas('sellerProfile', function ($profileQuery) use ($q) {
                                    $profileQuery->where('shop_name', 'like', "%{$q}%")
                                        ->orWhere('legal_name', 'like', "%{$q}%");
                                });
                        });
                });
            })
            ->when($owner === 'admin', function ($query) {
                $query->whereNull('seller_id');
            })
            ->when($owner === 'seller', function ($query) {
                $query->whereNotNull('seller_id');
            })
            ->when($moderation === 'pending', function ($query) {
                $query->whereNotNull('seller_id')->where('is_approved', false);
            })
            ->when($moderation === 'approved', function ($query) {
                $query->where(function ($qq) {
                    $qq->whereNull('seller_id')
                        ->orWhere('is_approved', true);
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('status', 1);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('status', 0);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Product::count(),
            'pending' => Product::whereNotNull('seller_id')->where('is_approved', false)->count(),
            'approved_seller_products' => Product::whereNotNull('seller_id')->where('is_approved', true)->count(),
            'admin_products' => Product::whereNull('seller_id')->count(),
        ];

        return view('admin.products.index', compact(
            'products',
            'q',
            'owner',
            'moderation',
            'status',
            'stats'
        ));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'final_price'    => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'status'         => ['nullable'],
            'image'          => ['nullable', 'image', 'max:4096'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'is_promo'       => ['nullable'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = ImageStorage::storeWebp($request->file('image'), 'products', 'public', 82, 'image');
        }

        Product::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'final_price'       => $request->final_price,
            'stock'             => $request->stock,
            'status'            => $request->has('status') ? 1 : 0,
            'image'             => $imagePath,
            'category_id'       => $request->category_id,
            'subcategory_id'    => $request->subcategory_id,
            'is_promo'          => $request->has('is_promo') ? 1 : 0,
            'seller_id'         => null,
            'shipping_included' => true,
            'is_approved'       => true,
            'proof_path'        => null,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produs adăugat');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'final_price'    => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'status'         => ['nullable'],
            'image'          => ['nullable', 'image', 'max:4096'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'is_promo'       => ['nullable'],
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->image = ImageStorage::storeWebp($request->file('image'), 'products', 'public', 82, 'image');
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->final_price = $request->final_price;
        $product->stock = $request->stock;
        $product->status = $request->has('status') ? 1 : 0;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->is_promo = $request->has('is_promo') ? 1 : 0;

        if (is_null($product->seller_id)) {
            $product->shipping_included = true;
            $product->is_approved = true;
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Produs actualizat');
    }

    public function approve($id)
    {
        $product = Product::findOrFail($id);

        $product->is_approved = true;
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Produs aprobat.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (!empty($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produs șters');
    }
}
