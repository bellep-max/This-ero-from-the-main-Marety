<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:52
 */

use App\Http\Controllers\Api\RadioController;

Route::prefix('radio')->group(function () {
    Route::get('', [RadioController::class, 'index'])->name('radio');
    Route::get('categories', [RadioController::class, 'categories'])->name('radio.categories');
    Route::get('{id}/stations', [RadioController::class, 'stations'])->name('radio.station');
});
