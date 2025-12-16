<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'credentials',
        'is_active',
        'channels',
        'channels_synced_at',
    ];

    protected $casts = [
        'credentials' => 'array',
        'channels' => 'array',
        'is_active' => 'boolean',
        'channels_synced_at' => 'datetime',
    ];
}
