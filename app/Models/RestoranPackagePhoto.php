<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestoranPackagePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'restoran_package_id',
        'file_path',
    ];

    public function package()
    {
        return $this->belongsTo(RestoranPackage::class, 'restoran_package_id');
    }
}
