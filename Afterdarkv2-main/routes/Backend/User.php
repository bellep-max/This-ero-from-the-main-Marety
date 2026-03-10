<?php

/*
 * Edit User
*/

use App\Http\Controllers\Backend\UserController;

Route::prefix('users')->as('users.')->middleware(['role:admin_users'])->group(function () {
    Route::get('', [UserController::class, 'index'])->name('index');
    Route::post('', [UserController::class, 'store'])->name('store');
    Route::get('create', [UserController::class, 'create'])->name('create');
    Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::patch('{user}', [UserController::class, 'update'])->name('update');
    Route::get('{user}/delete', [UserController::class, 'destroy'])->name('destroy');
    Route::post('batch', [UserController::class, 'batch'])->name('batch');
});
