<?php

namespace App\Http\Controllers;

use App\Models\SellerApplication;
use Illuminate\Http\Request;

class SellerApplicationController extends Controller
{
    public function create()
    {
        return view('seller-application.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                     => ['required', 'string', 'max:255'],
            'email'                    => ['required', 'email', 'max:255', 'unique:seller_applications,email'],
            'phone'                    => ['nullable', 'string', 'max:50'],
            'shop_name'                => ['required', 'string', 'max:255'],
            'legal_name'               => ['nullable', 'string', 'max:255'],
            'seller_type'              => ['required', 'in:individual,company'],
            'idnp'                     => ['nullable', 'string', 'max:50'],
            'company_idno'             => ['nullable', 'string', 'max:50'],
            'pickup_address'           => ['nullable', 'string', 'max:500'],
            'delivery_type'            => ['required', 'in:courier,personal'],
            'courier_company'          => ['nullable', 'string', 'max:255'],
            'courier_contract_details' => ['nullable', 'string', 'max:2000'],
            'notes'                    => ['nullable', 'string', 'max:3000'],
        ]);

        if ($data['seller_type'] === 'individual') {
            $data['company_idno'] = null;
        }

        if ($data['seller_type'] === 'company') {
            $data['idnp'] = null;
        }

        if ($data['delivery_type'] === 'personal') {
            $data['courier_company'] = null;
            $data['courier_contract_details'] = null;
        }

        $data['status'] = 'pending';

        SellerApplication::create($data);

        return redirect()
            ->route('seller.application.create')
            ->with('success', 'Cererea a fost trimisă cu succes. Te vom contacta după verificare.');
    }
}