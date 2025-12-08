<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function packages()
    {
        return $this->hasMany(TourPackage::class, 'category_id');
    }
}
