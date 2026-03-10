<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-28
 * Time: 15:13.
 */

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\GetArtistDetails;
use App\Models\Artist;
use App\Services\AjaxViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ArtistController extends Controller
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function show(Artist $artist, Request $request): JsonResponse
    {
        if (config('settings.automate')) {
            GetArtistDetails::dispatch($artist);
            sleep(1);
        }

        $artist->setRelation('albums', $artist->albums()->latest()->limit(4)->get());
        $artist->setRelation('songs', $artist->songs()->get());
        $artist->setRelation('podcasts', $artist->podcasts()->get());

        if ($request->get('callback')) {
            return response()
                ->jsonp($request->get('callback'), $artist->songs)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json([
            'artist' => $artist,
            'songs' => $artist->songs,
            'podcasts' => $artist->podcasts,
        ]);
    }

    public function albums(Artist $artist, Request $request): \Illuminate\Contracts\View\View|string|null
    {
        $artist->setRelation('albums', $artist->albums()->paginate(20));

        $view = View::make('artist.albums')
            ->with([
                'artist' => $artist,
            ]);

        if ($request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $request);
        }

        Helper::getMetatags($artist);

        return $view;
    }

    public function podcasts(Artist $artist, Request $request): \Illuminate\Contracts\View\View
    {
        $artist->setRelation('podcasts', $artist->podcasts()->paginate(20));

        $view = View::make('artist.podcasts')
            ->with([
                'artist' => $artist,
            ]);

        if ($request->ajax()) {
            $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags($artist);

        return $view;
    }

    public function similar(Artist $artist, Request $request): \Illuminate\Contracts\View\View
    {
        $artist->setRelation('similar', $artist->similar()->paginate(20));

        $view = View::make('artist.similar-artists')
            ->with([
                'artist' => $artist,
                'similar' => $artist->similar,
            ]);

        if ($request->ajax()) {
            $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags($artist);

        return $view;
    }

    public function followers(Artist $artist, Request $request): \Illuminate\Contracts\View\View
    {
        $view = View::make('artist.followers')
            ->with([
                'artist' => $artist->load('followers'),
            ]);

        if ($request->ajax()) {
            $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags($artist);

        return $view;
    }

    public function events(Artist $artist, Request $request): \Illuminate\Contracts\View\View
    {
        $view = View::make('artist.events')
            ->with([
                'artist' => $artist->load('followers'),
            ]);

        if ($request->ajax()) {
            $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags($artist);

        return $view;
    }
}
