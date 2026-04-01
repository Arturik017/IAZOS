<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'cart' => session()->get('cart', [])
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $request->qty;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->final_price ?? $product->price,
                'qty' => $request->qty,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Added to cart',
            'cart' => $cart
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['qty'] = $request->qty;
            session()->put('cart', $cart);
        }

        return response()->json([
            'cart' => $cart
        ]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        unset($cart[$id]);

        session()->put('cart', $cart);

        return response()->json([
            'cart' => $cart
        ]);
    }

    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'message' => 'Cart cleared'
        ]);
    }
}