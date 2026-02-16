<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['nullable'],
            'image'       => ['nullable', 'image', 'max:4096'],
            'category_id' => ['nullable','exists:categories,id'],
            'subcategory_id' => ['nullable','exists:categories,id'],
            'is_promo' => ['nullable'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'final_price' => $request->final_price,
            'stock'       => $request->stock,
            'status'      => $request->has('status') ? 1 : 0,
            'image'       => $imagePath,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'is_promo' => $request->has('is_promo') ? 1 : 0,
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
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['nullable'],
            'image'       => ['nullable', 'image', 'max:4096'],
            'category_id' => ['nullable','exists:categories,id'],
            'subcategory_id' => ['nullable','exists:categories,id'],
            'is_promo'    => ['nullable'],
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->final_price = $request->final_price;
        $product->stock = $request->stock;
        $product->status = $request->has('status') ? 1 : 0;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->is_promo = $request->has('is_promo') ? 1 : 0;

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Produs actualizat');
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
