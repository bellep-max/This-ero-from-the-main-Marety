<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Podcast;
use App\Models\User;

class PodcastPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(?User $user, Podcast $podcast): bool
    {
        if ($podcast->is_visible && $podcast->approved) {
            return true;
        }

        return $user?->id === $podcast->user_id;
    }

    public function edit(User $user, Podcast $podcast): bool
    {
        return $user->id === $podcast->user_id;
    }

    public function destroy(User $user, Podcast $podcast): bool
    {
        return $user->id === $podcast->user_id;
    }
}
