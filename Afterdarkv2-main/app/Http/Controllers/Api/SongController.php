<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-28
 * Time: 15:44.
 */

namespace App\Http\Controllers\Api;

use App\Constants\TypeConstants;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongController
{
    public function show(Song $song, Request $request): JsonResponse
    {
        if ($request->get('callback')) {
            return response()
                ->jsonp($request->get('callback'), [$song])
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($song->append(['genres']));
    }

    public function autoplay(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:activity,song,artist,album,playlist,queue,user,genre,recent,community,obsessed,trending',
            'id' => 'nullable|integer',
            'recent_songs' => 'nullable|string',
        ]);

        $song = new Song;

        switch ($request->input('type')) {
            case TypeConstants::QUEUE:
            case TypeConstants::SONG:
                break;
            case TypeConstants::ARTIST:
                $song = $song->where('artistIds', 'REGEXP', '(^|,)(' . $request->input('id') . ')(,|$)');
                break;
            case TypeConstants::ALBUM:
                $song = $song->leftJoin('album_songs', 'album_songs.song_id', '=', 'songs.id')
                    ->select(['songs.*', 'album_songs.id as host_id']);
                $song = $song->where(function ($query) use ($request) {
                    $query->where('album_songs.album_id', '=', $request->input('id'));
                });
                break;
            case TypeConstants::PLAYLIST:
                $song = $song->leftJoin('playlist_songs', 'playlist_songs.song_id', '=', 'songs.id')
                    ->select(['songs.*', 'playlist_songs.id as host_id']);
                $song = $song->where(function ($query) use ($request) {
                    $query->where('playlist_songs.playlist_id', '=', $request->input('id'));
                });
                break;
            case TypeConstants::RECENT:
            case TypeConstants::USER:
                $user = User::find($request->input('id'));
                $song = $user->recent();
                break;
            case TypeConstants::GENRE:
                $song = $song->where('genre', 'REGEXP', '(^|,)(' . implode(',', $request->input('id')) . ')(,|$)');
                break;
            case TypeConstants::COMMUNITY:
                $user = User::find($request->input('id'));
                $song = $user->communitySongs();
                break;
            case TypeConstants::OBSESSED:
                $user = User::find($request->input('id'));
                $song = $user->obsessed();
                break;
            default:
                $song = new Song;
                break;
        }

        if ($request->input('recent_songs')) {
            $song = $song->whereNotIn('songs.id', explode(',', $request->input('recent_songs')));
        }

        $song = $song->inRandomOrder()->first();

        return response()->json($song);
    }

    public function songFromIds(Request $request): JsonResponse
    {
        $songs = Song::query()
            ->whereIn('id', explode(',', $request->route('ids')))
            ->get();

        if ($request->get('callback')) {
            return response()
                ->jsonp($request->get('callback'), $songs)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($songs);
    }
}
