<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

use App\Http\Controllers\Backend\SongController;

Route::prefix('episodes')->as('episodes.')->middleware(['role:admin_songs'])->group(function () {
    Route::get('', [SongController::class, 'index'])->name('index');
    Route::post('', [SongController::class, 'batch'])->name('batch');
    Route::get('{song}/edit', [SongController::class, 'edit'])->name('edit');
    Route::post('{song}/edit', [SongController::class, 'update'])->name('edit.post');
    Route::get('{song}/delete', [SongController::class, 'destroy'])->name('destroy');
    Route::post('edit-title', [SongController::class, 'updateTitle'])->name('edit.title.post');
    Route::post('{song}/reject', [SongController::class, 'reject'])->name('edit.reject.post');
});
