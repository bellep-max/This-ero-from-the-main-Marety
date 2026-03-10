<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

/*
 * Edit User
*/

use App\Http\Controllers\Backend\ProfileController;

Route::prefix('profile')->as('profile.')->group(function () {
    Route::get('', [ProfileController::class, 'index'])->name('index');
    Route::post('', [ProfileController::class, 'store'])->name('store');
});
