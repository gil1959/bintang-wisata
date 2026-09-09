<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestoranMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'restoran_package_id',
        'name',
        'category',
        'price',
        'thumbnail_path',
        'is_ready',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'is_ready' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(RestoranPackage::class, 'restoran_package_id');
    }
}
