<?php

use App\Http\Controllers\Api\V1\ActionController;
use App\Http\Controllers\Api\V1\AdventureController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\ChannelController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DiscoverController;
use App\Http\Controllers\Api\V1\DownloadController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\FollowerController;
use App\Http\Controllers\Api\V1\FollowingController;
use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\HomepageController;
use App\Http\Controllers\Api\V1\InitController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PlaylistController;
use App\Http\Controllers\Api\V1\PodcastController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\ShareController;
use App\Http\Controllers\Api\V1\SongController;
use App\Http\Controllers\Api\V1\StreamController;
use App\Http\Controllers\Api\V1\TermsController;
use App\Http\Controllers\Api\V1\TrendingController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\UserLinktreeController;
use App\Http\Controllers\Api\V1\UserProfileController;
use App\Http\Controllers\Api\V1\UserSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('init', InitController::class);

Route::prefix('auth')->as('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
    });
});

Route::get('homepage', HomepageController::class)->name('homepage');

Route::get('discover', [DiscoverController::class, 'index'])->name('discover.index');

Route::prefix('trending')->as('trending.')->group(function () {
    Route::get('/', [TrendingController::class, 'index'])->name('index');
    Route::get('genre/{genre}', [TrendingController::class, 'topByGenre'])->name('topByGenre');
    Route::get('songs', [TrendingController::class, 'topSongs'])->name('topSongs');
    Route::get('voice/{voice}', [TrendingController::class, 'topVoice'])->name('topVoice');
});

Route::get('search', [SearchController::class, 'show'])->name('search.show');

