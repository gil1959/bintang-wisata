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
        // file_path disimpan seperti: documentations/photos/xxx.jpg
        return asset('storage/' . $this->file_path);
    }
}
