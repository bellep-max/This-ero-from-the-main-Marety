<?php

namespace App\Jobs;

use App\Constants\DefaultConstants;
use Exception;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertToHLS implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Model $model;

    protected $audio;

    protected bool $isHd;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Model $model, $audio, bool $isHd = false)
    {
        $this->model = $model;
        $this->audio = $audio;
        $this->isHd = $isHd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $tempFolder = Str::random(32);
            Storage::disk('public')->makeDirectory($tempFolder);
            $tempFile = Str::random(32);

            $bitrate = !$this->isHd
                ? config('settings.audio_default_bitrate', 128)
                : config('settings.audio_hd_bitrate', 320);

            //            $formatBitrate = (new X264)->setKiloBitrate($bitrate);

            if (config('settings.audio_hls_drm')) {
                exec(env('FFMPEG_PATH', '/usr/bin/ffmpeg') . ' -i ' . $this->audio->path . ' -c:a aac -b:a ' . $bitrate . 'k -vn -hls_list_size 0 -hls_time 20 -hls_key_info_file ' . public_path('enc.keyinfo') . ' ' . Storage::disk('public')->path($tempFolder . '/' . $tempFile . '.m3u8'), $status, $var);
            } else {
                exec(env('FFMPEG_PATH', '/usr/bin/ffmpeg') . ' -i ' . $this->audio->path . ' -c:a aac -b:a ' . $bitrate . 'k -vn -hls_list_size 0 -hls_time 20 ' . Storage::disk('public')->path($tempFolder . '/' . $tempFile . '.m3u8'), $status, $var);
            }

            //            $shouldEncrypt = config('settings.audio_hls_drm');
            //
            //            $exporter = FFMpeg::open($this->audio->path)
            //                ->exportForHLS()
            //                ->addFormat($formatBitrate)
            //                ->toDisk('public');
            //
            //            if ($shouldEncrypt) {
            //                $exporter->withEncryptionKey(public_path('enc.keyinfo'));
            //            }
            //
            //            $exporter->save($tempFolder.'/'.$tempFile.'.m3u8');

            if ($var == 0) {
                foreach (File::allFiles(Storage::disk('public')->path($tempFolder)) as $file) {
                    if (ends_with($file, ['.ts'])) {
                        $this->model->addMediaFromDisk($tempFolder . '/' . trim(basename($file) . PHP_EOL), 'public')->toMediaCollection(!$this->isHd ? 'hls' : 'hd_hls', config('settings.storage_audio_location', 'public'));
                    }
                }

                $collection = !$this->isHd
                    ? 'm3u8'
                    : 'hd_m3u8';

                $this->model->hls = DefaultConstants::TRUE;
                $this->model->addMedia(Storage::disk('public')->path($tempFolder . '/' . $tempFile . '.m3u8'))
                    ->withCustomProperties(['bitrate' => $bitrate])
                    ->toMediaCollection($collection, config('settings.storage_audio_location', 'public'));
                // Delete the temp file
                $this->model->save();
                //                Storage::disk('public')->deleteDirectory($tempFolder, true);
                //                sleep(1);
                //                Storage::disk('public')->deleteDirectory($tempFolder);

                if (
                    !config('settings.audio_hd') ||
                    (config('settings.audio_hd') && $this->audio->bitrate < config('settings.audio_hd_bitrate', 320)) ||
                    $this->isHd
                ) {
                    $this->model->pending = DefaultConstants::FALSE;
                    @unlink($this->audio->path);
                }

                $this->model->save();
            } else {
                abort(500, 'FFMPEG HLS conversion has failed!');
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
        }
    }
}
