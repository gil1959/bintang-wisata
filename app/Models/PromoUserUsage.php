<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoUserUsage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'promo_id',
        'user_id',
        'used_at'
    ];
}
