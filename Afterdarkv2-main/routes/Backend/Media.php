<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-21
 * Time: 23:03
 */

use App\Http\Controllers\Backend\MediaController;

Route::prefix('media')->as('media.')->middleware(['role:admin_media_manager'])->group(function () {
    Route::get('', [MediaController::class, 'index'])->name('index');
    Route::get('download', [MediaController::class, 'download'])->name('download');
    Route::delete('delete', [MediaController::class, 'delete'])->name('delete');
    Route::put('move', [MediaController::class, 'move'])->name('move');
    Route::post('upload', [MediaController::class, 'upload'])->name('upload');
    Route::post('folder', [MediaController::class, 'newFolder'])->name('new-folder');
});
