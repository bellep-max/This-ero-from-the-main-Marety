<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:59
 */

use App\Http\Controllers\Backend\SettingsController;

Route::prefix('settings')->as('settings.')->middleware(['role:admin_settings'])->group(function () {
    Route::get('', [SettingsController::class, 'index'])->name('index');
    Route::post('', [SettingsController::class, 'store'])->name('store');
});
