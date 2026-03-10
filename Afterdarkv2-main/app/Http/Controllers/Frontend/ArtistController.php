<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\GetArtistDetails;
use App\Models\Artist;
use App\Services\AjaxViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ArtistController extends Controller
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function show(Artist $artist, Request $request)
    {
        if (config('settings.automate')) {
            GetArtistDetails::dispatch($artist);
            sleep(1);
        }

        $artist->setRelation('albums', $artist->albums()->latest()->limit(4)->get());
        $artist->setRelation('songs', $artist->songs()->paginate(20));
        $artist->setRelation('podcasts', $artist->podcasts()->paginate(20));
        $artist->setRelation('activities', $artist->activities()->latest()->paginate(10));
        $artist->setRelation('similar', $artist->similar()->paginate(5));

        $view = View::make('artist.index')
            ->with([
                'artist' => $artist,
            ]);

        if ($request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $request);
        }

        Helper::getMetatags($artist);

        return $view;
    }

    public function albums(Artist $artist, Request $request)
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

    public function podcasts(Artist $artist, Request $request)
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

    public function similar(Artist $artist, Request $request)
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

    public function followers(Artist $artist, Request $request)
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

    public function events(Artist $artist, Request $request)
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
