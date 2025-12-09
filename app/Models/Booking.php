<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'invoice',
        'booking_type',
        'booking_id',
        'name',
        'email',
        'phone',
        'pickup_date',
        'return_date',
        'people_count',
        'base_price',
        'discount',
        'final_price',
        'promo_id',
        'payment_type',
        'payment_code',
        'status',
        'proof_path'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $last = Booking::max('id') + 1;
            $booking->invoice = 'BW-' . str_pad($last, 5, '0', STR_PAD_LEFT);
        });
    }
}
