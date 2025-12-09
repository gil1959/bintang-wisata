<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'method_name',
        'slug',
        'type',
        'bank_name',
        'account_number',
        'account_holder',
        'gateway_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
