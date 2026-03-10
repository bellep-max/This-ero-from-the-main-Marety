<?php

namespace App\Console\Commands;

use App\Constants\DefaultConstants;
use App\Models\Song;
use App\Services\SongService;
use Exception;
use Illuminate\Console\Command;

class ConvertTrackToMp3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:convert {uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command converts selected HLS song file to a MP3 format';

    /**
     * Execute the console command.
     */
    public function handle(SongService $songService): void
    {
        try {
            $song = Song::query()
                ->where('uuid', $this->argument('uuid'))
                ->where('mp3', DefaultConstants::FALSE)
                ->where('hls', DefaultConstants::TRUE)
                ->firstOrFail();

            $songService->convertHLSToMp3($song, $song->hd);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }

        $this->info("Converted $song->title HLS song to MP3");
    }
}
