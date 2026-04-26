<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttributeOption extends Model
{
    protected $fillable = [
        'category_attribute_id',
        'value',
        'label',
        'sort_order',
    ];

    public function attribute()
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }
}