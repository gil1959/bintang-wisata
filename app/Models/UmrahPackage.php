<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UmrahPackage extends Model
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
        'title_en',
        'label_en',
        'duration_text_en',
        'long_description_en',
        'seo_title_en',
        'seo_keywords_en',
        'seo_description_en'

    ];

    public function category()
    {
        return $this->belongsTo(UmrahCategory::class, 'category_id');
    }

    public function photos()
    {
        return $this->hasMany(UmrahPackagePhoto::class, 'umrah_package_id');
    }

    public function tiers()
    {
        return $this->hasMany(UmrahPackageTier::class, 'umrah_package_id')->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }
}
