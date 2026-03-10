<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 20:34
 */

use App\Http\Controllers\Api\AlbumController;

Route::prefix('album')->as('album.')->group(function () {
    Route::get('{album}', [AlbumController::class, 'show'])->name('show');
    Route::get('{id}/related-albums', [AlbumController::class, 'related'])->name('related');
});
