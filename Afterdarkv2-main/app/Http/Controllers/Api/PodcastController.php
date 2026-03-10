<?php

namespace App\Http\Controllers\Api;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PodcastController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show(Podcast $podcast): JsonResponse
    {
        $podcast->setRelation('episodes', $podcast->episodes()->withoutGlobalScopes()->get());

        if ($this->request->get('callback')) {
            foreach ($podcast->episodes as $episode) {
                $episode->artists = [['name' => $episode->podcast->title]];
                $episode->artwork = $episode->podcast->artwork;
            }

            return response()
                ->jsonp($this->request->get('callback'), $podcast->episodes)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($podcast);
    }

    public function subscribers(Podcast $podcast): JsonResponse
    {
        return response()->json($podcast->followers);
    }

    public function episode(Episode $episode): JsonResponse
    {
        $episode->load('podcast');

        if ($this->request->get('callback')) {
            $episode->artists = [['name' => $episode->podcast->title]];
            $episode->artwork = $episode->podcast->artwork;

            return response()
                ->jsonp($this->request->get('callback'), [$episode])
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($episode);
    }

    public function seasons(Podcast $podcast): JsonResponse
    {
        //        $episode->load('podcast');
        //
        //        if ($this->request->get('callback')) {
        //            $episode->artists = [['name' => $episode->podcast->title]];
        //            $episode->artwork = $episode->podcast->artwork;
        //
        //            return response()
        //                ->jsonp($this->request->get('callback'), [$episode])
        //                ->header('Content-Type', 'application/javascript');
        //        }

        return response()->json(['seasons' => $podcast->seasons]);
    }

    public function episodes(Podcast $podcast, int $season): JsonResponse
    {
        $podcast->load('episodes');

        //        $episode->load('podcast');
        //
        //        if ($this->request->get('callback')) {
        //            $episode->artists = [['name' => $episode->podcast->title]];
        //            $episode->artwork = $episode->podcast->artwork;
        //
        //            return response()
        //                ->jsonp($this->request->get('callback'), [$episode])
        //                ->header('Content-Type', 'application/javascript');
        //        }

        return response()->json(['episodes' => $podcast->episodes()->where('season', $season)->max('number') ?? 0]);
    }
}
