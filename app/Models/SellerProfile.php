<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'legal_name',
        'phone',
        'pickup_address',
        'seller_type',
        'idnp',
        'company_idno',
        'delivery_type',
        'courier_company',
        'courier_contract_details',
        'notes',
        'commission_percent',
        'application_status',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'commission_percent' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}