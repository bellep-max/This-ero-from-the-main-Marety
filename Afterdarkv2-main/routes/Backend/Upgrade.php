<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Album
*/

use App\Http\Controllers\Backend\UpgradeController;

Route::prefix('upgrade')->as('upgrade.')->middleware('role:admin_terminal')->group(function () {
    Route::get('', [UpgradeController::class, 'index'])->name('index');
    Route::post('', [UpgradeController::class, 'process'])->name('process');
});
