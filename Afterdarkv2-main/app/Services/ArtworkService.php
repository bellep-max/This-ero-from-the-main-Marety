<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class ArtworkService
{
    public static function updateArtwork(Request $request, Model $model, bool $clearCollection = true): void
    {
        $dimensions = config('settings.image_artwork_max');

        $request->validate([
            'artwork' => 'required|image|mimes:jpeg,png,jpg,gif|max:' . config('settings.image_max_file_size'),
        ]);

        if ($clearCollection) {
            $model->clearMediaCollection('artwork');
        }

        $model->addMediaFromBase64(
            base64_encode(
                Image::read($request->file('artwork'))
                    ->coverDown($dimensions, $dimensions)
                    ->toJpeg(config('settings.image_jpeg_quality'))
            )
        )
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
    }
}
