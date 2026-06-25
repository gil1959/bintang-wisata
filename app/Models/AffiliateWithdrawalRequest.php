<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateWithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'payout_method',
        'payout_provider',
        'account_name',
        'account_number',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function items()
{
    return $this->hasMany(AffiliateWithdrawalItem::class, 'withdrawal_request_id');
}

public function orders()
{
    // akses cepat order yang termasuk dalam request ini
    return $this->belongsToMany(
        Order::class,
        'affiliate_withdrawal_items',
        'withdrawal_request_id',
        'order_id'
    )->withPivot('amount')->withTimestamps();
}

}
