<?php

/**
 * Copyright (c) 2015 Jens Segers
 * The MIT License (MIT).
 *
 * @laravel-admin-extensions/media-manager
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Encore\MediaManager;
use App\Services\AjaxViewService;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|string|null
    {
        $path = $request->get('path', '/');
        $manager = new MediaManager('local', $path);

        $view = View::make('backend.media.index')
            ->with([
                'list' => $manager->ls(),
                'nav' => $manager->navigation(),
                'url' => $manager->urls(),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function download(Request $request): Application|Response|BinaryFileResponse|ResponseFactory
    {
        $manager = new MediaManager('local', $request->get('file'));

        return $manager->download();
    }

    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $files = $request->file('files');
        $dir = $request->get('dir', '/');
        $manager = new MediaManager('local', $dir);

        try {
            if ($manager->upload($files)) {
                return response()->json([
                    'status' => true,
                    'message' => ('Upload succeeded.'),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function delete(Request $request)
    {
        $files = $request->get('files');
        $manager = new MediaManager('local');

        try {
            if ($manager->delete($files)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Delete succeeded',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function move(Request $request)
    {
        $path = $request->get('path');
        $new = $request->get('new');
        $manager = new MediaManager('local', $path);

        try {
            if ($manager->move($new)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Move succeeded',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function newFolder(Request $request)
    {
        $dir = $request->get('dir');
        $name = $request->get('name');
        $manager = new MediaManager('local', $dir);

        try {
            if ($manager->newFolder($name)) {
                return response()->json([
                    'status' => true,
                    'message' => 'New folder created.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
