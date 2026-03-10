<?php

namespace App\Traits;

use App\Constants\DiskConstants;

trait ArtworkTrait
{
    public function getArtworkAttribute(): string
    {
        if (!$media = $this->getFirstMedia('artwork')) {
            return asset('assets/images/artist.png');
        }

        if ($media->disk === DiskConstants::WASABI) {
            try {
                return $media->getTemporaryUrl(now()->addMinutes(config('settings.s3_signed_time', 5)), 'lg');
            } catch (\Exception $e) {
                // Fallback to full URL if temporary URL generation fails (e.g., missing S3 config)
                try {
                    return $media->getFullUrl('lg');
                } catch (\Exception $e) {
                    return asset('assets/images/artist.png');
                }
            }
        }

        return $media->getFullUrl('lg');
    }
}
