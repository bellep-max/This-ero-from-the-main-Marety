<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:13
 */

use App\Http\Controllers\Backend\OrderController;

Route::prefix('orders')->as('orders.')->middleware(['role:admin_subscriptions'])->group(function () {
    Route::get('', [OrderController::class, 'index'])->name('index');
});
