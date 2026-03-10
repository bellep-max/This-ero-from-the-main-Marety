<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Radio
*/

use App\Http\Controllers\Backend\CountryController;
use App\Http\Controllers\Backend\CountryLanguageController;

Route::prefix('countries')->as('countries.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [CountryController::class, 'index'])->name('index');
    Route::post('', [CountryController::class, 'store'])->name('store');
    Route::get('create', [CountryController::class, 'create'])->name('create');
    Route::get('{country}/edit', [CountryController::class, 'edit'])->name('edit');
    Route::patch('{country}', [CountryController::class, 'update'])->name('update');
    Route::get('{country}/delete', [CountryController::class, 'destroy'])->name('destroy');

    Route::post('get-city', [CountryController::class, 'getCity'])->name('get.city');
});

Route::prefix('country-languages')->as('country.languages.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [CountryLanguageController::class, 'index'])->name('index');
    Route::post('', [CountryLanguageController::class, 'store'])->name('store');
    Route::get('create', [CountryLanguageController::class, 'create'])->name('create');
    Route::get('{language}/edit', [CountryLanguageController::class, 'edit'])->name('edit');
    Route::patch('{language}', [CountryLanguageController::class, 'update'])->name('update');
    Route::get('{language}/delete', [CountryLanguageController::class, 'destroy'])->name('destroy');

    Route::post('batch', [CountryLanguageController::class, 'batch'])->name('batch');
    Route::post('get-language', [CountryLanguageController::class, 'getLanguage'])->name('get.language');
});
