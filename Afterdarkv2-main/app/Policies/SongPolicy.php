<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Song;
use App\Models\User;

class SongPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(?User $user, Song $song): bool
    {
        if ($song->is_visible && $song->published_at->lessThanOrEqualTo(now())) {
            return true;
        }

        return $user?->id === $song->user_id;
    }

    public function edit(User $user, Song $song): bool
    {
        return $user->id === $song->user_id;
    }

    public function destroy(User $user, Song $song): bool
    {
        return $user->id === $song->user_id;
    }

    public function download(User $user, Song $song): bool
    {
        if ($song->user_id === $user->id) {
            return true;
        }

        return $song->allow_download;
    }
}
