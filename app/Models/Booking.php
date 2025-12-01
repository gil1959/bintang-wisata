<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'type',
        'status',
        'payment_status',
        'payment_method',
        'total_amount',
        'discount_amount',
        'final_amount',
        'promo_id',
        'with_flight',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
        'final_amount' => 'float',
        'with_flight' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function tourReviews()
    {
        return $this->hasMany(TourReview::class);
    }
}
