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
        'price_per_day',
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

    ];



    protected $casts = [
        'price_per_day' => 'float',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
    public function category()
    {
        return $this->belongsTo(RentCarCategory::class, 'category_id');
    }
}
