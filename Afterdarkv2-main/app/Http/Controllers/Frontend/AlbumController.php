<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\GetAlbumDetails;
use App\Models\Album;
use App\Models\Group;
use App\Services\AjaxViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AlbumController extends Controller
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function show(Album $album, Request $request)
    {
        if (!$album->approved && auth()->check() && Group::getValue('admin_albums')) {
        } else {
            if (!isset($album->id)) {
                abort(404);
            } elseif (auth()->check() && !$album->is_visible && ($album->user_id != auth()->id())) {
                abort(404);
            } elseif (!auth()->check() && !$album->is_visible) {
                abort(404);
            } elseif (!$album->approved) {
                abort(404);
            }
        }

        if (config('settings.automate')) {
            GetAlbumDetails::dispatch($album);
            sleep(1);
        }

        $album->setRelation('songs', $album->songs()->paginate(20));

        $view = View::make('album.index')
            ->with([
                'album' => $album,
            ]);

        if (count($album->artists) == 1) {
            $artist = $album->artists->first();
            $artist->setRelation('songs', $artist->songs()->paginate(5));
            $artist->setRelation('similar', $artist->similar()->paginate(5));
            $artistTopSongs = $artist->songs;
            $view = $view->with('artistTopSongs', $artistTopSongs)->with('artist', $artist);
        }

        if ($request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags($album);

        return $view;
    }

    public function related(Request $request): \Illuminate\Contracts\View\View|string|null
    {
        $album = auth()->check() && Group::getValue('admin_albums')
            ? Album::query()->withoutGlobalScopes()->findOrFail($request->route('id'))
            : Album::query()->findOrFail($request->route('id'));

        $view = View::make('album.related')
            ->with([
                'album' => $album,
                'related' => Album::query()
                    ->where('id', '!=', $album->id)
                    ->paginate(20),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }
}
