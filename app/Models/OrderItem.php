<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'seller_id',
        'seller_status',
        'seller_status_updated_at',
        'product_name',
        'variant_label',
        'price',
        'qty',
        'gross_amount',
        'platform_commission_percent',
        'platform_commission_amount',
        'seller_net_amount',
        'financial_status',
        'financial_status_updated_at',
        'admin_release_status',
        'delivered_reported_at',
        'admin_released_at',
        'admin_released_by',
        'admin_release_note',
        'refunded_at',
        'refunded_by',
        'refund_reason',
    ];

    protected $casts = [
        'seller_status_updated_at' => 'datetime',
        'financial_status_updated_at' => 'datetime',
        'delivered_reported_at' => 'datetime',
        'admin_released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gross_amount' => 'decimal:2',
        'platform_commission_percent' => 'decimal:2',
        'platform_commission_amount' => 'decimal:2',
        'seller_net_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function adminReleasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_released_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(FinancialLedgerEntry::class);
    }

    public function refundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class, 'order_item_id');
    }
}
