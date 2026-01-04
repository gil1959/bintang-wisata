<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'label',
        'rating_value',
        'rating_count',
        'slug',
        'category_id',
        'destination',
        'duration_text',
        'long_description',
        'itinerary',
        'include_text',
        'exclude_text',
        'thumbnail_path',
        'is_active',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(MiceCategory::class, 'category_id');
    }

    public function tiers()
    {
        return $this->hasMany(MicePackageTier::class, 'mice_package_id')
            ->orderBy('sort_order');
    }

    public function photos()
    {
        return $this->hasMany(MicePackagePhoto::class, 'mice_package_id');
    }

    public function reviews()
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }
}
