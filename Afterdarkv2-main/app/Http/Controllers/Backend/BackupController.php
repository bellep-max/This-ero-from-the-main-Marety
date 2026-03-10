<?php

/**
 * Copyright (c) 2015 Jens Segers
 * The MIT License (MIT).
 *
 * @laravel-admin-extensions/back-up
 */

namespace App\Http\Controllers\Backend;

use App\Services\AjaxViewService;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\Backup\Commands\ListCommand;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class BackupController
{
    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|string|null
    {
        $view = View::make('backend.backup.index')
            ->with([
                'backups' => $this->getExists(),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function getExists(): array
    {
        $config = MonitoredBackupsConfig::fromArray(config('backup.monitor_backups'));
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig($config);
        $listCommand = new ListCommand(Config::fromArray(config('backup')));

        $rows = $statuses->map(function (BackupDestinationStatus $backupDestinationStatus) use ($listCommand) {
            return $listCommand->convertToRow($backupDestinationStatus);
        })->all();

        foreach ($statuses as $index => $status) {
            $name = $status->backupDestination()->backupName();
            $files = array_map('basename', $status->backupDestination()->disk()->allFiles($name));
            $rows[$index]['files'] = array_slice(array_reverse($files), 0, 30);
        }

        return $rows;
    }

    /**
     * Download a backup zip file.
     */
    public function download(Request $request): BinaryFileResponse|Response|ResponseFactory
    {
        $disk = $request->get('disk');
        $file = $request->get('file');
        $storage = Storage::disk($disk);
        $fullPath = $storage->getDriver()->getAdapter()->applyPathPrefix($file);

        return File::isFile($fullPath)
            ? response()->download($fullPath)
            : response('', 404);
    }

    /**
     * Run `backup:run` command.
     */
    public function run(): JsonResponse
    {
        try {
            ini_set('max_execution_time', 300);
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();

            return response()->json([
                'status' => true,
                'message' => $output,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a backup file.
     */
    public function delete(Request $request): JsonResponse
    {
        $disk = Storage::disk($request->get('disk'));
        $file = $request->get('file');
        if ($disk->exists($file)) {
            $disk->delete($file);

            return response()->json([
                'status' => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => trans('admin.delete_failed'),
        ]);
    }
}
