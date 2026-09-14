<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPackagePhoto extends Model
{
    use HasFactory;

    protected $table = 'hotel_package_photos';

    protected $fillable = [
        'hotel_package_id',
        'file_path',
    ];

    public function hotelPackage()
    {
        return $this->belongsTo(HotelPackage::class, 'hotel_package_id');
    }
}
