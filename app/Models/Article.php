<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'is_published',
        'published_at',
        'seo_title',
        'seo_description',
        'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($article) {
            if (!$article->slug) {
                $article->slug = Str::slug($article->title);
            }

            if ($article->is_published && !$article->published_at) {
                $article->published_at = now();
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
