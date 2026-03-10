<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Playlist;
use App\Models\User;

class PlaylistPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(User $user, Playlist $playlist): bool
    {
        if ($playlist->is_visible) {
            return true;
        }

        return $user->id === $playlist->user_id
            || ($playlist->collaboration && $playlist->approvedCollaborators()->where('friend_id', $user->id)->exists());
    }

    public function edit(User $user, Playlist $playlist): bool
    {
        return $user->id === $playlist->user_id;
    }

    public function destroy(User $user, Playlist $playlist): bool
    {
        return $user->id === $playlist->user_id;
    }

    public function addSong(User $user, Playlist $playlist): bool
    {
        return $user->id === $playlist->user_id
            || ($playlist->collaboration && $playlist->approvedCollaborators()->where('friend_id', $user->id)->exists());
    }
}
