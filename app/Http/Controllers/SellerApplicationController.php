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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:seller_applications,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'shop_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'seller_type' => ['required', 'in:individual,freelancer,company'],
            'idnp' => ['nullable', 'string', 'max:50'],
            'company_idno' => ['nullable', 'string', 'max:50'],
            'pickup_address' => ['nullable', 'string', 'max:500'],
            'delivery_type' => ['required', 'in:courier,personal'],
            'courier_company' => ['nullable', 'string', 'max:255'],
            'courier_contract_details' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'payment_provider' => ['nullable', 'in:maib,paynet,none'],
            'has_online_payments_enabled' => ['nullable', 'boolean'],
            'merchant_id' => ['nullable', 'string', 'max:255'],
            'terminal_id' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:4000'],
            'secret_key' => ['nullable', 'string', 'max:4000'],
            'payment_contact_email' => ['nullable', 'email', 'max:255'],
            'settlement_iban' => ['nullable', 'string', 'max:64'],
            'payment_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $data['has_online_payments_enabled'] = $request->boolean('has_online_payments_enabled');

        if (in_array($data['seller_type'], ['individual', 'freelancer'], true)) {
            $data['company_idno'] = null;
        }

        if ($data['seller_type'] === 'company') {
            $data['idnp'] = null;
        }

        if ($data['delivery_type'] === 'personal') {
            $data['courier_company'] = null;
            $data['courier_contract_details'] = null;
        }

        if (!$data['has_online_payments_enabled']) {
            $data['payment_provider'] = 'none';
            $data['merchant_id'] = null;
            $data['terminal_id'] = null;
            $data['api_key'] = null;
            $data['secret_key'] = null;
            $data['payment_contact_email'] = null;
            $data['settlement_iban'] = null;
        }

        $data['status'] = 'pending';

        SellerApplication::create($data);

        return redirect()
            ->route('seller.application.create')
            ->with('success', 'Cererea a fost trimisa cu succes. Adminul va verifica datele magazinului si configurarea platilor online inainte de aprobare.');
    }
}
