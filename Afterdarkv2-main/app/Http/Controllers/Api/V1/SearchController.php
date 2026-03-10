<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\DefaultConstants;
use App\Http\Requests\Frontend\SearchRequest;
use App\Http\Resources\Song\SongFullResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class SearchController extends ApiController
{
    private const TAKE_AMOUNT = 20;

    public function show(SearchRequest $request): JsonResponse
    {
        $searchString = "%{$request->input('search')}%";

        $songs = Song::query()
            ->where('title', 'LIKE', $searchString)
            ->where('is_visible', DefaultConstants::TRUE)
            ->has('user')
            ->with(['user:id,uuid,name,username', 'tags', 'genres'])
            ->paginate(self::TAKE_AMOUNT);

        $users = User::query()
            ->where('name', 'LIKE', $searchString)
            ->get();

        return $this->success([
            'searchString' => $request->input('search'),
            'songs' => SongFullResource::collection($songs->items()),
            'users' => UserShortResource::collection($users),
            'pagination' => Arr::except($songs->toArray(), 'items'),
        ]);
    }
}
