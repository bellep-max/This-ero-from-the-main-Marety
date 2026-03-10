<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Services\AjaxViewService;
use App\Services\UserStatsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use View;

class NewProfileController extends Controller
{
    private User $profile;

    private bool $ownProfile = false;

    private Request $request;

    private UserStatsService $service;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, UserStatsService $service, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->service = $service;
        $this->ajaxViewService = $ajaxViewService;
    }

    private function getProfile()
    {
        $this->profile = $this->request->is('api*')
            ? User::query()->findOrFail($this->request->route('id'))
            : User::query()->where('username', $this->request->route('username'))->firstOrFail();

        $this->ownProfile = $this->profile->username === auth()->user()->username;
    }

    public function show(User $user)
    {
        dd($user);
        $this->getProfile();
        $this->profile->setRelation('recent', $this->profile->recent()->latest()->limit(10)->get());

        $view = View::make('profile.new.index')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function favorites()
    {
        $this->getProfile();

        if (!$this->ownProfile) {
            abort(404);
        }

        $view = View::make('profile.new.favorites')
            ->with([
                'profile' => $this->profile->load('lovedSongs'),
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function tracks()
    {
        $this->getProfile();
        $activeUserSubscription = auth()->user()->activeUserSubscription($this->profile->id);

        $songs = $this->profile
            ->tracks()
            ->latest()
            ->when($this->profile->id === auth()->id(), function (Builder $query) {
                $query->withoutGlobalScopes([new VisibilityScope, new ApprovedScope, new PublishedScope]);
            })
            ->when(!$activeUserSubscription && $this->profile->id !== auth()->id(), function (Builder $query) {
                $query->where('is_patron', false);
            })
            ->get();

        $collectionSongs = $this->profile->collection()->get();
        $this->profile->setRelation('tracks', collect([...$songs, ...$collectionSongs]));

        $view = View::make('profile.new.tracks')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function albums()
    {
        $this->getProfile();

        if (!$this->ownProfile) {
            abort(404);
        }

        $view = View::make('profile.new.albums')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function playlists()
    {
        $this->getProfile();
        $this->profile->setRelation('activities', $this->profile->activities()->latest()->paginate(10))
            ->setRelation('playlists', $this->profile->playlists()->with(['songsRelation' => function ($query) {
                $query->latest()->limit(4);
            }, 'user'])->get())
            ->setRelation('recent', $this->profile->recent()->latest()->paginate(10));

        $view = View::make('profile.new.playlists')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function followers()
    {
        $this->getProfile();

        if (!$this->ownProfile) {
            abort(404);
        }

        $paidUsers = $this->request->user()
            ->userSubscriptions('subscribed_user_id')
            ->whereIn('status', ['active', 'suspended'])
            ->pluck('user_id');

        $view = View::make('profile.new.followers')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
                'allFollowers' => $this->request->user()->followers()->get(),
                'paidFollowers' => $this->request->user()->followers()->whereIn('user_id', $paidUsers)->get(),
                'freeFollowers' => $this->request->user()->followers()->whereNotIn('user_id', $paidUsers)->get(),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function following()
    {
        $this->getProfile();
        abort_if(!$this->ownProfile, 404);

        $view = View::make('profile.new.following')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function adventures()
    {
        $this->getProfile();

        $view = View::make('profile.new.adventures')
            ->with([
                'profile' => $this->profile,
                'stats' => $this->service->getProfileStats($this->profile, $this->ownProfile),
                'canEdit' => $this->ownProfile,
                //                'hasUploadAccess' => $this->service->canUpload($this->profile),
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }
}
