<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'name_en',
        'created_by_partner_id'
    ];


    public function packages()
    {
        return $this->hasMany(ShipPackage::class, 'category_id');
    }
}
