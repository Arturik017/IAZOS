<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function apply()
    {
        $user = auth()->user();

        // deja este seller
        if ($user->seller_status === 'approved') {
            return back()->with('error', 'Ești deja seller.');
        }

        // deja a aplicat
        if ($user->seller_status === 'pending') {
            return back()->with('error', 'Cererea ta este în procesare.');
        }

        // setăm status pending
        $user->seller_status = 'pending';
        $user->role = 'seller';
        $user->save();

        // creăm profil seller
        SellerProfile::create([
            'user_id' => $user->id,
            'shop_name' => $user->name . ' Shop',
            'phone' => null,
        ]);

        return back()->with('success', 'Cererea a fost trimisă. Așteaptă aprobare.');
    }
}
