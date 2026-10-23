<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentCarPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'label',
        'slug',
        'category_id',
        'price_per_hour',
        'price_per_12_hours',
        'price_per_24_hours',
        'thumbnail_path',
        'is_active',
        'features',

        'long_description',
        'seo_title',
        'seo_keywords',
        'seo_image_path',
        'social_title',
        'social_description',
        'seo_description',
        'created_by_partner_id',
        'partner_review_status',
        'partner_review_note',
        'partner_reviewed_by',
        'partner_reviewed_at',
        'title_en',
        'label_en',
        'long_description_en',
        'features_en',
        'seo_title_en',
        'seo_keywords_en',
        'seo_description_en',


    ];



    protected $casts = [
        'price_per_hour' => 'float',
        'price_per_12_hours' => 'float',
        'price_per_24_hours' => 'float',
        'is_active' => 'boolean',
        'features' => 'array',
        'features_en' => 'array',
    ];

    public function getPricePer12HoursAttribute($value)
    {
        if (!is_null($value) && (float)$value > 0) {
            return (float)$value;
        }
        $hourly = (float)($this->attributes['price_per_hour'] ?? 0);
        return $hourly > 0 ? $hourly : 0;
    }

    public function getPricePer24HoursAttribute($value)
    {
        if (!is_null($value) && (float)$value > 0) {
            return (float)$value;
        }
        $hourly = (float)($this->attributes['price_per_hour'] ?? 0);
        if ($hourly > 0) {
            return $hourly * 2;
        }
        $p12 = (float)($this->price_per_12_hours ?? 0);
        return $p12 > 0 ? ($p12 * 2) : 0;
    }

    public function getPricePerHourAttribute($value)
    {
        if (!is_null($value) && (float)$value > 0) {
            return (float)$value;
        }
        $p12 = (float)($this->attributes['price_per_12_hours'] ?? 0);
        return $p12 > 0 ? round($p12 / 12) : 0;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function photos()
    {
        return $this->hasMany(RentCarPackagePhoto::class);
    }

    public function category()
    {
        return $this->belongsTo(RentCarCategory::class, 'category_id');
    }
}

