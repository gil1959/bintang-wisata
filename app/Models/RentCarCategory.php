<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentCarCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function packages()
    {
        return $this->hasMany(RentCarPackage::class, 'category_id');
    }
}
