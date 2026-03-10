<?php

namespace App\Http\Controllers\Backend;

use App\Constants\ActionConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Playlist\PlaylistTrackMassActionRequest;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class PlaylistTrackController
{
    public function show(Playlist $playlist): View|Application|Factory
    {
        return view('backend.playlists.tracklist')
            ->with([
                'playlist' => $playlist->load('songs'),
            ]);
    }

    public function batch(Playlist $playlist, PlaylistTrackMassActionRequest $request)
    {
        if (!$request->input('action')) {
            $songIds = $request->input('songIds');

            foreach ($songIds as $index => $songId) {
                $playlist->songs()->updateExistingPivot($songId, [
                    ['priority' => $index],
                ]);
            }

            return MessageHelper::redirectMessage('Priority successfully changed!');
        }

        if ($request->input('action') == ActionConstants::REMOVE_PLAYLIST_SONG) {
            $playlist->songs()->detach($request->input('ids'));

            return MessageHelper::redirectMessage('Songs successfully removed from the playlist!');
        } elseif ($request->input('action') == ActionConstants::DELETE) {
            Song::destroy($request->input('ids'));

            return MessageHelper::redirectMessage('Songs successfully deleted!');
        }
    }
}
