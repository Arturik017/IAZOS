<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'final_price',
        'stock',
        'status',
        'image',
        'category_id',
        'subcategory_id',
        'is_promo',
        'seller_id',
        'shipping_included',
        'is_approved',
        'proof_path',
        'ai_banner_prompt',
        'ai_banner_path',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'is_promo' => 'boolean',
        'shipping_included' => 'boolean',
        'is_approved' => 'boolean',
        'stock' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'seller_id' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function averageRating(): float
    {
        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }

    public function reviewsCount(): int
    {
        return (int) $this->reviews()->count();
    }
}