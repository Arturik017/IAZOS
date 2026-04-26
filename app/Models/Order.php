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
        'payment_flow',
        'payment_provider',
        'payment_url',
        'payment_reference',
        'payment_link_generated_at',
        'payment_details',
        'paid_at',
        'paid_email_sent_at',
        'refund_status',
        'refunded_at',
        'user_id',
        'seller_id',
        'checkout_uuid',
        'checkout_group_id',
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
        'commission_percent',
        'commission_amount',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'payment_link_generated_at' => 'datetime',
        'paid_at' => 'datetime',
        'paid_email_sent_at' => 'datetime',
        'refunded_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(FinancialLedgerEntry::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }
}
