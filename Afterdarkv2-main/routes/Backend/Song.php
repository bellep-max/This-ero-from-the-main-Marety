<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

/*
 * Edit Song
*/

use App\Http\Controllers\Backend\SongController;

Route::prefix('songs')->as('songs.')->middleware(['role:admin_songs'])->group(function () {
    Route::get('', [SongController::class, 'index'])->name('index');
    Route::get('{song}/edit', [SongController::class, 'edit'])->name('edit');
    Route::patch('{song}', [SongController::class, 'update'])->name('update');
    Route::get('{song}/delete', [SongController::class, 'destroy'])->name('destroy');
    Route::post('{song}/reject', [SongController::class, 'reject'])->name('reject');
    Route::post('{song}/title', [SongController::class, 'updateTitle'])->name('update.title');
    Route::post('batch', [SongController::class, 'batch'])->name('batch');
});
