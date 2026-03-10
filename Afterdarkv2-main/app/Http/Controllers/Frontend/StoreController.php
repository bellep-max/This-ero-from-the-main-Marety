<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\DefaultConstants;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use App\Services\AjaxViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class StoreController extends Controller
{
    private Request $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(): \Illuminate\Contracts\View\View|string|null
    {
        $songs = Song::query()->where('selling', DefaultConstants::TRUE)
            ->when($this->request->input('genres') && is_array($this->request->input('genres')), function ($query) {
                $query->where('genre', 'REGEXP', '(^|,)(' . implode(',', $this->request->input('genres')) . ')(,|$)');
            })
            ->when($this->request->has('terms'), function ($query) {
                $query->where(function ($query) {
                    foreach ($this->request->input('terms') as $index => $term) {
                        if ($index == 0) {
                            $query->where('title', 'LIKE', "%$term%");
                        } else {
                            $query->orWhere('title', 'LIKE', "%$term%");
                        }
                    }
                });
            })
            ->when($this->request->input('artists') && is_array($this->request->input('artists')), function ($query) {
                $query->where(function ($query) {
                    foreach ($this->request->input('artists') as $index => $artistId) {
                        if ($index == 0) {
                            $query->where('artistIds', 'REGEXP', '(^|,)(' . $artistId . ')(,|$)');
                        } else {
                            $query->orWhere('artistIds', 'REGEXP', '(^|,)(' . $artistId . ')(,|$)');
                        }
                    }
                });
            })
            ->paginate(50);

        $view = View::make('store.index')
            ->with([
                'songs' => $songs,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK);
        }

        Helper::getMetatags();

        return $view;
    }

    public function add() {}

    public function filter() {}

    public function genres(): JsonResponse
    {
        return response()
            ->json(
                Genre::query()->orderBy('discover', 'desc')->limit(100)->get()
            );
    }

    public function artists(): JsonResponse
    {
        return response()->json(
            Artist::query()->limit(100)->latest()->get()
        );
    }
}
