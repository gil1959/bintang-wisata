<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateUserCoupon extends Model
{
    protected $fillable = [
        'user_id',
        'promo_id',
        'alias_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
