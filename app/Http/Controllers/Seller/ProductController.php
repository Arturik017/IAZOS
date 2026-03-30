<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\AiBannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403);
        }

        $products = Product::where('seller_id', $user->id)
            ->orderByDesc('id')
            ->get();

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request, AiBannerService $aiBannerService)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'ai_banner_prompt' => ['nullable', 'string', 'max:2000'],
            'ai_generated_temp_path' => ['nullable', 'string', 'max:500'],
        ]);

        $imagePath = null;
        $bannerPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        if ($request->filled('ai_generated_temp_path')) {
            $bannerPath = $aiBannerService->moveTempBannerToFinal($request->ai_generated_temp_path);
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'final_price' => $request->final_price,
            'stock' => $request->stock,
            'status' => 1,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'seller_id' => auth()->id(),
            'shipping_included' => true,
            'is_approved' => false,
            'ai_banner_prompt' => $request->ai_banner_prompt,
            'ai_banner_path' => $bannerPath,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produs adăugat!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id, AiBannerService $aiBannerService)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'ai_banner_prompt' => ['nullable', 'string', 'max:2000'],
            'ai_generated_temp_path' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->image = $request->file('image')->store('products', 'public');
        }

        if ($request->filled('ai_generated_temp_path')) {
            if ($product->ai_banner_path) {
                $aiBannerService->deleteIfExists($product->ai_banner_path);
            }

            $product->ai_banner_path = $aiBannerService->moveTempBannerToFinal($request->ai_generated_temp_path);
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->final_price = $request->final_price;
        $product->stock = $request->stock;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->ai_banner_prompt = $request->ai_banner_prompt;
        $product->is_approved = false;
        $product->save();

        return redirect()->route('seller.products.index')->with('success', 'Produs actualizat!');
    }

    public function destroy($id, AiBannerService $aiBannerService)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        if ($product->ai_banner_path) {
            $aiBannerService->deleteIfExists($product->ai_banner_path);
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produs șters!');
    }

    public function generateBannerPreview(Request $request, AiBannerService $aiBannerService)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
            'ai_banner_prompt' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $tmpPath = $aiBannerService->generateFromProductImage(
                $request->file('image'),
                $request->string('ai_banner_prompt')->toString()
            );

            return response()->json([
                'ok' => true,
                'temp_path' => $tmpPath,
                'url' => asset('storage/' . $tmpPath),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function deleteBannerPreview(Request $request, AiBannerService $aiBannerService)
    {
        $request->validate([
            'temp_path' => ['required', 'string', 'max:500'],
        ]);

        $tempPath = $request->string('temp_path')->toString();

        if (str_starts_with($tempPath, 'products/banners/tmp/')) {
            $aiBannerService->deleteIfExists($tempPath);
        }

        return response()->json([
            'ok' => true,
        ]);
    }
}