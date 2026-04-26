<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'type',
        'is_required',
        'is_filterable',
        'is_variant',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_variant' => 'boolean',
        'allowed_units' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->hasMany(CategoryAttributeOption::class)->orderBy('sort_order')->orderBy('label');
    }
}