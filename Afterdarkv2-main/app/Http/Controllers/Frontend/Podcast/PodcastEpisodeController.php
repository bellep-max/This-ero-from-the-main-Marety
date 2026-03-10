<?php

namespace App\Http\Controllers\Frontend\Podcast;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Podcast\PodcastEpisodeUpdateRequest;
use App\Http\Resources\Podcast\PodcastEpisodeFullResource;
use App\Http\Resources\Podcast\PodcastEpisodeResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Episode;
use App\Services\ArtworkService;
use App\Services\NotificationService;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PodcastEpisodeController extends Controller
{
    public function __construct(
        private readonly ArtworkService $artworkService,
        private readonly NotificationService $notificationService,
    ) {}

    public function show(Episode $episode): Response
    {
        Gate::authorize('show', $episode);

        $episode->load('user')
            ->loadCount('loves');

        $this->notificationService->markAsRead($episode);

        return Inertia::render('Track/Show', [
            'track' => PodcastEpisodeResource::make($episode),
            'user' => UserProfileResource::make($episode->user),
        ]);
    }

    public function edit(Episode $episode): Response
    {
        Gate::authorize('edit', $episode);

        $episode->load('user', 'podcast:id,uuid')
            ->loadCount('loves');

        return Inertia::render('Podcast/Episode/Edit', [
            'episode' => PodcastEpisodeFullResource::make($episode),
            'user' => UserProfileResource::make($episode->user),
        ]);
    }

    public function update(Episode $episode, PodcastEpisodeUpdateRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $episode);

        $episode->update(Arr::except($request->validated(), ['file', 'artwork']));

        if ($request->hasFile('file')) {
            $uploadService = new UploadService;
            $uploadService->updateModelTrack($request, $episode);
        } elseif ($request->hasFile('artwork')) {
            $this->artworkService->updateArtwork($request, $episode);
        }

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Podcast episode updated successfully.',
        ]);

        return to_route('episodes.show', $episode);
    }
}
