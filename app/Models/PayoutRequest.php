<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutRequest extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'currency',
        'status',
        'beneficiary_name',
        'iban',
        'bank_name',
        'seller_note',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function batchItems(): HasMany
    {
        return $this->hasMany(PayoutBatchItem::class);
    }
}
