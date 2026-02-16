<?php

namespace App\Http\Controllers\Admin;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.products.dashboard');
    }

    public function edit(Product $product)
{
    return view('admin.products.edit', compact('product'));
}

public function update(Request $request, Product $product)
{
    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'final_price' => $request->final_price,
        'stock' => $request->stock,
        'status' => $request->status ? 1 : 0,
    ]);

    return redirect()->route('admin.products.index');
}

public function destroy(Product $product)
{
    $product->delete();
    return redirect()->route('admin.products.index');
}

}
