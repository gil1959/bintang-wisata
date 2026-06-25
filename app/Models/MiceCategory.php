<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'name_en'

    ];

    public function packages()
    {
        return $this->hasMany(MicePackage::class, 'category_id');
    }
}
