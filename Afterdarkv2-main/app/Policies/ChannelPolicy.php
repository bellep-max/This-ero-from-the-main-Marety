<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Channel;
use App\Models\User;

class ChannelPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        return null;
    }

    public function show(?User $user, Channel $channel): bool
    {
        return $channel->is_visible;
    }
}
