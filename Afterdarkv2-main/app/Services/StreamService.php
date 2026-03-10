<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class StreamService
{
    private const DISK_NAME = 's3';

    private const DEFAULT_TIME_AMOUNT = 5;

    public function getMp3File(Model $model, string $collectionName)
    {
        $media = $model->getFirstMedia($collectionName);

        if (!$media) {
            return null;
        }

        if ($media->disk == self::DISK_NAME) {
            header('Location: ' . $model->getFirstTemporaryUrl(
                now()->addMinutes(
                    config('settings.s3_signed_time', self::DEFAULT_TIME_AMOUNT)
                ), $collectionName)
            );
        } else {
            if (config('settings.direct_stream')) {
                header('Location: ' . $media->getUrl());
            } else {
                //                header('Content-type: '.$media->mime_type);
                //                header('Content-Length: '.$media->size);
                //                header('Content-Disposition: attachment; filename="'.$media->file_name);
                //                header('Cache-Control: no-cache');
                //                header('Accept-Ranges: bytes');

                if (config('filesystems.disks')[$media->disk]['driver'] == 'local') {
                    return readfile($media->getPath());
                } else {
                    return response()->json($media
                        ->getTemporaryUrl(
                            now()->addMinutes(
                                config('settings.s3_signed_time', self::DEFAULT_TIME_AMOUNT)
                            )
                        ));
                }
            }
        }
    }

    public function getHlsFile(Model $model, string $collectionName): array|false|string
    {
        if ($model->getFirstMedia($collectionName)->disk == self::DISK_NAME) {
            $content = stream_get_contents($model->getFirstMedia('hd_m3u8')->stream());

            foreach ($model->getMedia($collectionName) as $track) {
                $content = str_replace($track->file_name, $track->getTemporaryUrl(
                    now()->addMinutes(
                        config('settings.s3_signed_time', self::DEFAULT_TIME_AMOUNT)
                    )
                ), $content);
            }
        } else {
            $content = stream_get_contents($model->getFirstMedia('m3u8')->stream());
            foreach ($model->getMedia($collectionName) as $track) {
                $content = str_replace($track->file_name, $track->getFullUrl(), $content);
            }
        }

        return $content;
    }
}
