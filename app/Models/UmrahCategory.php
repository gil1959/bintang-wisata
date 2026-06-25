<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmrahCategory extends Model
{
    protected $fillable = ['name', 'name_en', 'slug'];

    public function packages()
    {
        return $this->hasMany(UmrahPackage::class, 'category_id');
    }
}
