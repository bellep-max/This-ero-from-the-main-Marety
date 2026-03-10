<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Follow\FollowerUpdateRequest;
use App\Http\Resources\User\UserFollowerResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FollowingController extends Controller
{
    public function __construct(private FollowService $service) {}

    public function index(User $user): Response
    {
        $user->load([
            'following',
        ]);

        return Inertia::render('User/Following', [
            'user' => UserProfileResource::make($user),
            'following' => UserFollowerResource::collection($user->following),
        ]);
    }

    public function store(FollowerUpdateRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->back()->with([
            'success' => true,
        ]);
    }

    public function destroy(FollowerUpdateRequest $request): RedirectResponse
    {
        $this->service->delete($request->validated());

        return redirect()->back(303)->with([
            'success' => true,
        ]);
    }
}
