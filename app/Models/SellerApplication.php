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
        'status',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
}