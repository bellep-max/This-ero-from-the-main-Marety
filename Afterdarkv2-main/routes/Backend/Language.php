<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Album
*/

use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\TranslationController;

Route::prefix('languages')->as('languages.')->middleware(['role:admin_languages'])->group(function () {
    Route::get('', [LanguageController::class, 'index'])->name('index');
    Route::post('', [LanguageController::class, 'store'])->name('store');
    Route::get('{language}/delete', [LanguageController::class, 'destroy'])->name('destroy');

    Route::name('translations.')->group(function () {
        Route::get('{language}/translations', [TranslationController::class, 'show'])->name('show');
        Route::post('{language}/translations/create', [TranslationController::class, 'store'])->name('create');
        Route::post('{language}/translations', [TranslationController::class, 'update'])->name('update');
    });
});
