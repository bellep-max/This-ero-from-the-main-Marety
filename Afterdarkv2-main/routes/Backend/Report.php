<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-08
 * Time: 21:45
 */

use App\Http\Controllers\Backend\ReportController;

Route::prefix('reports')->as('reports.')->middleware(['role:admin_subscriptions'])->group(function () {
    Route::get('', [ReportController::class, 'index'])->name('index');
    Route::post('', [ReportController::class, 'store'])->name('store');
});
