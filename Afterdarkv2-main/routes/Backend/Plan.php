<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 22:56
 */

use App\Http\Controllers\Backend\PlanController;

Route::prefix('plans')->as('plans.')->middleware(['role:admin_subscriptions'])->group(function () {
    Route::get('', [PlanController::class, 'index'])->name('index');
    Route::post('', [PlanController::class, 'update'])->name('update');
});
