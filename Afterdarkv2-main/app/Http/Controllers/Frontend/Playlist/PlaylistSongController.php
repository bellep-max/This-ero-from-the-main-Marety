<?php

namespace App\Http\Controllers\Frontend\Playlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Playlist\PlaylistAddSongRequest;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use App\Services\PlaylistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PlaylistSongController extends Controller
{
    public function __construct(
        private readonly PlaylistService $playlistService,
    ) {}

    public function store(Playlist $playlist, PlaylistAddSongRequest $request): RedirectResponse
    {
        Gate::authorize('addSong', $playlist);

        $this->playlistService->addPlaylistSong($playlist, $request);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Song added successfully',
        ]);

        return redirect()->back(303);
    }

    public function destroy(Playlist $playlist, Song $song): RedirectResponse
    {
        Gate::authorize('addSong', $playlist);

        $this->playlistService->removePlaylistSong($playlist, $song);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Song removed successfully',
        ]);

        return redirect()->back(303);
    }

    //    public function getCollaborators(Playlist $playlist, User $user): RedirectResponse
    //    {
    //        Gate::authorize('show', $playlist);
    //
    //
    //
    // //        session()->flash('message', [
    // //            'level' => 'success',
    // //            'content' => $request->input('collaboration')
    // //                ? 'Playlist is now collaborative'
    // //                : 'Playlist collaborative mode was disabled',
    // //        ]);
    //
    //        return redirect()->back(303);
    //    }
}
