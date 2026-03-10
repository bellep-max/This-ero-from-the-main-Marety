<?php

namespace App\Http\Controllers\Frontend\Podcast;

use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Podcast\PodcastEpisodeResource;
use App\Http\Resources\Podcast\PodcastFullResource;
use App\Models\Podcast;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PodcastSeasonController extends Controller
{
    public function show(Podcast $podcast, int $season): Response
    {
        Gate::authorize('show', $podcast);

        $podcast
            ->loadMissing([
                'followers:id,uuid,name,username',
                'user:id,uuid,name,username,linktree_link',
                'episodes' => function ($query) use ($season, $podcast) {
                    $query->where('season', $season)
                        ->when(auth()->id() !== $podcast->user_id, function ($query) {
                            $query->where('is_visible', DefaultConstants::TRUE);
                        });
                },
            ])
            ->loadCount([
                'followers',
                'loves',
            ])
            ->loadSum('episodes', 'duration', function ($query) use ($podcast) {
                $query->when(auth()->id() !== $podcast->user_id, function ($query) {
                    $query->where('is_visible', DefaultConstants::TRUE);
                });
            });

        return Inertia::render('Podcast/Season/Show', [
            'podcast' => PodcastFullResource::make($podcast),
            'episodes' => PodcastEpisodeResource::collection($podcast->episodes),
            'season' => $season,
        ]);
    }
}
