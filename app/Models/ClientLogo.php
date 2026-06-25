<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLogo extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'url',
        'sort_order',
        'is_active'
    ];
}
