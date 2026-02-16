<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'pay_id',
        'payment_status',

        // ✅ adăugate (altfel nu se salvează)
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

    // ✅ Relația cu utilizatorul (pentru $order->user->email)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}
