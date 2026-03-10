<?php

/**
 * Copyright (c) 2015 Jens Segers
 * The MIT License (MIT).
 *
 * @laravel-admin-extensions/log-viewer
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Encore\LogViewer;
use App\Services\AjaxViewService;
use App\Services\Backend\BackendService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use View;

class LogController extends Controller
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(Request $request, $file = null)
    {
        if ($file === null) {
            $file = (new LogViewer)->getLastModifiedLog();
        }

        $offset = $request->get('offset');
        $viewer = new LogViewer($file);

        $view = View::make('backend.logs.index')
            ->with([
                'logs' => $viewer->fetch($offset),
                'logFiles' => $viewer->getLogFiles(),
                'fileName' => $viewer->file,
                'end' => $viewer->getFilesize(),
                'tailPath' => route('backend.log-viewer-tail', ['file' => $viewer->file]),
                'prevUrl' => $viewer->getPrevPageUrl(),
                'nextUrl' => $viewer->getNextPageUrl(),
                'filePath' => $viewer->getFilePath(),
                'size' => BackendService::fileSizeConverter($viewer->getFilesize()),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function tail($file, Request $request): array
    {
        $offset = $request->get('offset');
        $viewer = new LogViewer($file);
        [$pos, $logs] = $viewer->tail($offset);

        return compact('pos', 'logs');
    }
}
