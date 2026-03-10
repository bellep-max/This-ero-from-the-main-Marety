<?php

namespace App\Http\Controllers\Frontend\Playlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Playlist\PlaylistAddSongRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistSetCollabRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistStoreRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Playlist\PlaylistEditResource;
use App\Http\Resources\Playlist\PlaylistFullResource;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\User\UserCollaboratorResource;
use App\Models\Playlist;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PlaylistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PlaylistController extends Controller
{
    public function __construct(
        private readonly PlaylistService $playlistService,
        private readonly NotificationService $notificationService,
    ) {}

    public function store(PlaylistStoreRequest $request): RedirectResponse
    {
        $playlist = $this->playlistService->createPlaylist($request);

        return to_route('playlists.show', $playlist);
    }

    public function show(Playlist $playlist): Response
    {
        Gate::authorize('show', $playlist);

        $playlist
            ->loadMissing([
                'user:id,uuid,name,username,linktree_link',
                'songs',
                'followers:id,uuid,name,username',
                'approvedCollaborators:id,uuid,name,username',
                'activities',
            ])
            ->loadCount([
                'songs',
                'followers',
                'approvedCollaborators',
                'loves',
                'followers',
            ])
            ->loadSum('songs', 'duration');

        if ($playlist->allow_comments) {
            $playlist->loadMissing([
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->orderBy('created_at', 'desc')
                        ->with([
                            'replies.user:id,name',
                            'user:id,name',
                        ]);
                },
            ]);
        }

        $this->notificationService->markAsRead($playlist);

        return Inertia::render('Playlist/Show', [
            'playlist' => PlaylistFullResource::make($playlist),
            'following' => UserCollaboratorResource::collection($this->playlistService->getUserPlaylistCollaborators($playlist, auth()->user())),
            'related' => PlaylistFullResource::collection($this->playlistService->getRelatedPlaylists($playlist)),
            'comments' => CommentResource::collection($playlist->comments),
        ]);
    }

    public function edit(Playlist $playlist): Response
    {
        Gate::authorize('edit', $playlist);

        $playlist
            ->loadMissing([
                'songs',
                'user',
                'genres:id',
            ])
            ->loadSum('songs', 'duration');

        return Inertia::render('Playlist/Edit', [
            'playlist' => PlaylistEditResource::make($playlist),
        ]);
    }

    public function update(Playlist $playlist, PlaylistUpdateRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $playlist);

        $playlist = $this->playlistService->updatePlaylist($playlist, $request);

        return redirect()
            ->back(303)
            ->with([
                'success' => 'Playlist updated successfully',
                'data' => PlaylistShortResource::make($playlist),
            ]);
    }

    public function destroy(Playlist $playlist): RedirectResponse
    {
        Gate::authorize('destroy', $playlist);

        $playlist->songs()->detach();
        $playlist->delete();

        return to_route('users.playlists', $playlist->user);
    }

    public function addSong(Playlist $playlist, PlaylistAddSongRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $playlist);

        $this->playlistService->addPlaylistSong($playlist, $request);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Song added successfully',
        ]);

        return redirect()->back(303);
    }

    public function setCollab(Playlist $playlist, User $user, PlaylistSetCollabRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $playlist);

        $playlist->update($request->validated());

        if (!$request->input('collaboration')) {
            $playlist->collaboratorsTest()->detach();
        }

        session()->flash('message', [
            'level' => 'success',
            'content' => $request->input('collaboration')
                ? 'Playlist is now collaborative'
                : 'Playlist collaborative mode was disabled',
        ]);

        return redirect()->back(303);
    }
}
