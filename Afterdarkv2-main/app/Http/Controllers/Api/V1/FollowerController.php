<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Follow\FollowerUpdateRequest;
use App\Http\Resources\User\UserFollowerResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowerController extends ApiController
{
    public function __construct(private FollowService $service) {}

    public function show(User $user): JsonResponse
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

        return $this->success([
            'user' => UserProfileResource::make($user),
            'patrons' => UserFollowerResource::collection($user->activePatrons),
            'free_followers' => UserFollowerResource::collection($user->freeFollowers),
        ]);
    }

    public function store(FollowerUpdateRequest $request): JsonResponse
    {
        $this->service->store($request->validated());

        return $this->success(null, 'Followed successfully');
    }

    public function destroy(FollowerUpdateRequest $request): JsonResponse
    {
        return $this->success(null, 'Unfollowed successfully');
    }
}
