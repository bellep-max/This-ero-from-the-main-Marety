<?php

namespace App\Http\Controllers\Api;

use App\Models\Activity;
use App\Models\Song;
use App\Models\User;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function tracks(User $user): JsonResponse
    {
        if (auth()->check() && auth()->id() == $user->id) {
            $user->setRelation('tracks', $user->tracks()->withoutGlobalScopes()->latest()->paginate(20));
        } else {
            $user->setRelation('tracks', $user->tracks()->latest()->paginate(20));
        }

        return response()->json([
            'profile' => $user,
        ]);
    }

    public function index(User $user): JsonResponse
    {
        $user->setRelation('activities', $user->activities()->latest()->paginate(10))
            ->setRelation('playlists', $user->playlists()->with('user')->paginate(10))
            ->setRelation('recent', $user->recent()->latest()->paginate(10));

        if ($this->request->get('callback')) {
            $user->setRelation('loved', $user->loved()->limit(50)->get());

            return response()->jsonp($this->request->get('callback'), $user->loved)->header('Content-Type', 'application/javascript');
        }

        return response()->json([
            'profile' => $user,
        ]);
    }

    public function recent(User $user): JsonResponse
    {
        return response()->json([
            'profile' => $user,
            'songs' => $user->recent()->latest()->paginate(20),
        ]);
    }

    public function feed(User $user): JsonResponse
    {
        $user->setRelation('feed', $user->feed()->latest()->paginate(20));

        return response()->json([
            'profile' => $user,
        ]);
    }

    public function posts(User $user): JsonResponse
    {
        $activity = Activity::query()->findOrFail($this->request->route('id'));

        if ($activity->user_id != $user->id) {
            abort(404);
        }

        return response()->json([
            'profile' => $user,
            'activity' => $activity,
        ]);
    }

    public function playlists(User $user): JsonResponse
    {
        return response()->json([
            'profile' => $user,
        ]);
    }

    public function collection(User $user): JsonResponse
    {
        $user->setRelation('collection', $user->collection()->paginate(20));

        return response()->json([
            'profile' => $user,
            'songs' => $user->collection,
        ]);
    }

    public function favorites(User $user): JsonResponse
    {
        $user->setRelation('loved', $user->loved()->paginate(20));

        return response()->json([
            'profile' => $user,
            'songs' => $user->loved,
        ]);
    }

    public function subscribed(User $user): JsonResponse
    {
        $user->setRelation('subscribed', $user->subscribed()->paginate(20));

        return response()->json([
            'profile' => $user,
            'subscribed' => $user->subscribed,
        ]);
    }

    public function followers(User $user): JsonResponse
    {
        return response()->json([
            'profile' => $user,
        ]);
    }

    public function following(User $user): JsonResponse
    {
        return response()->json([
            'profile' => $user,
            'following' => $user->following,
        ]);
    }

    public function notifications(User $user): JsonResponse
    {
        $user->setRelation('notifications', $user->notifications());

        return response()->json([
            'profile' => $user,
        ]);
    }

    public function now_playing(): JsonResponse
    {
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

    public function purchased(User $user): JsonResponse
    {
        $user->setRelation('purchased', $user->orders()->paginate(20));

        return response()->json([
            'profile' => $user,
        ]);
    }
}
