<?php

namespace App\Jobs;

use Aerni\Spotify\Facades\Spotify;
use App\Constants\DefaultConstants;
use App\Helpers\Helper;
use App\Models\AlbumLog;
use App\Models\AlbumSong;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAlbumDetails implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $album;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($album)
    {
        $this->album = $album;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $row = AlbumLog::query()
            ->where('album_id', $this->album->id)
            ->first();

        if (isset($row->spotify_id) && !$row->fetched) {
            $data = Spotify::album($row->spotify_id)->get();

            foreach ($data['tracks']['items'] as $item) {
                $artists = Helper::handleSpotifyArtists($item['artists']);
                $song = Helper::handleSpotifySong($item, $artists, $this->album->artwork);

                AlbumSong::create([
                    'song_id' => $song->id,
                    'album_id' => $this->album->id,
                    'priority' => $item['track_number'],
                ]);
            }

            AlbumLog::query()
                ->where('album_id', $this->album->id)
                ->update([
                    'fetched' => DefaultConstants::TRUE,
                ]);
        }
    }
}
