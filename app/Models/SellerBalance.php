<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerBalance extends Model
{
    protected $fillable = [
        'seller_id',
        'pending_amount',
        'available_amount',
        'paid_amount',
    ];

    protected $casts = [
        'pending_amount' => 'decimal:2',
        'available_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
