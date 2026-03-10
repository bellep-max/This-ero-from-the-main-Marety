<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 10:00
 */

use App\Http\Controllers\Backend\EmailController;

Route::prefix('email')->as('email.')->middleware(['role:admin_email'])->group(function () {
    Route::get('', [EmailController::class, 'index'])->name('index');
    Route::get('{email}/edit', [EmailController::class, 'edit'])->name('edit');
    Route::patch('{email}', [EmailController::class, 'update'])->name('update');
    Route::get('{email}/delete', [EmailController::class, 'delete'])->name('delete');
});
