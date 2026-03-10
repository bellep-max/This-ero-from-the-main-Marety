<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Services\AjaxViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use View;

class StationController extends Controller
{
    private $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function show(Station $station)
    {
        $station->setRelation('related', Station::query()->where('category', 'REGEXP', '(^|,)(' . $station->category . ')(,|$)')->where('id', '!=', $station->id)->paginate(5));

        $view = View::make('station.index')
            ->with([
                'station' => $station,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags($station);

        return $view;
    }

    public function report(Station $station): JsonResponse
    {
        $station->increment('failed_count');

        return response()->json(['success' => true]);
    }

    public function played(Station $station): JsonResponse
    {
        $station->increment('play_count');

        return response()->json(['success' => true]);
    }
}
