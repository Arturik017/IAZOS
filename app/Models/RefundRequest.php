<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'seller_id',
        'target_status',
        'status',
        'client_reason',
        'client_note',
        'seller_response',
        'seller_recommended_status',
        'seller_responded_at',
        'admin_decision_note',
        'resolved_financial_status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'seller_responded_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
