<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-30
 * Time: 10:08.
 */

namespace App\Http\Controllers\Api;

use App\Constants\DefaultConstants;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Playlist;
use App\Models\Slide;
use App\Models\Song;
use App\Scopes\NonAdventureScope;
use App\Services\MetatagService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class GenreController
{
    private MetatagService $metatagService;

    public function __construct(MetatagService $metatagService)
    {
        $this->metatagService = $metatagService;
    }

    public function show(): JsonResponse
    {
        $discover = (object) [];

        $discover->channels = [];
        $discover->genres = Genre::query()
            ->orderBy('priority', 'asc')
            ->discover()
            ->get();

        $discover->newAudio = Channel::query()
            ->where('allow_home', DefaultConstants::TRUE)
            ->where('alt_name', 'new-audios')
            ->orderBy('priority', 'asc')
            ->first()
            ->toArray()['objects']['data'] ?? [];

        return response()->json($discover);
    }

    public function genre(Genre $genre): JsonResponse
    {
        $this->metatagService->setMetatags($genre);

        $genreSearchString = '%' . $genre->id . '%';

        $channels = Channel::query()
            ->where('genre', 'LIKE', $genreSearchString)
            ->orderBy('priority')
            ->get();

        $slides = Slide::query()
            ->where('genre', 'LIKE', $genreSearchString)
            ->orderBy('priority')
            ->get();

        if (isset($songs) && count($songs)) {
            $genre->songs = $songs;
        } else {
            $userIds = Song::query()
                ->where('genre', 'LIKE', $genreSearchString)
                ->pluck('user_id');

            if (auth()->check()) {
                $patronUsers = collect($userIds)->unique()->filter(function (int $userId) {
                    return (bool) auth()->user()->activeUserSubscription($userId);
                })->toArray();
            } else {
                $patronUsers = collect($userIds)->unique();
            }

            $genre->songs = Song::query()
                ->withoutGlobalScope(NonAdventureScope::class)
                ->where('genre', 'LIKE', $genreSearchString)
                ->where('is_patron', false)
                ->when(auth()->check(), function ($query) use ($patronUsers) {
                    $query->orWhere(function (Builder $query) use ($patronUsers) {
                        $query->where('is_patron', true)
                            ->whereIn('user_id', $patronUsers);
                    });
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return response()->json([
            'slides' => json_decode(json_encode($slides)),
            'channels' => json_decode(json_encode($channels)),
            'genre' => $genre,
        ]);
    }

    public function songs(Genre $genre): JsonResponse
    {
        $this->metatagService->setMetatags($genre);

        $songs = Song::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genre->id . ')(,|$)');

        return response()->json($songs);
    }

    public function albums(Genre $genre): JsonResponse
    {
        $this->metatagService->setMetatags($genre);

        $albums = Album::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genre->id . ')(,|$)');

        return response()->json($albums);
    }

    public function artists(Genre $genre): JsonResponse
    {
        $this->metatagService->setMetatags($genre);

        $artists = Artist::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genre->id . ')(,|$)');

        return response()->json($artists);
    }

    public function playlists(Genre $genre): JsonResponse
    {
        $this->metatagService->setMetatags($genre);

        $playlists = Playlist::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genre->id . ')(,|$)');

        return response()->json($playlists);
    }
}
