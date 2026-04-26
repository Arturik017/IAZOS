<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    protected $fillable = [
        'product_variant_id',
        'category_attribute_id',
        'option_id',
        'custom_value',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function attribute()
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }

    public function option()
    {
        return $this->belongsTo(CategoryAttributeOption::class, 'option_id');
    }
}