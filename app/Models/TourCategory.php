<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

public function parent()
{
    return $this->belongsTo(TourCategory::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(TourCategory::class, 'parent_id')->orderBy('name');
}

public function packages()
{
    return $this->hasMany(TourPackage::class, 'category_id');
}

public function packagesAsSubcategory()
{
    return $this->hasMany(TourPackage::class, 'subcategory_id');
}

}
