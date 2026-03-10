<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\PeriodEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Genre\GenreShortResource;
use App\Http\Resources\Genre\GenreTrendingResource;
use App\Http\Resources\Post\PostShortResource;
use App\Http\Resources\Song\SongShortResource;
use App\Models\Adventure;
use App\Models\Genre;
use App\Models\Post;
use App\Models\Song;
use App\Services\FixedTrendingService;
use Inertia\Inertia;
use Inertia\Response;

class HomepageController extends Controller
{
    private FixedTrendingService $trendingService;

    public function __construct(FixedTrendingService $trendingService)
    {
        $this->trendingService = $trendingService;
    }

    public function __invoke(): Response
    {
        //        $freshAudios = Channel::query()
        //            ->where('allow_home', DefaultConstants::TRUE)
        //            ->where('alt_name', 'new-audios')
        //            ->with('songs')
        //            ->orderBy('priority')
        //            ->first() ?? [];

        $freshAudios = Song::query()
            ->visible()
            ->published()
            ->latest()
            ->limit(10)
            ->get();

        $femaleNew = Song::query()
            ->visible()
            ->published()
            ->female()
            ->limit(10)
            ->get();

        $maleNew = Song::query()
            ->visible()
            ->published()
            ->male()
            ->limit(10)
            ->get();

        $adventures = Adventure::query()
            ->heading()
            ->with(['tags', 'user', 'genres', 'roots.children'])
            ->latest()
            ->visible()
            ->limit(10)
            ->get();

        return Inertia::render('Index', [
            'newAudios' => [
                'fresh_audios' => SongShortResource::collection($freshAudios),
                'female_voice' => SongShortResource::collection($femaleNew),
                'male_voice' => SongShortResource::collection($maleNew),
            ],
            'popularAudios' => [
                'daily' => GenreTrendingResource::collection($this->trendingService->popularAudios(PeriodEnum::Daily)),
                'weekly' => GenreTrendingResource::collection($this->trendingService->popularAudios(PeriodEnum::Weekly)),
                'monthly' => GenreTrendingResource::collection($this->trendingService->popularAudios(PeriodEnum::Monthly)),
            ],
            'genres' => GenreShortResource::collection(Genre::query()
                ->orderBy('priority')
                ->discover()
                ->get()
            ),
            'posts' => PostShortResource::collection(Post::query()->visible()->approved()->latest()->limit(10)->get()),
            'adventures' => AdventureFullResource::collection($adventures),
        ]);
    }
}
