<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelBusPackagePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_bus_package_id',
        'photo_path',
    ];

    public function package()
    {
        return $this->belongsTo(TravelBusPackage::class, 'travel_bus_package_id');
    }
}
