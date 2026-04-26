<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutBatchItem extends Model
{
    protected $fillable = [
        'payout_batch_id',
        'payout_request_id',
        'seller_id',
        'amount',
        'currency',
        'beneficiary_name',
        'iban',
        'bank_name',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PayoutBatch::class, 'payout_batch_id');
    }

    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(PayoutRequest::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
