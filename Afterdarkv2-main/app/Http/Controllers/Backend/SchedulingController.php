<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Encore\Scheduling;
use App\Services\AjaxViewService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SchedulingController
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): string|null|\Illuminate\Contracts\View\View
    {
        $scheduling = new Scheduling;

        $view = View::make('backend.scheduling.index')
            ->with([
                'events' => $scheduling->getTasks(),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function run(Request $request): array
    {
        $scheduling = new Scheduling;

        try {
            $output = $scheduling->runTask($request->get('id'));

            return [
                'status' => true,
                'message' => 'success',
                'data' => $output,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'failed',
                'data' => $e->getMessage(),
            ];
        }
    }
}
