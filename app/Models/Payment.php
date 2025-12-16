<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',          // manual / gateway
        'amount',
        'proof_image',
        'gateway_reference',
        'status',
        'gateway_name',
        'channel_code',
        'payment_url',
        'gateway_payload',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'amount' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
