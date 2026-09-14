<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    use HasFactory;

    protected $table = 'hotel_rooms';

    protected $fillable = [
        'hotel_package_id',
        'name',
        'room_size',
        'bed_type',
        'max_guests',
        'has_shower',
        'has_wifi',
        'has_breakfast',
        'price',
        'price_with_breakfast',
        'original_price',
        'available_rooms',
        'facilities',
        'description',
        'photo_path',
        'photos',
        'is_ready',
        'sort_order',
    ];

    protected $casts = [
        'max_guests' => 'integer',
        'has_shower' => 'boolean',
        'has_wifi' => 'boolean',
        'has_breakfast' => 'boolean',
        'price' => 'float',
        'price_with_breakfast' => 'float',
        'original_price' => 'float',
        'available_rooms' => 'integer',
        'facilities' => 'array',
        'photos' => 'array',
        'is_ready' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function hotelPackage()
    {
        return $this->belongsTo(HotelPackage::class, 'hotel_package_id');
    }

    /**
     * Get all photos for this room (fallback to photo_path if photos array is empty)
     */
    public function getAllPhotosAttribute(): array
    {
        if (!empty($this->photos) && is_array($this->photos) && count($this->photos) > 0) {
            return array_values($this->photos);
        }
        if (!empty($this->photo_path)) {
            return [$this->photo_path];
        }
        return [];
    }
}
