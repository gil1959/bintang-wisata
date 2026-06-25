<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'item_type',
        'item_id',
        'audience_type',
        'qty',
        'unit_price',
        'subtotal',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class, 'item_id')
            ->where('item_type', 'tour');
    }

    public function rentalUnit()
    {
        return $this->belongsTo(RentalUnit::class, 'item_id')
            ->where('item_type', 'rental');
    }
}
