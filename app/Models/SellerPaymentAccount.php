<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPaymentAccount extends Model
{
    protected $fillable = [
        'seller_profile_id',
        'provider',
        'merchant_id',
        'terminal_id',
        'api_key',
        'secret_key',
        'payment_contact_email',
        'settlement_iban',
        'is_active',
        'status',
        'notes',
        'verified_at',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'secret_key' => 'encrypted',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function isReadyForCheckout(): bool
    {
        return $this->status === 'active'
            && $this->is_active
            && filled($this->provider)
            && filled($this->merchant_id)
            && filled($this->api_key)
            && filled($this->secret_key);
    }
}
