<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'pay_id',
        'payment_status',
        'payment_details',
        'paid_at',
        'paid_email_sent_at',
        'refund_status',
        'refunded_at',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'district',
        'locality',
        'street',
        'postal_code',
        'customer_note',
        'subtotal',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'paid_at' => 'datetime',
        'paid_email_sent_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}