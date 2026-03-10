<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Adventure\AdventureUpdateRequest;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Adventure;
use App\Services\AdventureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AdventureController extends Controller
{
    public function __construct(private readonly AdventureService $adventureService) {}

    public function show(Adventure $adventure): Response
    {
        Gate::authorize('show', $adventure);

        $adventure->load('property', 'roots.finals', 'user', 'tags', 'genres', 'comments')
            ->loadCount('loves');

        return Inertia::render('Adventures/Show', [
            'user' => UserProfileResource::make($adventure->user),
            'adventure' => AdventureFullResource::make($adventure),
            'comments' => CommentResource::collection($adventure->comments),
        ]);
    }

    public function edit(Adventure $adventure): Response
    {
        Gate::authorize('edit', $adventure);

        $adventure->load('property', 'roots.finals', 'user', 'tags', 'genres')
            ->loadCount('fans');

        return Inertia::render('Adventures/Edit', [
            'user' => UserProfileResource::make($adventure->user),
            'adventure' => AdventureFullResource::make($adventure),
        ]);
    }

    public function update(Adventure $adventure, AdventureUpdateRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $adventure);

        $this->adventureService->update($adventure, $request);

        return to_route('adventures.show', $adventure);
    }

    public function destroy(Adventure $adventure): RedirectResponse
    {
        Gate::authorize('destroy', $adventure);

        $this->adventureService->destroy($adventure);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Adventure deleted successfully.',
        ]);

        return to_route('users.adventures', $adventure->user);
    }
}
