<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Follow\FollowerUpdateRequest;
use App\Http\Resources\User\UserFollowerResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowingController extends ApiController
{
    public function __construct(private FollowService $service) {}

    public function index(User $user): JsonResponse
    {
        $user->load(['following']);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'following' => UserFollowerResource::collection($user->following),
        ]);
    }

    public function store(FollowerUpdateRequest $request): JsonResponse
    {
        $this->service->store($request->validated());

        return $this->success(null, 'Following successfully');
    }

    public function destroy(FollowerUpdateRequest $request): JsonResponse
    {
        $this->service->delete($request->validated());

        return $this->success(null, 'Unfollowed successfully');
    }
}
