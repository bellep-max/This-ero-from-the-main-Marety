<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\User\Favorite\UserFavoriteUpdateRequest;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Adventure;
use App\Models\Episode;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class FavoriteController extends ApiController
{
    public function index(User $user): JsonResponse
    {
        $favorites = $user
            ->loves()
            ->has('loveable')
            ->with('loveable')
            ->get()
            ->groupBy('loveable_type');

        $mapFavorites = fn ($type) => FavoriteResource::collection(
            $favorites->has($type) ? $favorites->pull($type) : []
        );

        return $this->success([
            'user' => UserProfileResource::make($user),
            'adventures' => $mapFavorites(Adventure::class),
            'songs' => $mapFavorites(Song::class),
            'podcasts' => $mapFavorites(Podcast::class),
            'episodes' => $mapFavorites(Episode::class),
            'playlists' => $mapFavorites(Playlist::class),
            'users' => $mapFavorites(User::class),
        ]);
    }

    public function store(User $user, UserFavoriteUpdateRequest $request): JsonResponse
    {
        $likedModelId = $request->input('type')::query()
            ->whereUuid($request->input('uuid'))
            ->value('id');

        if (!$likedModelId) {
            return $this->error("Couldn't find an item to like", 404);
        }

        $user->loves()->updateOrCreate([
            'loveable_id' => $likedModelId,
            'loveable_type' => $request->input('type'),
        ]);

        return $this->success(null, 'Added to favorites');
    }

    public function destroy(User $user, UserFavoriteUpdateRequest $request): JsonResponse
    {
        $likedModelId = $request->input('type')::query()
            ->whereUuid($request->input('uuid'))
            ->value('id');

        if (!$likedModelId) {
            return $this->error("Couldn't find an item to unlike", 404);
        }

        $user->loves()->where([
            'loveable_id' => $likedModelId,
            'loveable_type' => $request->input('type'),
        ])->delete();

        return $this->success(null, 'Removed from favorites');
    }
}
