<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\TrendingInterface;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\JsonResponse;

class TrendingController extends ApiController
{
    public function index(TrendingInterface $trending): JsonResponse
    {
        $genres = Genre::all();

        return $this->success([
            'popularAudios' => $trending->popularAudios($genres),
            'topGenre' => $trending->topByGenres($genres),
            'topFemale' => $trending->topByVoice(array_search('[F] female', __('web.GENDER_TAGS')), 20),
            'topMale' => $trending->topByVoice(array_search('[M] male', __('web.GENDER_TAGS')), 20),
        ]);
    }

    public function topByGenre(Genre $genre, TrendingInterface $trending): JsonResponse
    {
        $songs = $trending->topByGenrePaginate($genre->id, 20);

        return $this->success([
            'genre' => $genre,
            'songs' => $songs,
        ]);
    }

    public function topSongs(): JsonResponse
    {
        $songs = Song::query()
            ->orderBy('plays', 'desc')
            ->simplePaginate(20);

        return $this->success([
            'songs' => $songs,
        ]);
    }

    public function topVoice(string $voice, TrendingInterface $trending): JsonResponse
    {
        $songs = $trending->topByVoicePaginate($voice, 20);

        return $this->success([
            'songs' => $songs,
        ]);
    }
}
