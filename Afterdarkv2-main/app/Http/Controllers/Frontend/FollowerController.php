<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Follow\FollowerUpdateRequest;
use App\Http\Resources\User\UserFollowerResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\User;
use App\Services\FollowService;
use Inertia\Inertia;
use Inertia\Response;

class FollowerController extends Controller
{
    public function __construct(private FollowService $service) {}

    public function show(User $user): Response
    {
        $user->load([
            'freeFollowers' => function ($query) {
                $query->withCount([
                    'tracks',
                    'adventureHeaders',
                    'myPlaylists',
                ]);
            },
            'activePatrons' => function ($query) {
                $query->withCount([
                    'tracks',
                    'adventureHeaders',
                    'myPlaylists',
                ]);
            },
        ])->loadCount([
            'freeFollowers',
            'activePatrons',
        ]);

        return Inertia::render('User/Followers', [
            'user' => UserProfileResource::make($user),
            'patrons' => UserFollowerResource::collection($user->activePatrons),
            'free_followers' => UserFollowerResource::collection($user->freeFollowers),
        ]);
    }

    public function store(FollowerUpdateRequest $request): void
    {
        $this->service->store($request->validated());
    }

    public function destroy(FollowerUpdateRequest $request) {}
}
