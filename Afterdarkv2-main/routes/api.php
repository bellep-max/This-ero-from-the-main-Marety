<?php

use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Frontend\RegisterController;
use App\Http\Controllers\Frontend\Upload\UploadController;
use App\Http\Controllers\Frontend\User\UserPasswordController;
use App\Http\Controllers\Frontend\User\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::group(['laroute' => false, 'namespace' => 'App\Http\Controllers\Api', 'as' => 'api.'], function () {
//    Route::name('podcast.')->group(function () {
//        Route::get('podcast/{podcast}/seasons', [PodcastController::class, 'seasons'])->name('seasons');
//        Route::get('podcast/{podcast}/season/{season}/episodes', [PodcastController::class, 'episodes'])->name('episodes');
//        Route::get('episode/{episode}', [PodcastController::class, 'episode'])->name('episode');
//    });
// });
//
// Route::group(['laroute' => false, 'namespace' => 'App\Http\Controllers\Frontend', 'as' => 'api.'], function () {
//    Route::name('auth.')->group(function () {
//        Route::prefix('auth')->group(function () {
//            Route::post('userInfoValidate', [RegisterController::class, 'userInfoValidate'])->name('info.validate');
//            Route::post('usernameValidate', [RegisterController::class, 'usernameValidate'])->name('info.validate.username');
//            Route::post('signup', [RegisterController::class, 'store'])->name('signup');
//            //            Route::post('login', [RegisterController::class, 'login'])->name('login');
//        });
//
//        //        Route::prefix('connect')->as('login.socialite.')->group(function () {
//        //            Route::get('redirect/{service}', [AuthController::class, 'socialiteLogin'])->name('redirect');
//        //            Route::get('callback/{service}', [AuthController::class, 'socialiteLogin'])->name('callback');
//        //        });
//    });
// });
//
// Route::group(['laroute' => false, 'namespace' => 'App\Http\Controllers\Frontend', 'as' => 'api.', 'middleware' => 'auth:api'], function () {
//
//    Route::prefix('auth/user')->group(function () {
//        Route::post('', [RegisterController::class, 'user'])->name('auth.user');
//
//        Route::prefix('settings')->group(function () {
//            Route::post('profile', [UserProfileController::class, 'update'])->name('auth.user.settings.profile');
//            Route::post('account', [RegisterController::class, 'settingsAccount'])->name('auth.user.settings.account');
//            Route::post('password', [UserPasswordController::class, 'update'])->name('auth.user.settings/password');
//            Route::post('preferences', [RegisterController::class, 'settingsPreferences'])->name('auth.user/settings/preferences');
//        });
//
//        Route::post('notifications', [RegisterController::class, 'notifications'])->name('auth.user.notifications');
//        Route::post('notification-count', [RegisterController::class, 'notificationCount'])->name('auth.user.notification.count');
//        Route::post('favorite', [RegisterController::class, 'favorite'])->name('auth.user.favorite');
//        Route::post('song/favorite', [RegisterController::class, 'songFavorite'])->name('auth.user.song.favorite');
//
//        Route::post('library', [RegisterController::class, 'library'])->name('auth.user.library');
//        Route::post('song/library', [RegisterController::class, 'songLibrary'])->name('auth.user.song.library');
//
//        Route::prefix('playlists')->group(function () {
//            Route::post('', [RegisterController::class, 'playlists'])->name('auth.user.playlists');
//            Route::post('subscribed', [RegisterController::class, 'subscribed'])->name('auth.user.playlists.subscribed');
//        });
//
//        Route::prefix('playlist')->group(function () {
//            Route::post('delete', [RegisterController::class, 'deletePlaylist'])->name('auth.user.playlist.delete');
//            Route::post('edit', [RegisterController::class, 'editPlaylist'])->name('auth.user.playlist.edit');
//            Route::post('collaboration/set', [RegisterController::class, 'setPlaylistCollaboration'])->name('auth.user.playlist.collaboration.set');
//            Route::post('collaboration/invite', [RegisterController::class, 'collaborativePlaylist'])->name('auth.user.playlist.collaboration.invite');
//            Route::post('collaboration/accept', [RegisterController::class, 'collaborativePlaylist'])->name('auth.user.playlist.collaboration.accept');
//            Route::post('collaboration/cancel', [RegisterController::class, 'collaborativePlaylist'])->name('auth.user.playlist.collaboration.cancel');
//        });
//
//        Route::post('createPlaylist', [RegisterController::class, 'createPlaylist'])->name('auth.user.create.playlist');
//        Route::post('addToPlaylist', [RegisterController::class, 'addToPlaylist'])->name('auth.user.playlist.add.item');
//        Route::post('removeFromPlaylist', [RegisterController::class, 'removeFromPlaylist'])->name('auth.user.playlist.remove.item');
//        Route::post('managePlaylist', [RegisterController::class, 'managePlaylist'])->name('auth.user.playlist.manage');
//        Route::post('removeActivity', [RegisterController::class, 'removeActivity'])->name('auth.user.removeActivity');
//        Route::post('artistClaim', [RegisterController::class, 'artistClaim'])->name('auth.user.artistClaim');
//        Route::post('checkRole', [RegisterController::class, 'checkRole'])->name('auth.user.role');
//        Route::post('subscription/cancel', [RegisterController::class, 'cancelSubscription'])->name('auth.user.cancel.subscription');
//        Route::post('get-mention', [RegisterController::class, 'getMention'])->name('auth.user.get.mention');
//        Route::post('get-hashtag', [RegisterController::class, 'getHashTag'])->name('auth.user.get.hashtag');
//        Route::post('post-feed', [RegisterController::class, 'postFeed'])->name('auth.user.post.feed');
//        Route::post('report', [RegisterController::class, 'report'])->name('auth.user.report');
//    });
//
//    Route::get('upload', [UploadController::class, 'index'])->name('auth.upload');
//    Route::post('upload', [UploadController::class, 'store'])->name('auth.upload.post');
//    Route::get('auth/logout', [RegisterController::class, 'logout'])->name('auth.logout');
// });

Route::prefix('api/search')->as('api.search.')->middleware('cache.headers')->group(function () {
    Route::get('suggest', [SearchController::class, 'suggest'])->name('suggest');
    Route::get('song', [SearchController::class, 'song'])->name('song');
    Route::get('artist', [SearchController::class, 'artist'])->name('artist');
    Route::get('album', [SearchController::class, 'album'])->name('album');
    Route::get('playlist', [SearchController::class, 'playlist'])->name('playlist');
    Route::get('user', [SearchController::class, 'user'])->name('user');
    Route::get('station', [SearchController::class, 'station'])->name('station');
    Route::get('podcast', [SearchController::class, 'podcast'])->name('podcast');
});
