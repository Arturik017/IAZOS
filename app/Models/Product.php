<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
        'base_price',
        'has_variants',
        'primary_image',
        'brand',
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderByDesc('is_primary');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class); 
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlist_items')->withTimestamps();
    }

    public function scopeVisibleInMarketplace(Builder $query): Builder
    {
        return $query
            ->where('status', 1)
            ->where(function (Builder $visibilityQuery) {
                $visibilityQuery
                    ->where(function (Builder $sellerQuery) {
                        $sellerQuery
                            ->whereNotNull('seller_id')
                            ->where('is_approved', true)
                            ->whereHas('seller', function (Builder $userQuery) {
                                $userQuery
                                    ->where('role', 'seller')
                                    ->where('seller_status', 'approved');
                            });
                    })
                    ->orWhere(function (Builder $legacyAdminQuery) {
                        // Keep legacy admin-owned products visible, but hide orphaned seller listings.
                        $legacyAdminQuery
                            ->whereNull('seller_id')
                            ->whereNull('primary_image')
                            ->whereNull('proof_path')
                            ->where(function (Builder $variantsQuery) {
                                $variantsQuery
                                    ->whereNull('has_variants')
                                    ->orWhere('has_variants', false);
                            });
                    });
            });
    }

    public function isVisibleInMarketplace(): bool
    {
        return static::query()
            ->visibleInMarketplace()
            ->whereKey($this->getKey())
            ->exists();
    }
}
