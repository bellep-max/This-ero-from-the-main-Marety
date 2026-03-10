<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\SongService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertSongToMp3 implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Song $song;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
     * Execute the job.
     */
    public function handle(SongService $songService): void
    {
        $songService->convertHLSToMp3($this->song, $this->song->hd);
    }
}
