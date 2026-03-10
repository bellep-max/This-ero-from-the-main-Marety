<?php

namespace App\Http\Controllers\Frontend\Playlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborateRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborationResponseRequest;
use App\Models\Playlist;
use App\Models\User;
use App\Services\PlaylistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PlaylistCollaboratorController extends Controller
{
    public function __construct(
        private readonly PlaylistService $playlistService,
    ) {}

    public function store(Playlist $playlist, PlaylistCollaborateRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $playlist);

        $this->playlistService->inviteUserToCollaborate($playlist->id, $request);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'User invited successfully',
        ]);

        return redirect()->back(303);
    }

    public function response(Playlist $playlist, User $user, PlaylistCollaborationResponseRequest $request): RedirectResponse
    {
        return $this->playlistService->responseToCollaborate($playlist->id, $user, $request)
            ? to_route('playlists.show', $playlist)
            : redirect()->back(303);
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
