<?php

use App\Http\Controllers\Backend\GroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('groups')->as('groups.')->middleware(['role:admin_roles'])->group(function () {
    Route::get('', [GroupController::class, 'index'])->name('index');
    Route::post('', [GroupController::class, 'store'])->name('store');
    Route::get('{group}/edit', [GroupController::class, 'edit'])->name('edit');
    Route::patch('{group}', [GroupController::class, 'update'])->name('update');
    Route::get('{group}/delete', [GroupController::class, 'destroy'])->name('destroy');
});
