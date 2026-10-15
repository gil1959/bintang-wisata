<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentCarPackagePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_car_package_id',
        'file_path',
    ];

    public function package()
    {
        return $this->belongsTo(RentCarPackage::class, 'rent_car_package_id');
    }
}
