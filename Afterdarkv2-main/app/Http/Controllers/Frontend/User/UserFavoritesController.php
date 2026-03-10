<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\Favorite\UserFavoriteUpdateRequest;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Adventure;
use App\Models\Episode;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserFavoritesController extends Controller
{
    public function index(User $user): Response
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

        return Inertia::render('User/Favorites', [
            'user' => UserProfileResource::make($user),
            'adventures' => $mapFavorites(Adventure::class),
            'songs' => $mapFavorites(Song::class),
            'podcasts' => $mapFavorites(Podcast::class),
            'episodes' => $mapFavorites(Episode::class),
            'playlists' => $mapFavorites(Playlist::class),
            'users' => $mapFavorites(User::class),
        ]);
    }

    public function store(User $user, UserFavoriteUpdateRequest $request): RedirectResponse
    {
        $likedModelId = $request->input('type')::query()
            ->whereUuid($request->input('uuid'))
            ->value('id');

        if (!$likedModelId) {
            session()->flash('message', [
                'level' => 'danger',
                'content' => "Couldn't find an item to like",
            ]);

            return redirect()->back(303);
        }

        $user->loves()->updateOrCreate([
            'loveable_id' => $likedModelId,
            'loveable_type' => $request->input('type'),
        ]);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Added to favorites',
        ]);

        return redirect()->back(303);
    }

    public function destroy(User $user, UserFavoriteUpdateRequest $request): RedirectResponse
    {
        $likedModelId = $request->input('type')::query()
            ->whereUuid($request->input('uuid'))
            ->value('id');

        if (!$likedModelId) {
            session()->flash('message', [
                'level' => 'danger',
                'content' => "Couldn't find an item to like",
            ]);

            return redirect()->back(303);
        }

        $user->loves()->where([
            'loveable_id' => $likedModelId,
            'loveable_type' => $request->input('type'),
        ])->delete();

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Removed from favorites',
        ]);

        return redirect()->back(303);
    }
}
