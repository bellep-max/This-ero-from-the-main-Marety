<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 18:12
 */

use App\Http\Controllers\Backend\SchedulingController;

Route::prefix('scheduling')->as('scheduling.')->middleware(['role:admin_scheduled'])->group(function () {
    Route::get('', [SchedulingController::class, 'index'])->name('index');
    Route::post('run', [SchedulingController::class, 'run'])->name('run');
});
