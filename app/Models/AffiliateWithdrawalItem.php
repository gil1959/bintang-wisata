<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateWithdrawalItem extends Model
{
    protected $fillable = [
        'withdrawal_request_id',
        'order_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(AffiliateWithdrawalRequest::class, 'withdrawal_request_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
