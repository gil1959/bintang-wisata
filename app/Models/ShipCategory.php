<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function packages()
    {
        return $this->hasMany(ShipPackage::class, 'category_id');
    }
}
