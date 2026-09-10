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
        'description',
        'thumbnail_path',
        'photos',
        'is_ready',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'photos' => 'array',
        'is_ready' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(RestoranPackage::class, 'restoran_package_id');
    }

    /**
     * Get all photos for this menu (fallback to thumbnail_path if photos array is empty)
     */
    public function getAllPhotosAttribute(): array
    {
        if (!empty($this->photos) && is_array($this->photos) && count($this->photos) > 0) {
            return array_values($this->photos);
        }
        if (!empty($this->thumbnail_path)) {
            return [$this->thumbnail_path];
        }
        return [];
    }
}
