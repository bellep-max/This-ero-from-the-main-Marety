<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 20:34
 */

use App\Http\Controllers\Api\ArtistController;

Route::prefix('artist')->group(function () {
    Route::get('{id}', [ArtistController::class, 'show'])->name('artist');
    Route::get('{id}/albums', [ArtistController::class, 'albums'])->name('artist.albums');
    Route::get('{id}/similar-artists', [ArtistController::class, 'similar'])->name('artist.similar');
    Route::get('{id}/followers', [ArtistController::class, 'followers'])->name('artist.followers');
    Route::get('{id}/events', [ArtistController::class, 'events'])->name('artist.events');
});
