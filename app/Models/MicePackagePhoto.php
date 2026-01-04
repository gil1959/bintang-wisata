<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicePackagePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'mice_package_id',
        'file_path',
    ];

    public function package()
    {
        return $this->belongsTo(MicePackage::class, 'mice_package_id');
    }
}
