<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\DefaultConstants;
use App\Http\Requests\Backend\Podcast\PodcastUpdateRequest;
use App\Http\Requests\Frontend\Podcast\PodcastSearchRequest;
use App\Http\Requests\Frontend\Podcast\PodcastStoreRequest;
use App\Http\Resources\OptionResource;
use App\Http\Resources\Podcast\PodcastEditResource;
use App\Http\Resources\Podcast\PodcastEpisodeFullResource;
use App\Http\Resources\Podcast\PodcastEpisodeResource;
use App\Http\Resources\Podcast\PodcastFullResource;
use App\Http\Resources\Podcast\PodcastShortResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Channel;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Region;
use App\Models\Slide;
use App\Services\ArtworkService;
use App\Services\NotificationService;
use App\Services\PodcastService;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class PodcastController extends ApiController
{
    private const TAKE_AMOUNT = 20;

    public function __construct(
        private readonly PodcastService $podcastService,
        private readonly NotificationService $notificationService,
    ) {}

    public function index(PodcastSearchRequest $request): JsonResponse
    {
        $podcasts = Podcast::query()
            ->when($request->filled('categories'), function ($query) use ($request) {
                $query->whereIn('category', $request->input('categories'));
            })
            ->when($request->filled('languages'), function ($query) use ($request) {
                $query->whereIn('language_id', $request->input('languages'));
            })
            ->when($request->filled('countries'), function ($query) use ($request) {
                $query->whereHas('country', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('countries'));
                });
            })
            ->when($request->filled('tags'), function ($query) use ($request) {
                $query->whereHas('tags', function ($query) use ($request) {
                    $query->whereIn('tags.id', $request->input('tags'));
                });
            })
            ->has('episodes')
            ->where(function ($query) {
                $query->where('is_visible', DefaultConstants::TRUE)
                    ->when(auth()->check(), function ($query) {
                        $query->orWhere('user_id', auth()->id());
                    });
            })
            ->withMax('episodes', 'season')
            ->withCount('episodes')
            ->paginate(self::TAKE_AMOUNT);

        return $this->success([
            'podcasts' => PodcastShortResource::collection($podcasts),
            'regions' => OptionResource::collection(Region::all()),
            'channels' => Channel::query()
                ->where('allow_podcasts', DefaultConstants::TRUE)
                ->orderBy('priority', 'asc')
                ->get(),
            'slides' => Slide::query()
                ->where('allow_podcasts', DefaultConstants::TRUE)
                ->orderBy('priority', 'asc')
                ->get(),
            'filters' => $request->validated(),
        ]);
    }

    public function store(PodcastStoreRequest $request): JsonResponse
    {
        $podcast = $this->podcastService->store($request);

        if (!$podcast) {
            return $this->error('Failed to create podcast.');
        }

        return $this->success(PodcastShortResource::make($podcast), 'Podcast created successfully', 201);
    }

    public function show(Podcast $podcast): JsonResponse
    {
        Gate::authorize('show', $podcast);

        $podcast->loadMissing(['user']);

        return $this->success([
            'podcast' => PodcastShortResource::make($podcast),
        ]);
    }

    public function edit(Podcast $podcast): JsonResponse
    {
        Gate::authorize('edit', $podcast);

        $podcast->loadMissing(['user', 'tags', 'categories']);

        return $this->success([
            'podcast' => PodcastEditResource::make($podcast),
        ]);
    }

    public function update(Podcast $podcast, PodcastUpdateRequest $request): JsonResponse
    {
        $this->podcastService->update($request, $podcast);

        return $this->success(PodcastShortResource::make($podcast->fresh()), 'Podcast updated successfully');
    }

    public function showEpisode(Episode $episode): JsonResponse
    {
        Gate::authorize('show', $episode);

        $episode->load('user')
            ->loadCount('loves');

        $this->notificationService->markAsRead($episode);

        return $this->success([
            'track' => \App\Http\Resources\Podcast\PodcastEpisodeResource::make($episode),
            'user' => UserProfileResource::make($episode->user),
        ]);
    }

    public function editEpisode(Episode $episode): JsonResponse
    {
        Gate::authorize('edit', $episode);

        $episode->load('user', 'podcast:id,uuid')
            ->loadCount('loves');

        return $this->success([
            'episode' => PodcastEpisodeFullResource::make($episode),
            'user' => UserProfileResource::make($episode->user),
        ]);
    }

    public function updateEpisode(Episode $episode, \App\Http\Requests\Frontend\Podcast\PodcastEpisodeUpdateRequest $request): JsonResponse
    {
        Gate::authorize('edit', $episode);

        $episode->update(Arr::except($request->validated(), ['file', 'artwork']));

        if ($request->hasFile('file')) {
            $uploadService = new UploadService;
            $uploadService->updateModelTrack($request, $episode);
        } elseif ($request->hasFile('artwork')) {
            (new ArtworkService)->updateArtwork($request, $episode);
        }

        return $this->success(PodcastEpisodeFullResource::make($episode->fresh()), 'Podcast episode updated successfully');
    }

    public function showSeason(Podcast $podcast, int $season): JsonResponse
    {
        Gate::authorize('show', $podcast);

        $podcast
            ->loadMissing([
                'followers:id,uuid,name,username',
                'user:id,uuid,name,username,linktree_link',
                'episodes' => function ($query) use ($season, $podcast) {
                    $query->where('season', $season)
                        ->when(auth()->id() !== $podcast->user_id, function ($query) {
                            $query->where('is_visible', DefaultConstants::TRUE);
                        });
                },
            ])
            ->loadCount([
                'followers',
                'loves',
            ])
            ->loadSum('episodes', 'duration', function ($query) use ($podcast) {
                $query->when(auth()->id() !== $podcast->user_id, function ($query) {
                    $query->where('is_visible', DefaultConstants::TRUE);
                });
            });

        return $this->success([
            'podcast' => PodcastFullResource::make($podcast),
            'episodes' => PodcastEpisodeResource::collection($podcast->episodes),
            'season' => $season,
        ]);
    }
}
