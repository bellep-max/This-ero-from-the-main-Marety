<?php

use App\Http\Controllers\Settings\AccountController;
use App\Http\Controllers\Settings\ConnectedServiceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\PreferencesController;
use App\Http\Controllers\Settings\ProfileAvatarController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SubscriptionController;
use App\Http\Middleware\HasNoActiveSubscription;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::prefix('settings')->as('settings.')->group(function () {
        Route::prefix('profile')->as('profile.')->group(function () {
            Route::get('', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('', [ProfileController::class, 'update'])->name('update');
            Route::patch('avatar', [ProfileAvatarController::class, 'update'])->name('avatar.update');
            //    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        Route::prefix('subscription')->as('subscription.')->group(function () {
            Route::get('', [SubscriptionController::class, 'edit'])->name('edit');
            Route::get('checkout', [SubscriptionController::class, 'checkout'])
                ->middleware(HasNoActiveSubscription::class)
                ->name('checkout');

            Route::get('success', [SubscriptionController::class, 'success'])->name('success');
            Route::get('cancel', [SubscriptionController::class, 'checkoutCancel'])->name('checkout-cancel');
            Route::post('suspend', [SubscriptionController::class, 'suspend'])->name('suspend');
            Route::post('activate', [SubscriptionController::class, 'activate'])->name('activate');
            Route::post('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('account')->as('account.')->group(function () {
            Route::get('', [AccountController::class, 'edit'])->name('edit');
            Route::patch('', [AccountController::class, 'update'])->name('update');
            //    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        //        Route::prefix('preferences')->as('preferences.')->group(function () {
        //            Route::get('', [PreferencesController::class, 'edit'])->name('edit');
        //            Route::patch('', [PreferencesController::class, 'update'])->name('update');
        //        });

        Route::prefix('password')->as('password.')->group(function () {
            Route::get('', [PasswordController::class, 'edit'])->name('edit');
            Route::put('', [PasswordController::class, 'update'])->name('update');
        });

        Route::prefix('connections')->as('connections.')->group(function () {
            Route::get('', [ConnectedServiceController::class, 'index'])->name('index');
            Route::put('', [ConnectedServiceController::class, 'update'])->name('update');

            Route::get('{provider}/redirect', [ConnectedServiceController::class, 'redirect'])->name('redirect');
            Route::get('{provider}/callback', [ConnectedServiceController::class, 'callback'])->name('callback');
        });
    });
});
