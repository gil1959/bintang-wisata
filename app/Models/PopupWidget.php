<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupWidget extends Model
{
    protected $table = 'popup_widgets';

    protected $fillable = [
    'is_enabled',
    'name',
    'title',
    'body_format',
    'body_html',
    'body_text',
    'image_path',
    'primary_button_text',
    'primary_button_link',
    'secondary_button_text',
    'secondary_button_link',
    'include_paths',
    'exclude_paths',
    'show_on_mobile',
    'show_on_desktop',
    'delay_seconds',
    'frequency',
    'start_at',
    'end_at',
];


    protected $casts = [
        'is_enabled' => 'boolean',
        'include_paths' => 'array',
        'exclude_paths' => 'array',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'delay_seconds' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'body_format' => 'string',

    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }
}
