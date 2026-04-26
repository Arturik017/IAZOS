<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'shop_name',
        'legal_name',
        'seller_type',
        'idnp',
        'company_idno',
        'pickup_address',
        'delivery_type',
        'courier_company',
        'courier_contract_details',
        'notes',
        'payment_provider',
        'has_online_payments_enabled',
        'merchant_id',
        'terminal_id',
        'api_key',
        'secret_key',
        'payment_contact_email',
        'settlement_iban',
        'payment_notes',
        'status',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'has_online_payments_enabled' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
}
