<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = ['name', 'is_active', 'credentials'];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean'
    ];
}
