<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Adventure;
use App\Models\User;

class AdventurePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(?User $user, Adventure $adventure): bool
    {
        if ($adventure->property->is_visible) {
            return true;
        }

        return $user?->id === $adventure->user_id;
    }

    public function edit(User $user, Adventure $adventure): bool
    {
        return $user->id === $adventure->user_id;
    }

    public function destroy(User $user, Adventure $adventure): bool
    {
        return $user->id === $adventure->user_id;
    }

    public function download(User $user, Adventure $adventure): bool
    {
        return $user->id === $adventure->user_id;

    }
}
