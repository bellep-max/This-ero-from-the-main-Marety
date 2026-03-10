<?php

namespace App\Services;

use App\Models\User;

class FollowService
{
    public function store(array $data)
    {
        $user = User::findByUuid($data['user_uuid']);

        return auth()->user()->following()->syncWithoutDetaching($user->id);
    }

    public function delete(array $data)
    {
        $user = User::findByUuid($data['user_uuid']);

        return auth()->user()->following()->detach($user->id);
    }
}
