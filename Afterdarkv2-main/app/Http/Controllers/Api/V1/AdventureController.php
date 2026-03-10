<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Adventure\AdventureUpdateRequest;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Adventure;
use App\Services\AdventureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AdventureController extends ApiController
{
    public function __construct(private readonly AdventureService $adventureService) {}

    public function show(Adventure $adventure): JsonResponse
    {
        Gate::authorize('show', $adventure);

        $adventure->load('property', 'roots.finals', 'user', 'tags', 'genres', 'comments')
            ->loadCount('loves');

        return $this->success([
            'user' => UserProfileResource::make($adventure->user),
            'adventure' => AdventureFullResource::make($adventure),
            'comments' => CommentResource::collection($adventure->comments),
        ]);
    }

    public function edit(Adventure $adventure): JsonResponse
    {
        Gate::authorize('edit', $adventure);

        $adventure->load('property', 'roots.finals', 'user', 'tags', 'genres')
            ->loadCount('fans');

        return $this->success([
            'user' => UserProfileResource::make($adventure->user),
            'adventure' => AdventureFullResource::make($adventure),
        ]);
    }

    public function update(Adventure $adventure, AdventureUpdateRequest $request): JsonResponse
    {
        Gate::authorize('edit', $adventure);

        $this->adventureService->update($adventure, $request);

        return $this->success(null, 'Adventure updated successfully.');
    }

    public function destroy(Adventure $adventure): JsonResponse
    {
        Gate::authorize('destroy', $adventure);

        $this->adventureService->destroy($adventure);

        return $this->success(null, 'Adventure deleted successfully.');
    }
}
