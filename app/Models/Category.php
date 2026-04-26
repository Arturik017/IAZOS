<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'is_active',
        'sort_order',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function isLeaf(): bool
    {
        return !$this->children()->exists();
    }
}