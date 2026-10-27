<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'date',
        'hits',
    ];

    protected $casts = [
        'date' => 'date',
        'hits' => 'integer',
    ];
}
