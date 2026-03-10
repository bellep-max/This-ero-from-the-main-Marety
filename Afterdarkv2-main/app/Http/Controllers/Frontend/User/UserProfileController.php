<?php

namespace App\Http\Controllers\Frontend\User;

use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function __construct(
        private UserStatsService $userStatsService,
        private UserService $userService,
        private readonly NotificationService $notificationService,
    ) {}

    public function show(User $user): Response
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

        return Inertia::render('User/Show', [
            'user' => UserProfileResource::make($user),
            'recent' => SongFullResource::collection($this->userService->getUserRecentSongs($user, 20)),
        ]);
    }

    public function notifications(User $user): Response
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

        return Inertia::render('User/Notifications', [
            'user' => UserProfileResource::make($user),
            //            'recent_activities' => ActivityResource::collection($this->userService->getUserRecentActivities($user)),
            'notifications' => NotificationResource::collection($user->unreadNotifications),
        ]);

        //        getMetatags($user);
    }

    public function purchased(User $user): Response
    {
        //        getMetatags($user);

        $user->loadMissing(['orders', 'userSubscriptions']);

        return Inertia::render('User/Purchased', [
            'user' => UserProfileResource::make($user),
            'orders' => OrderResource::collection($user->orders),
            'subscriptions' => UserSubscriptionResource::collection($user->userSubscriptions),
        ]);
    }

    public function tracks(User $user, TrackSearchRequest $request): Response
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

        return Inertia::render('User/Tracks', [
            'user' => UserProfileResource::make($user),
            'tracks' => SongFullResource::collection(collect([...$songs, ...$user->collection()->get()])),
            'filters' => $request->validated(),
        ]);
    }

    public function adventures(User $user): Response
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

        return Inertia::render('User/Adventures', [
            'user' => UserProfileResource::make($user),
            'adventures' => AdventureFullResource::collection($user->adventureHeaders),
        ]);
    }

    public function podcasts(User $user): Response
    {
        $user->loadMissing(['podcasts']);

        $this->notificationService->markAsRead($user);

        return Inertia::render('User/Podcasts', [
            'user' => UserProfileResource::make($user),
            'podcasts' => PodcastShortResource::collection($user->podcasts),
        ]);
    }

    public function playlists(User $user): Response
    {
        $user->loadMissing(['playlists' => function ($query) use ($user) {
            if ($user->id !== auth()->id()) {
                $query->where('is_visible', DefaultConstants::TRUE);
            }
        }]);

        $this->notificationService->markAsRead($user);

        return Inertia::render('User/Playlists', [
            'user' => UserProfileResource::make($user),
            'playlists' => PlaylistShortResource::collection($user->playlists),
        ]);
    }

    public function albums(User $user, Request $request)
    {
        $ownProfile = $this->userStatsService->ownProfile($user);

        if (!$ownProfile) {
            abort(404);
        }

        $view = View::make('profile.new.albums')
            ->with([
                'profile' => $user,
                'stats' => $this->userStatsService->getProfileStats($user, $ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function update(UserProfileUpdateRequest $request): JsonResponse
    {
        $user = auth()->user();

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $user);
            $request->session()->put('message', 'Your avatar image was updated');
        }

        $user->update($request->validated());

        auth()->setUser($user);

        return response()->json($request->user());
    }
}
