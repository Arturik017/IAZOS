<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerCommissionPeriod extends Model
{
    protected $fillable = [
        'seller_id',
        'period_start',
        'period_end',
        'deadline_at',
        'gross_sales_amount',
        'commission_percent',
        'commission_amount',
        'status',
        'seller_note',
        'admin_note',
        'submitted_at',
        'reviewed_at',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'deadline_at' => 'date',
        'gross_sales_amount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
