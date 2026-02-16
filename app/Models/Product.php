<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


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

        ];




}

