<?php

namespace App\Jobs;

use App\Constants\DefaultConstants;
use App\Models\ArtistLog;
use App\Models\Genre;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spotify;

class GetArtistDetails implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $artist;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($artist)
    {
        $this->artist = $artist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $row = ArtistLog::query()
            ->where('artist_id', $this->artist->id)
            ->first();

        if (isset($row->spotify_id) && !$row->fetched) {
            $data = Spotify::artist($row->spotify_id)->get();

            // handle genre
            if (isset($data['genres']) && count($data['genres'])) {
                $genres = [];
                foreach ($data['genres'] as $name) {
                    $genreRow = Genre::query()
                        ->where('alt_name', str_slug($name))
                        ->first();

                    if (isset($genreRow->id)) {
                        $genres[] = $genreRow->id;
                    } else {
                        $genre = Genre::create([
                            'name' => $name,
                            'alt_name' => str_slug($name),
                            'discover' => DefaultConstants::FALSE,
                        ]);

                        $genres[] = $genre->id;
                    }
                }
            }

            // handle artist image
            if (isset($data['images'][1])) {
                $artist = $this->artist;

                if (isset($genres) && is_array($genres)) {
                    $artist->genre = implode(',', $genres);
                }

                $artist->addMediaFromUrl($data['images'][1]['url'])
                    ->usingFileName(time() . '.jpg')
                    ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
                $artist->save();
            }

            $data = Spotify::artistAlbums($row->spotify_id)->get();

            foreach ($data['items'] as $item) {
                $artists = handleSpotifyArtists($item['artists']);
                handleSpotifyAlbum($artists, $item);
            }

            $data = Spotify::artistTopTracks($row->spotify_id)->get();

            foreach ($data['tracks'] as $item) {
                $artists = handleSpotifyArtists($item['album']['artists']);
                handleSpotifyAlbum($artists, $item['album']);
                handleSpotifySong($item, $artists);
            }

            ArtistLog::query()
                ->where('artist_id', $this->artist->id)
                ->update([
                    'fetched' => DefaultConstants::TRUE,
                ]);
        }
    }
}
