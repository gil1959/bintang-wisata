<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePromoBanner extends Model
{
    protected $table = 'home_promo_banners';

    protected $fillable = [
        'section',
        'thumbnail_path',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
