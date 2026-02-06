<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganUmrahDeposit extends Model
{
    protected $table = 'tabungan_umrah_deposits';

    protected $fillable = [
        'account_id',
        'user_id',
        'payment_method_id',
        'amount',
        'proof_image',
        'status',
        'note',
        'submitted_at',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(TabunganUmrahAccount::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
{
    return $this->belongsTo(\App\Models\PaymentMethod::class, 'payment_method_id');
}


    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
