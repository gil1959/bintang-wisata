<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipPackage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'label',
        'category_id',
        'thumbnail_path',
        'is_active',
        'features',
        'long_description',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'rating_value',
        'rating_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'rating_value' => 'float',
        'rating_count' => 'int',
    ];

    public function category()
    {
        return $this->belongsTo(ShipCategory::class, 'category_id');
    }

    public function tiers()
    {
        return $this->hasMany(ShipPackageTier::class, 'ship_package_id')
            ->orderBy('type')
            ->orderBy('sort_order');
    }
    public function reviews()
{
    return $this->morphMany(\App\Models\Review::class, 'reviewable');
}

}
