<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:51
 */

use App\Http\Controllers\Api\PlaylistController;

Route::prefix('playlist')->as('playlist.')->group(function () {
    Route::get('{playlistVisible}', [PlaylistController::class, 'show'])->name('show');
    Route::get('{playlist}/subscribers', [PlaylistController::class, 'subscribers'])->name('subscribers');
    Route::get('{playlist}/collaborators', [PlaylistController::class, 'collaborators'])->name('collaborators');
});
