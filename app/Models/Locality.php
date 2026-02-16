<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name', 'is_enabled'];

    public function localities()
    {
        return $this->hasMany(Locality::class);
    }
}
