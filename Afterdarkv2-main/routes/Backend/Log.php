<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 00:27
 */

use App\Http\Controllers\Backend\LogController;

Route::prefix('logs')->middleware(['role:admin_system_logs'])->group(function () {
    Route::get('', [LogController::class, 'index'])->name('log-viewer-index');
    Route::get('{file?}', [LogController::class, 'index'])->name('log-viewer-file');
    Route::get('{file}/tail', [LogController::class, 'tail'])->name('log-viewer-tail');
});
