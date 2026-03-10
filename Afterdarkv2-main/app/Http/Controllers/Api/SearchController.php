<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-02
 * Time: 16:38.
 */

namespace App\Http\Controllers\Api;

use App\Models\Album;
use App\Models\Artist;
use App\Models\City;
use App\Models\Event;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;
use App\Scopes\NonAdventureScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController
{
    private $request;

    private $term;

    private $searchParam;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->term = $this->request->input('q');
        $this->searchParam = '%' . $this->term . '%';
    }

    public function song(): JsonResponse
    {
        $result = Song::query()
            ->withoutGlobalScopes([NonAdventureScope::class])
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function artist(): JsonResponse
    {
        $result = Artist::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function album(): JsonResponse
    {
        $result = Album::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function playlist(): JsonResponse
    {
        $result = Playlist::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function user(): JsonResponse
    {
        $result = User::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function event(): JsonResponse
    {
        $result = (object) [];

        $result->events = Event::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);
        $result->users = User::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->paginate(20);
        $result->artists = Artist::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->paginate(20);
        $result->albums = Album::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);
        $result->playlists = Playlist::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function station(): JsonResponse
    {
        $result = Station::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function podcast(): JsonResponse
    {
        $result = Podcast::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }

    public function suggest(): JsonResponse
    {
        $songs = Song::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();
        $artists = Artist::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();
        $albums = Album::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();
        $playlists = Playlist::query()
            ->with('user')
            ->where('title', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();
        $stations = Station::query()
            ->where('title', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();
        $users = User::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->limit(20)
            ->get();

        return response()->json([
            'songs' => $songs,
            'artists' => $artists,
            'albums' => $albums,
            'playlists' => $playlists,
            'stations' => $stations,
            'users' => $users,
        ]);
    }

    public function city(): JsonResponse
    {
        $result = City::query()
            ->where('name', 'LIKE', $this->searchParam)
            ->paginate(20);

        return response()->json($result);
    }
}
