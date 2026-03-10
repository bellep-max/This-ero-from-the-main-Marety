<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Episode;
use App\Models\User;

class PodcastEpisodePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(?User $user, Episode $episode): bool
    {
        if ($episode->is_visible && $episode->approved) {
            return true;
        }

        return $user?->id === $episode->user_id;
    }

    public function edit(User $user, Episode $episode): bool
    {
        return $user->id === $episode->user_id;
    }

    public function destroy(User $user, Episode $episode): bool
    {
        return $user->id === $episode->user_id;
    }

    public function download(User $user, Episode $episode): bool
    {
        if ($episode->user_id === $user->id) {
            return true;
        }

        return $episode->allow_download;
    }
}
