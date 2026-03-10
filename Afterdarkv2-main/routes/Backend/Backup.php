<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 02:35
 */

use App\Http\Controllers\Backend\BackupController;

Route::prefix('backup')->middleware('role:admin_backup')->group(function () {
    Route::get('', [BackupController::class, 'index'])->name('backup-list');
    Route::get('download', [BackupController::class, 'download'])->name('backup-download');
    Route::post('run', [BackupController::class, 'run'])->name('backup-run');
    Route::post('run/db', [BackupController::class, 'runDB'])->name('backup-run-db');
    Route::delete('delete', [BackupController::class, 'delete'])->name('backup-delete');
});
