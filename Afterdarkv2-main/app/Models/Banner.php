<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'banner_tag',
        'description',
        'code',
        'approved',
        'short_place',
        'bstick',
        'main',
        'category',
        'group_level',
        'started_at',
        'ended_at',
        'fpage',
        'innews',
        'device_level',
        'allow_views',
        'max_views',
        'allow_counts',
        'max_counts',
        'views',
        'clicks',
        'rubrics',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
