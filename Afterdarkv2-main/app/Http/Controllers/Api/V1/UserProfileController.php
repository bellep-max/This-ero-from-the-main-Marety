<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\DefaultConstants;
use App\Http\Requests\Frontend\User\UserProfile\TrackSearchRequest;
use App\Http\Requests\Frontend\User\UserProfile\UserProfileUpdateRequest;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\Podcast\PodcastShortResource;
use App\Http\Resources\Song\SongFullResource;
use App\Http\Resources\User\UserProfileResource;
use App\Http\Resources\User\UserSubscriptionResource;
use App\Models\User;
use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Services\ArtworkService;
use App\Services\NotificationService;
use App\Services\UserService;
use App\Services\UserStatsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class UserProfileController extends ApiController
{
    public function __construct(
        private UserStatsService $userStatsService,
        private UserService $userService,
        private readonly NotificationService $notificationService,
    ) {}

    public function show(User $user): JsonResponse
    {
        $user->loadCount([
            'freeFollowers',
            'following',
            'activePatrons',
            'loves' => function ($query) {
                $query->has('loveable');
            },
        ]);

        $this->notificationService->markAsRead($user);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'recent' => SongFullResource::collection($this->userService->getUserRecentSongs($user, 20)),
        ]);
    }

    public function notifications(User $user): JsonResponse
    {
        $user->loadMissing([
            'unreadNotifications' => function ($query) {
                $query->with([
                    'notificationable',
                    'hostable',
                ])
                    ->orderBy('read_at', 'asc')
                    ->orderBy('created_at', 'desc');
            },
        ]);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'notifications' => NotificationResource::collection($user->unreadNotifications),
        ]);
    }

    public function purchased(User $user): JsonResponse
    {
        $user->loadMissing(['orders', 'userSubscriptions']);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'orders' => OrderResource::collection($user->orders),
            'subscriptions' => UserSubscriptionResource::collection($user->userSubscriptions),
        ]);
    }

    public function tracks(User $user, TrackSearchRequest $request): JsonResponse
    {
        $activeUserSubscription = auth()->user()->activeUserSubscription($user->id);

        $songs = $user
            ->tracks()
            ->latest()
            ->when($user->id === auth()->id(), function (Builder $query) {
                $query->withoutGlobalScopes([new VisibilityScope, new ApprovedScope, new PublishedScope]);
            })
            ->when(!$activeUserSubscription && $user->id !== auth()->id(), function (Builder $query) {
                $query->where('is_patron', false);
            })
            ->when($request->input('search'), function ($query) use ($request) {
                $query->whereFulltext('title', $request->input('search'));
            })
            ->get();

        $this->notificationService->markAsRead($user);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'tracks' => SongFullResource::collection(collect([...$songs, ...$user->collection()->get()])),
            'filters' => $request->validated(),
        ]);
    }

    public function adventures(User $user): JsonResponse
    {
        $user->loadMissing([
            'adventureHeaders' => function ($query) {
                $query->visible();
            },
            'adventureHeaders.tags',
            'adventureHeaders.user',
            'adventureHeaders.genres',
            'adventureHeaders.roots.children',
        ]);

        $this->notificationService->markAsRead($user);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'adventures' => AdventureFullResource::collection($user->adventureHeaders),
        ]);
    }

    public function podcasts(User $user): JsonResponse
    {
        $user->loadMissing(['podcasts']);

        $this->notificationService->markAsRead($user);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'podcasts' => PodcastShortResource::collection($user->podcasts),
        ]);
    }

    public function playlists(User $user): JsonResponse
    {
        $user->loadMissing(['playlists' => function ($query) use ($user) {
            if ($user->id !== auth()->id()) {
                $query->where('is_visible', DefaultConstants::TRUE);
            }
        }]);

        $this->notificationService->markAsRead($user);

        return $this->success([
            'user' => UserProfileResource::make($user),
            'playlists' => PlaylistShortResource::collection($user->playlists),
        ]);
    }

    public function update(UserProfileUpdateRequest $request): JsonResponse
    {
        $user = auth()->user();

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $user);
        }

        $user->update($request->validated());
        auth()->setUser($user);

        return $this->success($user);
    }
}
