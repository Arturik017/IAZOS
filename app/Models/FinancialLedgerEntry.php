<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLedgerEntry extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'order_item_id',
        'payout_request_id',
        'payout_batch_id',
        'type',
        'bucket',
        'amount',
        'currency',
        'description',
        'meta',
        'happened_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
        'happened_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(PayoutRequest::class);
    }

    public function payoutBatch(): BelongsTo
    {
        return $this->belongsTo(PayoutBatch::class);
    }
}
