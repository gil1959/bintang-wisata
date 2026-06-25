<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'capacity',
        'transmission',
        'price_per_day',
        'price_with_driver_per_day',
        'include_fuel',
        'is_active',
        'thumbnail_path',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'price_per_day' => 'float',
        'price_with_driver_per_day' => 'float',
        'include_fuel' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class, 'item_id')
            ->where('item_type', 'rental');
    }
}
