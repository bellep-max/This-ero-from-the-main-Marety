<?php

namespace App\Traits;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ImageMediaTrait
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('artwork')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $sizes = [
            'sm' => 60,
            'md' => 120,
            'lg' => 500,
        ];

        foreach ($sizes as $name => $size) {
            $this->addMediaConversion($name)
                ->fit(Fit::Crop, $size, $size)
                ->performOnCollections('artwork')
                ->nonOptimized()
                ->nonQueued();
        }
    }
}
