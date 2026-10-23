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

    public function getSavingTypeLabelAttribute(): string
    {
        return match ($this->saving_type) {
            'tabungan_liburan', 'liburan' => 'Tabungan Liburan',
            'tabungan_umrah', 'tabungan_umrah_haji', 'umroh_reguler' => 'Tabungan Umroh/Haji',
            'haji_furoda' => 'Haji Furoda',
            default => ucwords(str_replace('_', ' ', $this->saving_type ?? 'Tabungan')),
        };
    }
}
