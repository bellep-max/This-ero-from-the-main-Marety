<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Playlist
*/

use App\Http\Controllers\Backend\PlaylistController;
use App\Http\Controllers\Backend\PlaylistTrackController;

Route::prefix('playlists')->as('playlists.')->middleware(['role:admin_playlists'])->group(function () {
    Route::get('', [PlaylistController::class, 'index'])->name('index');
    Route::post('', [PlaylistController::class, 'store'])->name('store');
    Route::get('{playlistVisible}/edit', [PlaylistController::class, 'edit'])->name('edit');
    Route::patch('{playlistVisible}', [PlaylistController::class, 'update'])->name('update');
    Route::get('{playlistVisible}/delete', [PlaylistController::class, 'destroy'])->name('destroy');
    Route::post('batch', [PlaylistController::class, 'batch'])->name('batch');

    Route::name('tracks.')->group(function () {
        Route::get('{playlistVisible}/track-list', [PlaylistTrackController::class, 'show'])->name('show');
        Route::post('{playlistVisible}/track-list', [PlaylistTrackController::class, 'batch'])->name('batch');
    });
});
