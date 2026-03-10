<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Enums\RoleEnum;
use App\Http\Requests\Frontend\Song\SongUpdateRequest;
use App\Models\Group;
use App\Models\Song;
use App\Models\Tag;
use Exception;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class SongService
{
    public function updateSong(Song $song, SongUpdateRequest $request): Song
    {
        $song->update($request->validated());

        $song->genres()->sync($request->input('genres'));

        if ($request->filled('tags')) {
            $tagIds = [];

            foreach ($request->input('tags') as $tag) {
                if ($tag['id'] < 0) {
                    $newTag = Tag::firstOrCreate([
                        'tag' => $tag['tag'],
                    ], [
                        'tag' => $tag['tag'],
                    ]);

                    $tagIds[] = $newTag->id;
                } else {
                    $tagIds[] = $tag['id'];
                }
            }

            $song->tags()->detach();
            $song->tags()->attach(array_unique($tagIds));
        } else {
            $song->tags()->detach();
        }

        if ($request->hasFile('artwork')) {
            $song->clearMediaCollection('artwork');

            $song->addMedia($request->file('artwork'))
                ->usingFileName(Str::random(10) . '.jpg')
                ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
        }

        return $song;
    }

    public function convertHLSToMp3(Song $song, bool $hd = false): bool
    {
        $tempFilename = Str::random(10) . '.mp3';

        $bitrate = $hd
            ? config('settings.audio_hd_bitrate', 320)
            : config('settings.audio_default_bitrate', 128);

        $diskName = config('settings.storage_audio_location', 'public');
        $collectionName = $hd ? 'hd_audio' : 'audio';

        $formatAndBitrate = (new Mp3)
            ->setAudioKiloBitrate($bitrate);

        // todo this was written to work with Spatie files but they didn't probe
        //        $collectionName = $hd ? 'm3u8' : 'hd_m3u8';

        //        FFMpeg::fromDisk($diskName)
        //            ->openUrl(
        //                $song->getFirstMedia('m3u8')
        //                    ->getTemporaryUrl(
        //                        now()->addMinutes(config('settings.s3_signed_time', 5))
        //                    )
        //            )
        //            ->export()
        //            ->inFormat($formatAndBitrate)
        //            ->toDisk('public')
        //            ->onProgress(function ($percentage) {
        //                echo "{$percentage}% transcoded";
        //            })
        //            ->save($tempFilename);

        try {
            FFMpeg::openUrl($song->file_url)
                ->export()
                ->inFormat($formatAndBitrate)
                ->toDisk('public')
                ->onProgress(function ($percentage) {
                    echo "$percentage% transcoded";
                })
                ->save($tempFilename);

            $song->addMedia(Storage::disk('public')->path($tempFilename))
                ->usingFileName($tempFilename)
                ->withCustomProperties(['bitrate' => $bitrate])
                ->toMediaCollection($collectionName, $diskName);

            $song->mp3 = DefaultConstants::TRUE;

            if ($hd) {
                $song->hd = DefaultConstants::TRUE;
            }

            Storage::disk('public')->delete($tempFilename);

            Log::info("$song->uuid converted successfully to MP3.");

            return $song->save();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }

        return true;
    }

    public function downloadSong(Song $song, bool $hd = false): string
    {
        if (!$song->mp3 || !$song->allow_download) {
            abort(404);
        }

        if ($hd) {
            $mediaCollectionName = 'hd_audio';
            $allowDownloadOption = 'option_download_hd';
        } else {
            $mediaCollectionName = 'audio';
            $allowDownloadOption = 'option_download';
        }

        // todo fix this
        //        if (!auth()->user()->hasAnyRole(RoleEnum::getAdminRoles()) || !Group::getValue($allowDownloadOption)) {
        //            abort(403);
        //        }

        if (!$media = $song->getFirstMedia($mediaCollectionName)) {
            abort(404, 'Audio file not found for the requested quality.');
        }

        if (!Storage::disk('wasabi')->exists($media->getPath())) {
            abort(404);
        }

        return Storage::disk('wasabi')
            ->temporaryUrl($media->getPath(), now()->addMinutes(5), [
                'ResponseContentType' => $media->mime_type,
                'ResponseContentDisposition' => 'attachment; filename="' . $song->title . '.mp3"',
            ]);
    }
}
