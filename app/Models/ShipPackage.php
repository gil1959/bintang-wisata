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
        'created_by_partner_id',
        'partner_review_status',
        'partner_review_note',
        'partner_reviewed_by',
        'partner_reviewed_at',
        'title_en',
        'label_en',
        'duration_text_en',
        'long_description_en',
        'seo_title_en',
        'seo_keywords_en',
        'seo_description_en',

    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'rating_value' => 'float',
        'rating_count' => 'int',
        'created_by_partner_id' => 'int',
        'partner_reviewed_by' => 'int',
        'partner_reviewed_at' => 'datetime',
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
