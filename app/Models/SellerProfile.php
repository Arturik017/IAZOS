<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'avatar_path',
        'legal_name',
        'payout_beneficiary_name',
        'payout_iban',
        'payout_bank_name',
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

    public function paymentAccount(): HasOne
    {
        return $this->hasOne(SellerPaymentAccount::class);
    }
}
