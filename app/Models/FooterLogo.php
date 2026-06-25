<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLogo extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'url',
        'sort_order',
        'is_active',
    ];
}
