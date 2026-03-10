<?php

namespace App\Console\Commands;

use App\Constants\DefaultConstants;
use App\Jobs\ConvertSongToMp3;
use App\Models\Song;
use Illuminate\Console\Command;

class ConvertHlsToMp3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:hls-to-mp3-convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command converts all HLS song files to a MP3 format';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $songs = Song::query()
            ->where('mp3', DefaultConstants::FALSE)
            ->where('hls', DefaultConstants::TRUE)
            ->get();

        $bar = $this->output->createProgressBar(count($songs));

        $bar->start();

        foreach ($songs as $song) {
            ConvertSongToMp3::dispatch($song)
                ->onQueue('conversions');

            $bar->advance();
        }

        $bar->finish();
    }
}
