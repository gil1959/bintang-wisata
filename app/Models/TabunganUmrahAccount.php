<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganUmrahAccount extends Model
{
    protected $table = 'tabungan_umrah_accounts';

    protected $fillable = [
        'user_id',
        'full_name',
        'whatsapp',
        'saving_type',
        'status',
        'target_amount',
        'target_departure_date',
        'approved_at',
        'rejected_at',
        'rejected_reason',
        'suspended_at',
    ];

    protected $casts = [
        'target_departure_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deposits()
    {
        return $this->hasMany(TabunganUmrahDeposit::class, 'account_id');
    }

    public function approvedDeposits()
    {
        return $this->deposits()->where('status', 'approved');
    }

    public function getApprovedTotalAttribute()
    {
        return (int) $this->approvedDeposits()->sum('amount');
    }
}
