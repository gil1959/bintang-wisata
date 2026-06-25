<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'code',

        'product_type',
        'product_id',
        'product_slug',
        'product_name',

        'promo_id',
        'promo_code',

        'platform',
        'platform_id',

        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',

        'sales_url',
        'checkout_url',

        'clicks',
        'conversions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
