<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Song\DiscoverSearchRequest;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Song\SongFullResource;
use App\Models\Adventure;
use App\Models\Song;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class DiscoverController extends ApiController
{
    public function __construct(private readonly SearchService $searchService) {}

    public function index(DiscoverSearchRequest $request): JsonResponse
    {
        $type = $request->get('type');

        $songs = $type === Song::class || !$type
            ? $this->searchService->search(Song::class, $request)
            : null;

        $adventures = $type === Adventure::class || !$type
            ? $this->searchService->search(Adventure::class, $request)
            : null;

        return $this->success([
            'songs' => SongFullResource::collection($songs),
            'adventures' => AdventureFullResource::collection($adventures),
            'song_max_duration' => ceil(Song::query()->max('duration') / 60),
            'filters' => $request->validated(),
        ]);
    }
}
