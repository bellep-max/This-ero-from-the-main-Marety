<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Playlist\PlaylistAddSongRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborateRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborationResponseRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistSetCollabRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistStoreRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Playlist\PlaylistEditResource;
use App\Http\Resources\Playlist\PlaylistFullResource;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\User\UserCollaboratorResource;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PlaylistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PlaylistController extends ApiController
{
    public function __construct(
        private readonly PlaylistService $playlistService,
        private readonly NotificationService $notificationService,
    ) {}

    public function store(PlaylistStoreRequest $request): JsonResponse
    {
        $playlist = $this->playlistService->createPlaylist($request);

        return $this->success(PlaylistShortResource::make($playlist), 'Playlist created successfully', 201);
    }

    public function show(Playlist $playlist): JsonResponse
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

        return $this->success([
            'playlist' => PlaylistFullResource::make($playlist),
            'following' => UserCollaboratorResource::collection($this->playlistService->getUserPlaylistCollaborators($playlist, auth()->user())),
            'related' => PlaylistFullResource::collection($this->playlistService->getRelatedPlaylists($playlist)),
            'comments' => CommentResource::collection($playlist->comments),
        ]);
    }

    public function edit(Playlist $playlist): JsonResponse
    {
        Gate::authorize('edit', $playlist);

        $playlist
            ->loadMissing([
                'songs',
                'user',
                'genres:id',
            ])
            ->loadSum('songs', 'duration');

        return $this->success([
            'playlist' => PlaylistEditResource::make($playlist),
        ]);
    }

    public function update(Playlist $playlist, PlaylistUpdateRequest $request): JsonResponse
    {
        Gate::authorize('edit', $playlist);

        $playlist = $this->playlistService->updatePlaylist($playlist, $request);

        return $this->success(PlaylistShortResource::make($playlist), 'Playlist updated successfully');
    }

    public function destroy(Playlist $playlist): JsonResponse
    {
        Gate::authorize('destroy', $playlist);

        $playlist->songs()->detach();
        $playlist->delete();

        return $this->success(null, 'Playlist deleted successfully');
    }

    public function addSong(Playlist $playlist, PlaylistAddSongRequest $request): JsonResponse
    {
        Gate::authorize('edit', $playlist);

        $this->playlistService->addPlaylistSong($playlist, $request);

        return $this->success(null, 'Song added successfully');
    }

    public function removeSong(Playlist $playlist, Song $song): JsonResponse
    {
        Gate::authorize('addSong', $playlist);

        $this->playlistService->removePlaylistSong($playlist, $song);

        return $this->success(null, 'Song removed successfully');
    }

    public function setCollab(Playlist $playlist, User $user, PlaylistSetCollabRequest $request): JsonResponse
    {
        Gate::authorize('edit', $playlist);

        $playlist->update($request->validated());

        if (!$request->input('collaboration')) {
            $playlist->collaboratorsTest()->detach();
        }

        return $this->success(null, $request->input('collaboration')
            ? 'Playlist is now collaborative'
            : 'Playlist collaborative mode was disabled');
    }

    public function inviteCollaborator(Playlist $playlist, PlaylistCollaborateRequest $request): JsonResponse
    {
        Gate::authorize('edit', $playlist);

        $this->playlistService->inviteUserToCollaborate($playlist->id, $request);

        return $this->success(null, 'User invited successfully');
    }

    public function respondCollaboration(Playlist $playlist, User $user, PlaylistCollaborationResponseRequest $request): JsonResponse
    {
        $result = $this->playlistService->responseToCollaborate($playlist->id, $user, $request);

        return $this->success(['accepted' => $result], $result ? 'Collaboration accepted' : 'Collaboration declined');
    }
}
