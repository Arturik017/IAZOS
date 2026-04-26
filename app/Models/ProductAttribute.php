<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'category_attribute_id',
        'option_id',
        'value_text',
        'value_number',
        'value_boolean',
        'unit',
    ];

    protected $casts = [
        'value_number' => 'decimal:2',
        'value_boolean' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(CategoryAttributeOption::class, 'option_id');
    }
}