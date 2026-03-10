<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class File extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(config('settings.image_max_thumbnail_size'))
            ->keepOriginalImageFormat()
            ->performOnCollections('images')->nonOptimized()->nonQueued();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
