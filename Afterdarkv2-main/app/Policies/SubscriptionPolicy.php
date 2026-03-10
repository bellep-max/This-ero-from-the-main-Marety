<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class SubscriptionPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function edit(User $user): bool
    {
        return $user->hasAnyRole([RoleEnum::Creator, RoleEnum::Admin]);
    }
}
