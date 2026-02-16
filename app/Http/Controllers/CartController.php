<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['qty']);
        }

        return view('shop.cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    public function add(Product $product)
    {
        // doar produse active
        if ((int)$product->status !== 1) {
            return redirect()->route('home')->with('error', 'Produsul nu este disponibil.');
        }

        $cart = session()->get('cart', []);

        $id = (string)$product->id;

        if (isset($cart[$id])) {
            // crește cantitatea, dar nu mai mult decât stocul
            $newQty = $cart[$id]['qty'] + 1;
            $cart[$id]['qty'] = min($newQty, (int)$product->stock);
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->final_price,
                'qty' => 1,
                'stock' => (int)$product->stock,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produs adăugat în coș.');

    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);
        $id = (string)$product->id;

        if (!isset($cart[$id])) {
            return redirect()->route('cart.index');
        }

        $qty = (int)$request->input('qty');
        $qty = min($qty, (int)$product->stock);

        $cart[$id]['qty'] = $qty;
        $cart[$id]['stock'] = (int)$product->stock;
        $cart[$id]['price'] = (float)$product->final_price;

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Coș actualizat.');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        $id = (string)$product->id;

        unset($cart[$id]);

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produs șters din coș.');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Coș golit.');
    }
    public function buyNow(Product $product)
    {
        // doar produse active
        if ((int)$product->status !== 1) {
            return redirect()->route('home')->with('error', 'Produsul nu este disponibil.');
        }

        $cart = session()->get('cart', []);
        $id = (string)$product->id;

        if (isset($cart[$id])) {
            $newQty = (int)$cart[$id]['qty'] + 1;
            $cart[$id]['qty'] = min($newQty, (int)$product->stock);
        } else {
            $cart[$id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => (float)$product->final_price,
                'qty'   => 1,
                'stock' => (int)$product->stock,
                // opțional: dacă vrei imagine în coș, o poți adăuga fără să strici nimic
                'image' => $product->image,
            ];
        }

        // sincronizăm mereu preț+stoc, în caz că s-au schimbat
        $cart[$id]['stock'] = (int)$product->stock;
        $cart[$id]['price'] = (float)$product->final_price;

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produs adăugat. Poți finaliza comanda.');
    }

}
