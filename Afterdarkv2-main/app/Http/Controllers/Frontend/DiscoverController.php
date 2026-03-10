<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Song\DiscoverSearchRequest;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Song\SongFullResource;
use App\Models\Adventure;
use App\Models\Song;
use App\Services\SearchService;
use Inertia\Inertia;
use Inertia\Response;

class DiscoverController extends Controller
{
    private const TAKE_AMOUNT = 20;

    public function __construct(private readonly SearchService $searchService) {}

    public function index(DiscoverSearchRequest $request): Response
    {
        $type = $request->get('type');

        $songs = $type === Song::class || !$type
            ? $this->searchService->search(Song::class, $request)
            : null;

        $adventures = $type === Adventure::class || !$type
            ? $this->searchService->search(Adventure::class, $request)
            : null;

        return Inertia::render('Discover', [
            'songs' => SongFullResource::collection($songs),
            'adventures' => AdventureFullResource::collection($adventures),
            'song_max_duration' => ceil(Song::query()->max('duration') / 60),
            'filters' => $request->validated(),
        ]);
    }
}
