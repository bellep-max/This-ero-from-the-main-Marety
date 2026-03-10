<?php

namespace App\Http\Controllers\Api;

use App\Jobs\GetAlbumDetails;
use App\Models\Album;
use App\Models\Group;
use App\Services\AjaxViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use View;

class AlbumController
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function show(Album $album, Request $request): JsonResponse
    {
        if (config('settings.automate')) {
            GetAlbumDetails::dispatch($album);
            sleep(1);
        }

        $album->setRelation('songs', $album->songs()->withoutGlobalScopes()->get());

        if ($request->get('callback')) {
            return response()
                ->jsonp($request->get('callback'), $album->songs)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($album);
    }

    public function related(Request $request)
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
