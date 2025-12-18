<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'label',
        'slug',
        'category_id',
        'destination',
        'duration_text',
        'long_description',
        'includes',
        'excludes',
        'flight_info',
        'thumbnail_path',
        'is_active',
    ];

    protected $casts = [
        'includes' => 'array',
        'excludes' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(TourCategory::class);
    }

    public function tiers()
    {
        return $this->hasMany(TourPackageTier::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TourItinerary::class)->orderBy("sort_order");
    }

    public function photos()
    {
        return $this->hasMany(TourPackagePhoto::class);
    }
    public function reviews()
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }
}
