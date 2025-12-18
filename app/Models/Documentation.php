<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    protected $fillable = [
        'type',
        'title',
        'file_path',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getUrlAttribute(): string
    {
        $path = (string) $this->file_path;

        // kalau URL external
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // file lokal
        return asset('storage/' . ltrim($path, '/'));
    }

    public function getIsExternalAttribute(): bool
    {
        return preg_match('#^https?://#i', (string) $this->file_path) === 1;
    }
}
