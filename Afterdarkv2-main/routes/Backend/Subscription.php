<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:13
 */

use App\Http\Controllers\Backend\SubscriptionController;

Route::prefix('subscriptions')->as('subscriptions.')->middleware(['role:admin_subscriptions'])->group(function () {
    Route::get('', [SubscriptionController::class, 'index'])->name('index');
    Route::get('{subscription}/edit', [SubscriptionController::class, 'edit'])->name('edit');
    Route::patch('{subscription}', [SubscriptionController::class, 'update'])->name('update');
    Route::get('{subscription}/delete', [SubscriptionController::class, 'destroy'])->name('destroy');
});
