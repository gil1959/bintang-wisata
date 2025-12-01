<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'destination',
        'duration_text',
        'short_description',
        'description',
        'include_flight_option',
        'is_active',
        'thumbnail_path',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'include_flight_option' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function priceTiers()
    {
        return $this->hasMany(TourPriceTier::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TourItinerary::class);
    }

    public function images()
    {
        return $this->hasMany(TourImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(TourReview::class);
    }
}
