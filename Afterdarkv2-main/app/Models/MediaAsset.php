<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaAsset extends Model
{
    protected $fillable = [
        'uuid',
        'parent_id',
        'assetable_type',
        'assetable_id',
        'type',
        'format',
        'quality',
        'path',
        'filename',
        'file_size',
        'bitrate',
        'sample_rate',
        'duration',
        'codec',
        'status',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'parent_id');
    }

    public function renditions(): HasMany
    {
        return $this->hasMany(MediaAsset::class, 'parent_id');
    }
}
