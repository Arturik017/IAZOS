<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $profile = $user->sellerProfile;

        if (!$profile) {
            abort(404, 'Profil seller inexistent.');
        }

        return view('seller.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $profile = $user->sellerProfile;

        if (!$profile) {
            abort(404, 'Profil seller inexistent.');
        }

        $data = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'pickup_address' => ['nullable', 'string', 'max:500'],
            'seller_type' => ['required', 'in:individual,company'],
            'idnp' => ['nullable', 'string', 'max:50'],
            'company_idno' => ['nullable', 'string', 'max:50'],
            'delivery_type' => ['nullable', 'in:courier,personal'],
            'courier_company' => ['nullable', 'string', 'max:255'],
            'courier_contract_details' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if ($data['seller_type'] === 'individual') {
            $data['company_idno'] = null;
        }

        if ($data['seller_type'] === 'company') {
            $data['idnp'] = null;
        }

        if (($data['delivery_type'] ?? null) === 'personal') {
            $data['courier_company'] = null;
            $data['courier_contract_details'] = null;
        }

        $profile->update($data);

        return redirect()->route('seller.profile.edit')->with('success', 'Profilul seller a fost actualizat.');
    }
}