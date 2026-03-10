<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\User\UserLinktree\UserLinktreeUpdateRequest;
use Illuminate\Http\JsonResponse;

class UserLinktreeController extends ApiController
{
    public function update(UserLinktreeUpdateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $user->update($request->validated());
        auth()->setUser($user);

        return $this->success($user, 'Linktree updated successfully');
    }

    public function destroy(): JsonResponse
    {
        $user = auth()->user();
        $user->update(['linktree_link' => null]);
        auth()->setUser($user);

        return $this->success($user, 'Linktree removed successfully');
    }
}
