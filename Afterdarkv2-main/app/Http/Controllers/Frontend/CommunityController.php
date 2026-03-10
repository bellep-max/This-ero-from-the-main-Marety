<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Channel;
use App\Models\Slide;
use App\Services\AjaxViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use stdClass;

class CommunityController extends Controller
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
        $community = new stdClass;
        $community->activities = Activity::query()
            ->where('action', '!=', ActionConstants::ADD_EVENT)
            ->latest()
            ->paginate(20);

        $channels = Channel::query()
            ->where('allow_community', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();

        $slides = Slide::query()
            ->where('allow_community', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();

        $view = View::make('community.index')
            ->with([
                'slides' => json_decode(json_encode($slides)),
                'channels' => json_decode(json_encode($channels)),
                'community' => $community,
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request)
            : $view;
    }
}
