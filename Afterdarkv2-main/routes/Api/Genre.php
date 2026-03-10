<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:52
 */

use App\Http\Controllers\Api\GenreController;

Route::prefix('genre')->group(function () {
    Route::get('{genre:alt_name}', [GenreController::class, 'show'])->name('genre');
    Route::get('{genre:alt_name}/songs', [GenreController::class, 'songs'])->name('genre.songs');
    Route::get('{genre:alt_name}/albums', [GenreController::class, 'albums'])->name('genre.albums');
    Route::get('{genre:alt_name}/artists', [GenreController::class, 'artists'])->name('genre.artists');
    Route::get('{genre:alt_name}/playlists', [GenreController::class, 'playlists'])->name('genre.playlists');
});
