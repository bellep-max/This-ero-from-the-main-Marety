<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Song;
use App\Models\User;
use App\Services\AjaxViewService;
use DB;
use Illuminate\Http\Request;
use View;

class ProfileController extends Controller
{
    private Request $request;

    private User $profile;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    private function getProfile()
    {
        $this->profile = $this->request->is('api*')
            ? User::query()->findOrFail($this->request->route('id'))
            : User::query()->where('username', $this->request->route('username'))->firstOrFail();
    }

    public function tracks()
    {
        $this->getProfile();

        if (auth()->check() && auth()->id() == $this->profile->id) {
            $this->profile->setRelation('tracks', $this->profile->tracks()->withoutGlobalScopes()->latest()->paginate(20));
        } else {
            $this->profile->setRelation('tracks', $this->profile->tracks()->latest()->paginate(20));
        }

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.tracks')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function index()
    {
        $this->getProfile();

        $this->profile->setRelation('activities', $this->profile->activities()->latest()->paginate(10))
            ->setRelation('playlists', $this->profile->playlists()->with('user')->paginate(10))
            ->setRelation('recent', $this->profile->recent()->latest()->paginate(10));

        if ($this->request->is('api*')) {
            if ($this->request->get('callback')) {
                $this->profile->setRelation('loved', $this->profile->loved()->limit(50)->get());

                return response()->jsonp($this->request->get('callback'), $this->profile->loved)->header('Content-Type', 'application/javascript');
            }

            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.index')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function recent()
    {
        $this->getProfile();

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'songs' => $this->profile->recent()->latest()->paginate(20),
            ]);
        }
    }

    public function feed()
    {
        $this->getProfile();

        $this->profile->setRelation('feed', $this->profile->feed()->latest()->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.feed')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function posts()
    {
        $this->getProfile();

        $activity = Activity::query()->findOrFail($this->request->route('id'));

        if ($activity->user_id != $this->profile->id) {
            abort(404);
        }

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'activity' => $activity,
            ]);
        }

        $view = View::make('profile.posts')
            ->with([
                'profile' => $this->profile,
                'activity' => $activity,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function playlists()
    {
        $this->getProfile();

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.playlists')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function collection()
    {
        $this->getProfile();

        $this->profile->setRelation('collection', $this->profile->collection()->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'songs' => $this->profile->collection,
            ]);
        }

        $view = View::make('profile.collection')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function favorites()
    {
        $this->getProfile();

        $this->profile->setRelation('loved', $this->profile->loved()->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'songs' => $this->profile->loved,
            ]);
        }

        $view = View::make('profile.favorites')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function subscribed()
    {
        $this->getProfile();

        $this->profile->setRelation('subscribed', $this->profile->subscribed()->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'subscribed' => $this->profile->subscribed,
            ]);
        }

        $view = View::make('profile.subscribed')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function followers()
    {
        $this->getProfile();

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.followers')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function following()
    {
        $this->getProfile();

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
                'following' => $this->profile->following,
            ]);
        }

        $view = View::make('profile.following')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function now_playing()
    {
        $this->getProfile();

        if ($this->request->is('api*') || $this->request->isMethod('post')) {
            $currentSong = Song::find($this->request->input('currentId'));
            $queueIds = $this->request->input('queueIds');
            // remove empty values in array
            $queueIds = array_filter($queueIds, 'strlen');
            // safe sql by remove set integer for all song id key
            $queueIds = array_filter($queueIds, function ($a) {
                return intval($a);
            });

            $queueSongs = Song::query()
                ->whereIn('id', $queueIds)
                ->orderBy(DB::raw('FIELD(id, ' . implode(',', $queueIds) . ')', 'FIELD'))
                ->get();

            return response()->json([
                'success' => true,
                'currentSong' => $currentSong,
                'queueSongs' => $queueSongs,
            ]);
        }

        $this->profile->suggest = Song::query()
            ->where('plays', '>', 0)
            ->limit(10)
            ->get();

        $view = View::make('profile.now_playing')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($this->profile);

        return $view;
    }

    public function purchased()
    {
        $this->getProfile();

        $this->profile->setRelation('purchased', Order::where('user_id', $this->profile->id)->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'profile' => $this->profile,
            ]);
        }

        $view = View::make('profile.purchased')
            ->with([
                'profile' => $this->profile,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request);
        }

        getMetatags($this->profile);

        return $view;
    }
}
