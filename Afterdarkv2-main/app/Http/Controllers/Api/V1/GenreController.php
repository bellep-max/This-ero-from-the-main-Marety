<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Genre\GenreShortResource;
use App\Http\Resources\Song\SongFullResource;
use App\Models\Genre;
use App\Services\MetatagService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class GenreController extends ApiController
{
    private const TAKE_AMOUNT = 20;

    public function index(): JsonResponse
    {
        return $this->success([
            'genres' => GenreShortResource::collection(Genre::query()
                ->orderBy('priority', 'asc')
                ->discover()
                ->get()),
        ]);
    }

    public function show(Genre $genre): JsonResponse
    {
        $userIds = $genre
            ->songs()
            ->pluck('user_id');

        if (auth()->check()) {
            $patronUsers = collect($userIds)->unique()->filter(function (int $userId) {
                return (bool) auth()->user()->activeUserSubscription($userId);
            })->toArray();
        } else {
            $patronUsers = collect($userIds)->unique();
        }

        $songs = $genre
            ->songs()
            ->visible()
            ->when(auth()->check(), function ($query) use ($patronUsers) {
                $query->orWhere(function (Builder $query) use ($patronUsers) {
                    $query->where('is_patron', true)
                        ->whereIn('user_id', $patronUsers);
                });
            },
                function ($query) {
                    $query->where('is_patron', false);
                })
            ->with([
                'user:id,uuid,name,username',
                'tags',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(self::TAKE_AMOUNT);

        $adventures = $genre
            ->adventures()
            ->visible()
            ->with([
                'user:id,uuid,name,username',
                'tags',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(self::TAKE_AMOUNT);

        return $this->success([
            'genre' => GenreShortResource::make($genre),
            'songs' => SongFullResource::collection($songs),
            'adventures' => AdventureFullResource::collection($adventures),
            'related' => GenreShortResource::collection(Genre::query()->whereNot('id', $genre->id)->get()),
        ]);
    }
}
