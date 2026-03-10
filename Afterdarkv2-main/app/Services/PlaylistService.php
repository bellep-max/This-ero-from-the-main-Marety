<?php

namespace App\Services;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Enums\ActivityTypeEnum;
use App\Events\AddedTrackToPlaylist;
use App\Events\RemovedTrackFromPlaylist;
use App\Helpers\Helper;
use App\Http\Requests\Frontend\Playlist\PlaylistAddSongRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborateRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistCollaborationResponseRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistStoreRequest;
use App\Http\Requests\Frontend\Playlist\PlaylistUpdateRequest;
use App\Models\Collaborator;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PlaylistService
{
    public function createPlaylist(PlaylistStoreRequest $request): Playlist
    {
        $playlist = auth()->user()->playlists()->create($request->validated());

        if ($request->filled('genres')) {
            $playlist->genres()->sync($request->input('genres'));
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $playlist);
        }

        return $playlist;
    }

    public function updatePlaylist(Playlist $playlist, PlaylistUpdateRequest $request): Playlist
    {
        $playlist->update($request->all());

        if ($request->filled('genres')) {
            $playlist->genres()->sync($request->input('genres'));
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $playlist);
        }

        return $playlist;
    }

    public function addPlaylistSong(Playlist $playlist, PlaylistAddSongRequest $request): void
    {
        $song = Song::query()
            ->where('uuid', $request->input('song_uuid'))
            ->first();

        $playlist->songs()->syncWithoutDetaching($song->id);

        AddedTrackToPlaylist::dispatch($playlist, $song);
    }

    public function removePlaylistSong(Playlist $playlist, Song $song): void
    {
        $playlist->songs()->detach($song->id);

        RemovedTrackFromPlaylist::dispatch($playlist, $song);
    }

    public function getPlaylistDuration(Playlist $playlist): string
    {
        $playingDuration = 0;

        if (count($playlist->playlistSongs)) {
            foreach ($playlist->playlistSongs as $song) {
                $playingDuration += $song->duration;
            }
        }

        return Helper::humanTime($playingDuration);
    }

    public function getRelatedPlaylists(Playlist $playlist, int $limit = 5): Collection
    {
        return Playlist::query()
            ->whereNot('id', $playlist->id)
            ->where('user_id', $playlist->user_id)
            ->limit($limit)
            ->get();
    }

    public function getUserPlaylistCollaborators(Playlist $playlist, User $user): Collection
    {
        return $user->following()
            ->with(['collaboratedPlaylists' => function ($query) use ($playlist) {
                $query->where('playlist_id', $playlist->id);
            }])
            ->get();
    }

    public function inviteUserToCollaborate(int $playlistId, PlaylistCollaborateRequest $request): void
    {
        $collaborator = User::query()
            ->where('uuid', $request->input('collaborator_uuid'))
            ->first();

        Collaborator::updateOrCreate([
            'user_id' => auth()->id(),
            'playlist_id' => $playlistId,
            'friend_id' => $collaborator->id,
            'approved' => DefaultConstants::FALSE,
        ]);

        $collaborator->notificationsTest()->create([
            'object_id' => $playlistId,
            'notificationable_id' => $playlistId,
            'notificationable_type' => Playlist::class,
            'action' => ActivityTypeEnum::inviteCollaborate,
            'hostable_id' => auth()->id(),
            'hostable_type' => User::class,
        ]);
    }

    public function responseToCollaborate(int $playlistId, User $owner, PlaylistCollaborationResponseRequest $request): bool
    {
        $collaborator = User::query()
            ->where('uuid', $request->input('collaborator_uuid'))
            ->first();

        $collaboration = Collaborator::query()
            ->where([
                'user_id' => $owner->id,
                'playlist_id' => $playlistId,
                'friend_id' => $collaborator->id,
            ])->first();

        if (!$collaboration) {
            return false;
        }

        $collaboration->update([
            'approved' => $request->input('response'),
        ]);

        Notification::query()
            ->where([
                'object_id' => $playlistId,
                'notificationable_id' => $playlistId,
                'notificationable_type' => Playlist::class,
                'action' => ActionConstants::INVITE_COLLAB,
                'hostable_id' => $owner->id,
                'hostable_type' => User::class,
            ])
            ->delete();

        if (!$request->input('response')) {
            $collaboration->delete();

            return false;
        }

        $owner->notificationsTest()->create([
            'object_id' => $playlistId,
            'notificationable_id' => $playlistId,
            'notificationable_type' => Playlist::class,
            'action' => ActionConstants::ACCEPTED_COLLAB,
            'hostable_id' => $collaborator->id,
            'hostable_type' => User::class,
        ]);

        return true;
    }

    public function isCollaborator(Playlist $playlist): bool
    {
        return $playlist->collaboration && Collaborator::query()
            ->where([
                'playlist_id' => $playlist->id,
                'friend_id' => auth()->id(),
                'approved' => DefaultConstants::TRUE,
            ])->exists();
    }
}
