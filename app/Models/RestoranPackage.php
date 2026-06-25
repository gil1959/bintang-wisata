<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestoranPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'label',
        'slug',
        'price_per_pax',
        'thumbnail_path',
        'is_active',
        'features',

        'long_description',
        'seo_title',
        'seo_keywords',
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
        'price_per_pax' => 'float',
        'is_active' => 'boolean',
        'features' => 'array',
        'features_en' => 'array',
    ];

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
