<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-01
 * Time: 22:01.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show(Playlist $playlistVisible): JsonResponse
    {
        $playlistVisible
            ->makeVisible('genre');

        $playlistVisible->setRelation('songs', $playlistVisible->songs()->get());

        if ($this->request->get('callback')) {
            return response()
                ->jsonp($this->request->get('callback'), $playlistVisible->songs)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($playlistVisible);
    }

    public function subscribers(Playlist $playlist): JsonResponse
    {
        return response()->json($playlist->followers);
    }

    public function collaborators(Playlist $playlist): JsonResponse
    {
        $playlist->setRelation('collaborators', $playlist->collaborators()->paginate(20));

        return response()->json($playlist->collaborators);
    }
}
