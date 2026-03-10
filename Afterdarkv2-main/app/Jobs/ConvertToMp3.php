<?php

namespace App\Jobs;

use App\Constants\DefaultConstants;
use App\Models\ChildSong;
use App\Models\FinalSong;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertToMp3 implements ShouldQueue
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
        if ($this->model->is_adventure || $this->model instanceof ChildSong || $this->model instanceof FinalSong) {
            $data = $this->model->media; /* getMedia('hls'); */
            foreach ($data as $item) {
                //  echo $item->getFullUrl();
                $item->delete();
            }
        }

        $tempFilename = Str::random(10) . '.mp3';

        $bitrate = !$this->isHd
            ? config('settings.audio_default_bitrate', 128)
            : config('settings.audio_hd_bitrate', 320);

        $formatAndBitrate = (new Mp3)
            ->setAudioKiloBitrate($bitrate);

        FFMpeg::open($this->audio->path)
            ->export()
            ->inFormat($formatAndBitrate)
            ->toDisk('public')
            ->save($tempFilename);

        $this->model->addMedia(Storage::disk('public')->path($tempFilename))
            ->usingFileName($tempFilename)
            ->withCustomProperties(['bitrate' => $bitrate])
            ->toMediaCollection(!$this->isHd ? 'audio' : 'hd_audio', config('settings.storage_audio_location', 'public'));

        $this->model->mp3 = DefaultConstants::TRUE;

        if ($this->isHd) {
            $this->model->hd = DefaultConstants::TRUE;
        }

        if ((!config('settings.audio_hd') && !config('settings.audio_stream_hls')) ||
            ($this->isHd && !config('settings.audio_stream_hls')) ||
            (config('settings.audio_hd') && $this->audio->bitrate < config('settings.audio_hd_bitrate', 320) && !config('settings.audio_stream_hls'))
        ) {
            $this->model->pending = DefaultConstants::FALSE;
            @unlink($this->audio->path);
        }

        $this->model->save();
    }
}
