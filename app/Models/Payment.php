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
        'return_token',
        'gateway_payload',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'amount' => 'float',
    ];

    protected $hidden = ['return_token', 'gateway_payload'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