Route::prefix('songs')->as('songs.')->group(function () {
    Route::get('{song}', [SongController::class, 'show'])->name('show');
    Route::get('{song}/edit', [SongController::class, 'edit'])->name('edit')->middleware('auth:sanctum');
    Route::put('{song}', [SongController::class, 'update'])->name('update')->middleware('auth:sanctum');
    Route::delete('{song}', [SongController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
});

Route::prefix('adventures')->as('adventures.')->group(function () {
    Route::get('{adventure}', [AdventureController::class, 'show'])->name('show');
    Route::get('{adventure}/edit', [AdventureController::class, 'edit'])->name('edit')->middleware('auth:sanctum');
    Route::put('{adventure}', [AdventureController::class, 'update'])->name('update')->middleware('auth:sanctum');
    Route::delete('{adventure}', [AdventureController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
});

Route::prefix('genres')->as('genres.')->group(function () {
    Route::get('/', [GenreController::class, 'index'])->name('index');
    Route::get('{genre}', [GenreController::class, 'show'])->name('show');
});

Route::get('channels/{channel}', [ChannelController::class, 'show'])->name('channels.show');

Route::prefix('blog')->as('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('{post}', [BlogController::class, 'show'])->name('show');
});

Route::prefix('stream')->as('stream.')->group(function () {
    Route::get('mp3/{uuid}/{type}', [StreamController::class, 'mp3'])->name('mp3');
    Route::get('hls/{uuid}/{type}', [StreamController::class, 'hls'])->name('hls');
    Route::get('youtube/{song}', [StreamController::class, 'youtube'])->name('youtube');
    Route::post('on-track-played', [StreamController::class, 'onTrackPlayed'])->name('onTrackPlayed');
});

Route::prefix('users/{user}')->as('users.')->group(function () {
    Route::get('/', [UserProfileController::class, 'show'])->name('show');
    Route::get('tracks', [UserProfileController::class, 'tracks'])->name('tracks')->middleware('auth:sanctum');
    Route::get('adventures', [UserProfileController::class, 'adventures'])->name('adventures');
    Route::get('podcasts', [UserProfileController::class, 'podcasts'])->name('podcasts');
    Route::get('playlists', [UserProfileController::class, 'playlists'])->name('playlists');
    Route::get('notifications', [UserProfileController::class, 'notifications'])->name('notifications')->middleware('auth:sanctum');
    Route::get('purchased', [UserProfileController::class, 'purchased'])->name('purchased')->middleware('auth:sanctum');
    Route::get('followers', [FollowerController::class, 'show'])->name('followers');
    Route::get('following', [FollowingController::class, 'index'])->name('following');
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('favorites', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

        Route::post('subscriptions/checkout', [UserSubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
        Route::get('subscriptions/success', [UserSubscriptionController::class, 'paymentSuccess'])->name('subscriptions.success');
        Route::post('subscriptions/cancel', [UserSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/suspend', [UserSubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
        Route::post('subscriptions/activate', [UserSubscriptionController::class, 'activate'])->name('subscriptions.activate');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('profile', [UserProfileController::class, 'update'])->name('users.profile.update');

    Route::prefix('playlists')->as('playlists.')->group(function () {
        Route::post('/', [PlaylistController::class, 'store'])->name('store');
        Route::get('{playlist}', [PlaylistController::class, 'show'])->name('show');
        Route::get('{playlist}/edit', [PlaylistController::class, 'edit'])->name('edit');
        Route::put('{playlist}', [PlaylistController::class, 'update'])->name('update');
        Route::delete('{playlist}', [PlaylistController::class, 'destroy'])->name('destroy');
        Route::post('{playlist}/songs', [PlaylistController::class, 'addSong'])->name('addSong');
        Route::delete('{playlist}/songs/{song}', [PlaylistController::class, 'removeSong'])->name('removeSong');
        Route::post('{playlist}/collab/{user}', [PlaylistController::class, 'setCollab'])->name('setCollab');
        Route::post('{playlist}/collaborators', [PlaylistController::class, 'inviteCollaborator'])->name('inviteCollaborator');
        Route::post('{playlist}/collaborators/{user}/respond', [PlaylistController::class, 'respondCollaboration'])->name('respondCollaboration');
    });

    Route::prefix('podcasts')->as('podcasts.')->group(function () {
        Route::get('/', [PodcastController::class, 'index'])->name('index')->withoutMiddleware('auth:sanctum');
        Route::post('/', [PodcastController::class, 'store'])->name('store');
        Route::get('{podcast}', [PodcastController::class, 'show'])->name('show')->withoutMiddleware('auth:sanctum');
        Route::get('{podcast}/edit', [PodcastController::class, 'edit'])->name('edit');
        Route::put('{podcast}', [PodcastController::class, 'update'])->name('update');
        Route::get('{podcast}/seasons/{season}', [PodcastController::class, 'showSeason'])->name('seasons.show')->withoutMiddleware('auth:sanctum');
    });

    Route::prefix('episodes')->as('episodes.')->group(function () {
        Route::get('{episode}', [PodcastController::class, 'showEpisode'])->name('show')->withoutMiddleware('auth:sanctum');
        Route::get('{episode}/edit', [PodcastController::class, 'editEpisode'])->name('edit');
        Route::put('{episode}', [PodcastController::class, 'updateEpisode'])->name('update');
    });

    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');

    Route::post('followers', [FollowerController::class, 'store'])->name('followers.store');
    Route::delete('followers', [FollowerController::class, 'destroy'])->name('followers.destroy');

    Route::post('following', [FollowingController::class, 'store'])->name('following.store');
    Route::delete('following', [FollowingController::class, 'destroy'])->name('following.destroy');

    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');

    Route::post('actions', [ActionController::class, 'store'])->name('actions.store');

    Route::prefix('upload')->as('upload.')->group(function () {
        Route::get('/', [UploadController::class, 'index'])->name('index');
        Route::post('tracks', [UploadController::class, 'tracks'])->name('tracks');
        Route::post('adventures/heading', [UploadController::class, 'adventureHeading'])->name('adventures.heading');
        Route::post('adventures/root', [UploadController::class, 'adventureRoot'])->name('adventures.root');
        Route::delete('adventures/{adventure}', [UploadController::class, 'destroyAdventure'])->name('adventures.destroy');
        Route::post('episodes', [UploadController::class, 'episodes'])->name('episodes');
    });

    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('profile', [SettingsController::class, 'profile'])->name('profile');
        Route::put('profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/avatar', [SettingsController::class, 'updateAvatar'])->name('profile.avatar');
        Route::get('account', [SettingsController::class, 'account'])->name('account');
        Route::put('account', [SettingsController::class, 'updateAccount'])->name('account.update');
        Route::delete('account', [SettingsController::class, 'deleteAccount'])->name('account.destroy');
        Route::get('password', [SettingsController::class, 'password'])->name('password');
        Route::put('password', [SettingsController::class, 'updatePassword'])->name('password.update');
        Route::get('preferences', [SettingsController::class, 'preferences'])->name('preferences');
        Route::put('preferences', [SettingsController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('subscription', [SettingsController::class, 'subscription'])->name('subscription');
        Route::get('connected-services', [SettingsController::class, 'connectedServices'])->name('connectedServices');
    });

    Route::post('terms/accept', [TermsController::class, 'accept'])->name('terms.accept');

    Route::get('download/{type}/{uuid}', [DownloadController::class, 'download'])->name('download');
    Route::get('download-hd/{type}/{uuid}', [DownloadController::class, 'downloadHd'])->name('download.hd');

    Route::put('linktree', [UserLinktreeController::class, 'update'])->name('linktree.update');
    Route::delete('linktree', [UserLinktreeController::class, 'destroy'])->name('linktree.destroy');
});

Route::get('share/embed/{type}/{uuid}/{theme?}', [ShareController::class, 'embed'])->name('share.embed');
