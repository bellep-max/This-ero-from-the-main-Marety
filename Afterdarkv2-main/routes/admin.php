<?php

use App\Http\Controllers\Backend\AdminAuthController;
use App\Http\Controllers\Backend\AlbumController;
use App\Http\Controllers\Backend\ApiTesterController;
use App\Http\Controllers\Backend\ArtistController;
use App\Http\Controllers\Backend\ArtistRequestController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\BulkController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ChannelController;
use App\Http\Controllers\Backend\CityController;
use App\Http\Controllers\Backend\CommentController;
use App\Http\Controllers\Backend\CountryController;
use App\Http\Controllers\Backend\CountryLanguageController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmailController;
use App\Http\Controllers\Backend\GenreController;
use App\Http\Controllers\Backend\GroupController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\LogController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MetaTagController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PlanController;
use App\Http\Controllers\Backend\PlaylistController;
use App\Http\Controllers\Backend\PlaylistTrackController;
use App\Http\Controllers\Backend\PodcastCategoryController;
use App\Http\Controllers\Backend\PodcastController;
use App\Http\Controllers\Backend\PodcastEpisodeController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\PostMediaController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\RadioController;
use App\Http\Controllers\Backend\RegionController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\SchedulingController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\SitemapController;
use App\Http\Controllers\Backend\SlideshowController;
use App\Http\Controllers\Backend\SongController;
use App\Http\Controllers\Backend\StationController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\TerminalController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\UpgradeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WithdrawController;
use Illuminate\Support\Facades\Route;

// Admin authentication routes (no middleware - accessible without login)
Route::namespace('Backend')->prefix('admin')->as('backend.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'getLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post');
    Route::get('forgot-password', [AdminAuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPasswordPost'])->name('forgot-password.post');
    Route::post('reset-password', [AdminAuthController::class, 'resetPasswordPost'])->name('reset-password.post');
    Route::get('reset-password/{token}', [AdminAuthController::class, 'resetPassword'])->name('reset-password');
});

// Protected admin routes (require admin role)
Route::middleware(['role:admin|super-admin'])->namespace('Backend')->prefix('admin')->as('backend.')->group(function () {
    Route::get('logout', [AdminAuthController::class, 'getLogout'])->name('logout');

    Route::prefix('albums')->as('albums.')->group(function () {
        Route::get('', [AlbumController::class, 'index'])->name('index');
        Route::post('add', [AlbumController::class, 'store'])->name('store');
        Route::get('create', [AlbumController::class, 'create'])->name('create');
        Route::get('{album}/edit', [AlbumController::class, 'edit'])->name('edit');
        Route::patch('{album}', [AlbumController::class, 'update'])->name('update');
        Route::get('{album}/delete', [AlbumController::class, 'destroy'])->name('destroy');

        Route::get('{album}/upload', [AlbumController::class, 'upload'])->name('upload');
        Route::post('{album}/reject', [AlbumController::class, 'reject'])->name('reject');
        Route::get('{album}/track-list', [AlbumController::class, 'trackList'])->name('tracklist');
        Route::post('{album}/track-list', [AlbumController::class, 'trackListMassAction'])->name('tracklist.batch');
        Route::post('batch', [AlbumController::class, 'batch'])->name('batch');
        Route::get('search', [AlbumController::class, 'search'])->name('search');
    });

    Route::get('api-tester', [ApiTesterController::class, 'index'])->name('api-tester-index');
    Route::post('api-tester/handle', [ApiTesterController::class, 'handle'])->name('api-tester-handle');

    Route::prefix('artists')->as('artists.')->group(function () {
        Route::get('', [ArtistController::class, 'index'])->name('index');
        Route::get('create', [ArtistController::class, 'create'])->name('create');
        Route::post('', [ArtistController::class, 'store'])->name('store');
        Route::get('{artist}/edit', [ArtistController::class, 'edit'])->name('edit');
        Route::patch('{artist}', [ArtistController::class, 'update'])->name('update');
        Route::get('{artist}/delete', [ArtistController::class, 'destroy'])->name('delete');

        Route::get('{artist}/upload', [ArtistController::class, 'upload'])->name('upload');
        Route::post('batch', [ArtistController::class, 'batch'])->name('batch');
        Route::get('search', [ArtistController::class, 'search'])->name('search');

    });

    Route::prefix('artist-access')->as('artist.access.')->group(function () {
        Route::get('', [ArtistRequestController::class, 'index'])->name('index');
        Route::get('{artistRequest}/edit', [ArtistRequestController::class, 'edit'])->name('edit');
        Route::post('{artistRequest}', [ArtistRequestController::class, 'update'])->name('update');
        Route::get('{artistRequest}/reject', [ArtistRequestController::class, 'reject'])->name('reject');
    });

    Route::prefix('auth')->group(function () {
        Route::post('upload/bulk', [AdminAuthController::class, 'upload'])->name('upload.bulk');
        Route::post('artist/{artistId}/upload', [AdminAuthController::class, 'upload'])->name('artist.upload.bulk');
        Route::post('album/{album_id}/upload', [AdminAuthController::class, 'upload'])->name('album.upload.bulk');
        Route::post('podcast/{podcast_id}/upload', [AdminAuthController::class, 'uploadEpisode'])->name('podcast.upload.bulk');
        Route::post('song', [AdminAuthController::class, 'editSong'])->name('ajax.song.edit');
        Route::post('podcast/episode', [AdminAuthController::class, 'editEpisode'])->name('ajax.podcast.episode.edit');
        Route::post('addSong', [AdminAuthController::class, 'addSong'])->name('ajax.add.song');
        Route::post('removeSong', [AdminAuthController::class, 'removeSong'])->name('ajax.remove.song');
    });

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/check-for-update', [DashboardController::class, 'checkForUpdate'])->name('dashboard.check.for.update');

    Route::prefix('backup')->group(function () {
        Route::get('', [BackupController::class, 'index'])->name('backup-list');
        Route::get('download', [BackupController::class, 'download'])->name('backup-download');
        Route::post('run', [BackupController::class, 'run'])->name('backup-run');
        Route::post('run/db', [BackupController::class, 'runDB'])->name('backup-run-db');
        Route::delete('delete', [BackupController::class, 'delete'])->name('backup-delete');
    });

    Route::prefix('banners')->as('banners.')->group(function () {
        Route::get('', [BannerController::class, 'index'])->name('index');
        Route::post('', [BannerController::class, 'store'])->name('store');
        Route::get('create', [BannerController::class, 'create'])->name('create');
        Route::get('{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::patch('{banner}', [BannerController::class, 'update'])->name('update');
        Route::get('{banner}/delete', [BannerController::class, 'delete'])->name('delete');
        Route::get('{banner}/disable', [BannerController::class, 'disable'])->name('disable');
    });

    Route::get('bulk', [BulkController::class, 'index'])->name('bulk');

    Route::prefix('categories')->as('categories.')->group(function () {
        Route::get('', [CategoryController::class, 'index'])->name('index');
        Route::post('', [CategoryController::class, 'store'])->name('store');
        Route::get('create', [CategoryController::class, 'create'])->name('create');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::patch('{category}', [CategoryController::class, 'update'])->name('update');
        Route::get('{category}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('sort', [CategoryController::class, 'sort'])->name('sort');
    });

    Route::prefix('channels')->as('channels.')->group(function () {
        Route::get('', [ChannelController::class, 'index'])->name('index');
        Route::post('', [ChannelController::class, 'store'])->name('store');
        Route::get('create', [ChannelController::class, 'create'])->name('create');
        Route::get('{channel}/edit', [ChannelController::class, 'edit'])->name('edit');
        Route::patch('{channel}', [ChannelController::class, 'update'])->name('update');
        Route::get('{channel}/delete', [ChannelController::class, 'destroy'])->name('destroy');
        Route::post('sort', [ChannelController::class, 'sort'])->name('sort');

        Route::get('home', [ChannelController::class, 'index'])->name('home');
        Route::get('discover', [ChannelController::class, 'index'])->name('discover');
        Route::get('radio', [ChannelController::class, 'index'])->name('radio');
        Route::get('community', [ChannelController::class, 'index'])->name('community');
        Route::get('trending', [ChannelController::class, 'index'])->name('trending');
        Route::get('{id}/genre', [ChannelController::class, 'index'])->name('genre');
        Route::get('{id}/station-category', [ChannelController::class, 'index'])->name('station-category');
        Route::get('{id}/podcast-category', [ChannelController::class, 'index'])->name('podcast-category');
    });

    Route::prefix('cities')->as('cities.')->group(function () {
        Route::get('', [CityController::class, 'index'])->name('index');
        Route::post('', [CityController::class, 'store'])->name('store');
        Route::get('create', [CityController::class, 'create'])->name('create');
        Route::get('{city}/edit', [CityController::class, 'edit'])->name('edit');
        Route::patch('{city}', [CityController::class, 'update'])->name('update');
        Route::get('{city}/delete', [CityController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('comments')->as('comments.')->group(function () {
        Route::get('', [CommentController::class, 'index'])->name('index');
        Route::get('approved', [CommentController::class, 'index'])->name('approved');
        Route::get('{comment}/edit', [CommentController::class, 'edit'])->name('edit');
        Route::patch('{comment}', [CommentController::class, 'update'])->name('update');
        Route::get('{comment}/delete', [CommentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('countries')->as('countries.')->group(function () {
        Route::get('', [CountryController::class, 'index'])->name('index');
        Route::post('', [CountryController::class, 'store'])->name('store');
        Route::get('create', [CountryController::class, 'create'])->name('create');
        Route::get('{country}/edit', [CountryController::class, 'edit'])->name('edit');
        Route::patch('{country}', [CountryController::class, 'update'])->name('update');
        Route::get('{country}/delete', [CountryController::class, 'destroy'])->name('destroy');

        Route::post('get-city', [CountryController::class, 'getCity'])->name('get.city');
    });

    Route::prefix('country-languages')->as('country.languages.')->group(function () {
        Route::get('', [CountryLanguageController::class, 'index'])->name('index');
        Route::post('', [CountryLanguageController::class, 'store'])->name('store');
        Route::get('create', [CountryLanguageController::class, 'create'])->name('create');
        Route::get('{language}/edit', [CountryLanguageController::class, 'edit'])->name('edit');
        Route::patch('{language}', [CountryLanguageController::class, 'update'])->name('update');
        Route::get('{language}/delete', [CountryLanguageController::class, 'destroy'])->name('destroy');

        Route::post('batch', [CountryLanguageController::class, 'batch'])->name('batch');
        Route::post('get-language', [CountryLanguageController::class, 'getLanguage'])->name('get.language');
    });

    Route::prefix('coupons')->as('coupons.')->group(function () {
        Route::get('', [CouponController::class, 'index'])->name('index');
        Route::post('', [CouponController::class, 'store'])->name('store');
        Route::get('create', [CouponController::class, 'create'])->name('create');
        Route::get('{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::patch('{coupon}', [CouponController::class, 'update'])->name('update');
        Route::get('{coupon}/delete', [CouponController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('email')->as('email.')->group(function () {
        Route::get('', [EmailController::class, 'index'])->name('index');
        Route::get('{email}/edit', [EmailController::class, 'edit'])->name('edit');
        Route::patch('{email}', [EmailController::class, 'update'])->name('update');
        Route::get('{email}/delete', [EmailController::class, 'delete'])->name('delete');
    });

    Route::prefix('episodes')->as('episodes.')->group(function () {
        Route::get('', [SongController::class, 'index'])->name('index');
        Route::post('', [SongController::class, 'batch'])->name('batch');
        Route::get('{song}/edit', [SongController::class, 'edit'])->name('edit');
        Route::post('{song}/edit', [SongController::class, 'update'])->name('edit.post');
        Route::get('{song}/delete', [SongController::class, 'destroy'])->name('destroy');
        Route::post('edit-title', [SongController::class, 'updateTitle'])->name('edit.title.post');
        Route::post('{song}/reject', [SongController::class, 'reject'])->name('edit.reject.post');
    });

    Route::prefix('genres')->as('genres.')->group(function () {
        Route::get('', [GenreController::class, 'index'])->name('index');
        Route::post('', [GenreController::class, 'store'])->name('store');
        Route::get('create', [GenreController::class, 'create'])->name('create');
        Route::get('{genre}/edit', [GenreController::class, 'edit'])->name('edit');
        Route::patch('{genre}', [GenreController::class, 'update'])->name('update');
        Route::get('{genre}/delete', [GenreController::class, 'destroy'])->name('destroy');
        Route::post('sort', [GenreController::class, 'sort'])->name('sort');
    });

    Route::prefix('helpers')->group(function () {
        Route::get('terminal/artisan', [TerminalController::class, 'artisan'])->name('help.terminal.artisan');
        Route::post('terminal/artisan', [TerminalController::class, 'runArtisan'])->name('help.terminal.artisan.post');
    });

    //    Route::prefix('languages')->as('languages.')->group(function () {
    //        Route::get('', [LanguageController::class, 'index'])->name('index');
    //        Route::post('', [LanguageController::class, 'store'])->name('store');
    //        Route::get('{language}/delete', [LanguageController::class, 'destroy'])->name('destroy');
    //
    //        Route::name('translations.')->group(function () {
    //            Route::get('{language}/translations', [TranslationController::class, 'show'])->name('show');
    //            Route::post('{language}/translations/create', [TranslationController::class, 'store'])->name('create');
    //            Route::post('{language}/translations', [TranslationController::class, 'update'])->name('update');
    //        });
    //    });

    Route::prefix('logs')->group(function () {
        Route::get('', [LogController::class, 'index'])->name('log-viewer-index');
        Route::get('{file?}', [LogController::class, 'index'])->name('log-viewer-file');
        Route::get('{file}/tail', [LogController::class, 'tail'])->name('log-viewer-tail');
    });

    Route::prefix('media')->as('media.')->group(function () {
        Route::get('', [MediaController::class, 'index'])->name('index');
        Route::get('download', [MediaController::class, 'download'])->name('download');
        Route::delete('delete', [MediaController::class, 'delete'])->name('delete');
        Route::put('move', [MediaController::class, 'move'])->name('move');
        Route::post('upload', [MediaController::class, 'upload'])->name('upload');
        Route::post('folder', [MediaController::class, 'newFolder'])->name('new-folder');
    });

    Route::prefix('metatags')->as('metatags.')->group(function () {
        Route::get('', [MetaTagController::class, 'index'])->name('index');
        Route::post('', [MetaTagController::class, 'store'])->name('store');
        Route::get('{metaTag}/edit', [MetaTagController::class, 'edit'])->name('edit');
        Route::post('{metaTag}', [MetaTagController::class, 'update'])->name('update');
        Route::get('{metaTag}/delete', [MetaTagController::class, 'destroy'])->name('destroy');
        Route::post('sort', [MetaTagController::class, 'sort'])->name('sort');
    });

    Route::prefix('orders')->as('orders.')->group(function () {
        Route::get('', [OrderController::class, 'index'])->name('index');
    });

    Route::prefix('pages')->as('pages.')->group(function () {
        Route::get('', [PageController::class, 'index'])->name('index');
        Route::post('', [PageController::class, 'store'])->name('store');
        Route::get('create', [PageController::class, 'create'])->name('create');
        Route::get('{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::patch('{page}', [PageController::class, 'update'])->name('update');
        Route::get('{page}/delete', [PageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('plans')->as('plans.')->group(function () {
        Route::get('', [PlanController::class, 'index'])->name('index');
        Route::post('', [PlanController::class, 'update'])->name('update');
    });

    Route::prefix('playlists')->as('playlists.')->group(function () {
        Route::get('', [PlaylistController::class, 'index'])->name('index');
        Route::post('', [PlaylistController::class, 'store'])->name('store');
        Route::get('{playlist}/edit', [PlaylistController::class, 'edit'])->name('edit');
        Route::patch('{playlist}', [PlaylistController::class, 'update'])->name('update');
        Route::get('{playlist}/delete', [PlaylistController::class, 'destroy'])->name('destroy');

        Route::post('batch', [PlaylistController::class, 'batch'])->name('batch');
        Route::get('search', [PlaylistController::class, 'search'])->name('search');

        Route::name('tracks.')->group(function () {
            Route::get('{playlist}/track-list', [PlaylistTrackController::class, 'show'])->name('show');
            Route::post('{playlist}/track-list', [PlaylistTrackController::class, 'batch'])->name('batch');
        });
    });

    Route::prefix('podcasts')->as('podcasts.')->group(function () {
        Route::get('', [PodcastController::class, 'index'])->name('index');
        Route::post('', [PodcastController::class, 'store'])->name('store');
        Route::get('create', [PodcastController::class, 'create'])->name('create');
        Route::get('{podcast}/edit', [PodcastController::class, 'edit'])->name('edit');
        Route::patch('{podcast}', [PodcastController::class, 'update'])->name('update');
        Route::get('{podcast}/delete', [PodcastController::class, 'destroy'])->name('destroy');

        Route::get('search', [PodcastController::class, 'search'])->name('search');

        Route::name('episodes.')->group(function () {
            Route::get('{podcast}/episodes', [PodcastEpisodeController::class, 'show'])->name('show');
            Route::get('{podcast}/upload', [PodcastEpisodeController::class, 'upload'])->name('upload');
            Route::post('{podcast}/episodes', [PodcastEpisodeController::class, 'batch'])->name('batch');
        });
    });

    Route::prefix('podcast-categories')->as('podcast-categories.')->group(function () {
        Route::get('', [PodcastCategoryController::class, 'index'])->name('index');
        Route::post('', [PodcastCategoryController::class, 'store'])->name('store');
        Route::get('create', [PodcastCategoryController::class, 'create'])->name('create');
        Route::get('{category}/edit', [PodcastCategoryController::class, 'edit'])->name('edit');
        Route::patch('{category}', [PodcastCategoryController::class, 'update'])->name('update');
        Route::get('{category}/delete', [PodcastCategoryController::class, 'destroy'])->name('destroy');
        Route::post('sort', [PodcastCategoryController::class, 'sort'])->name('sort');
    });

    Route::prefix('posts')->as('posts.')->group(function () {
        Route::get('', [PostController::class, 'index'])->name('index');
        Route::post('', [PostController::class, 'store'])->name('store');
        Route::get('create', [PostController::class, 'create'])->name('create');
        Route::get('{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::patch('{post}', [PostController::class, 'update'])->name('update');
        Route::get('{post}/delete', [PostController::class, 'destroy'])->name('destroy');
        Route::post('batch', [PostController::class, 'batch'])->name('batch');

        Route::prefix('media')->as('media.')->group(function () {
            Route::get('', [PostMediaController::class, 'index'])->name('index');
            Route::get('{file:post_id}', [PostMediaController::class, 'show'])->name('associated');
            Route::post('get', [PostMediaController::class, 'get'])->name('get');
            Route::post('delete', [PostMediaController::class, 'destroy'])->name('delete');
            Route::post('download', [PostMediaController::class, 'download'])->name('download');
            Route::post('upload', [PostMediaController::class, 'upload'])->name('upload');
        });
    });

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('', [ProfileController::class, 'index'])->name('index');
        Route::post('', [ProfileController::class, 'store'])->name('store');
    });

    Route::prefix('radios')->as('radios.')->group(function () {
        Route::get('', [RadioController::class, 'index'])->name('index');
        Route::post('', [RadioController::class, 'store'])->name('store');
        Route::get('create', [RadioController::class, 'create'])->name('create');
        Route::get('{radio}/edit', [RadioController::class, 'edit'])->name('edit');
        Route::patch('{radio}', [RadioController::class, 'update'])->name('update');
        Route::get('{radio}/delete', [RadioController::class, 'destroy'])->name('destroy');
        Route::post('sort', [RadioController::class, 'sort'])->name('sort');
    });

    Route::prefix('regions')->as('regions.')->group(function () {
        Route::get('', [RegionController::class, 'index'])->name('index');
        Route::post('', [RegionController::class, 'store'])->name('store');
        Route::get('create', [RegionController::class, 'create'])->name('create');
        Route::get('{region}/edit', [RegionController::class, 'edit'])->name('edit');
        Route::patch('{region}', [RegionController::class, 'update'])->name('update');
        Route::get('{region}/delete', [RegionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('', [ReportController::class, 'index'])->name('index');
        Route::post('', [ReportController::class, 'store'])->name('store');
    });

    Route::prefix('groups')->as('groups.')->group(function () {
        Route::get('', [GroupController::class, 'index'])->name('index');
        Route::post('', [GroupController::class, 'store'])->name('store');
        Route::get('{group}/edit', [GroupController::class, 'edit'])->name('edit');
        Route::patch('{group}', [GroupController::class, 'update'])->name('update');
        Route::get('{group}/delete', [GroupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('scheduling')->as('scheduling.')->group(function () {
        Route::get('', [SchedulingController::class, 'index'])->name('index');
        Route::post('run', [SchedulingController::class, 'run'])->name('run');
    });

    Route::prefix('services')->as('services.')->group(function () {
        Route::get('', [ServiceController::class, 'index'])->name('index');
        Route::post('', [ServiceController::class, 'store'])->name('store');
        Route::get('create', [ServiceController::class, 'create'])->name('create');
        Route::get('{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::patch('{service}', [ServiceController::class, 'update'])->name('update');
        Route::get('{service}/delete', [ServiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('', [SettingsController::class, 'index'])->name('index');
        Route::post('', [SettingsController::class, 'store'])->name('store');
    });

    Route::prefix('sitemap')->as('sitemap.')->group(function () {
        Route::get('', [SitemapController::class, 'index'])->name('index');
        Route::post('', [SitemapController::class, 'store'])->name('store');
    });

    Route::prefix('slideshow')->as('slideshow.')->group(function () {
        Route::get('', [SlideshowController::class, 'index'])->name('index');
        Route::post('', [SlideshowController::class, 'store'])->name('store');
        Route::get('create', [SlideshowController::class, 'create'])->name('create');
        Route::get('{slide}/edit', [SlideshowController::class, 'edit'])->name('edit');
        Route::patch('{slide}', [SlideshowController::class, 'update'])->name('update');
        Route::get('{slide}/delete', [SlideshowController::class, 'destroy'])->name('destroy');

        Route::post('sort', [SlideshowController::class, 'sort'])->name('sort');

        Route::get('home', [SlideshowController::class, 'index'])->name('home');
        Route::get('discover', [SlideshowController::class, 'index'])->name('discover');
        Route::get('radio', [SlideshowController::class, 'index'])->name('radio');
        Route::get('community', [SlideshowController::class, 'index'])->name('community');
        Route::get('trending', [SlideshowController::class, 'index'])->name('trending');
        Route::get('{id}/genre', [SlideshowController::class, 'index'])->name('genre');
        Route::get('{id}/station-category', [SlideshowController::class, 'index'])->name('station-category');
        Route::get('{id}/podcast-category', [SlideshowController::class, 'index'])->name('podcast-category');
    });

    Route::prefix('songs')->as('songs.')->group(function () {
        Route::get('search', [SongController::class, 'search'])->name('search');

        Route::get('', [SongController::class, 'index'])->name('index');
        Route::get('{songUuid}/edit', [SongController::class, 'edit'])->name('edit');
        Route::patch('{songUuid}', [SongController::class, 'update'])->name('update');
        Route::get('{songUuid}/delete', [SongController::class, 'destroy'])->name('destroy');
        Route::post('{songUuid}/reject', [SongController::class, 'reject'])->name('reject');
        Route::post('{songUuid}/title', [SongController::class, 'updateTitle'])->name('update.title');

        Route::post('batch', [SongController::class, 'batch'])->name('batch');
    });

    Route::prefix('stations')->as('stations.')->group(function () {
        Route::get('', [StationController::class, 'index'])->name('index');
        Route::post('', [StationController::class, 'store'])->name('store');
        Route::get('create', [StationController::class, 'create'])->name('create');
        Route::get('{station}/edit', [StationController::class, 'edit'])->name('edit');
        Route::patch('{station}', [StationController::class, 'update'])->name('update');
        Route::get('{station}/delete', [StationController::class, 'destroy'])->name('destroy');

        Route::get('search', [StationController::class, 'search'])->name('search');
    });

    Route::prefix('subscriptions')->as('subscriptions.')->group(function () {
        Route::get('', [SubscriptionController::class, 'index'])->name('index');
        Route::get('{subscription}/edit', [SubscriptionController::class, 'edit'])->name('edit');
        Route::patch('{subscription}', [SubscriptionController::class, 'update'])->name('update');
        Route::get('{subscription}/delete', [SubscriptionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('upgrade')->as('upgrade.')->middleware('role:admin_terminal')->group(function () {
        Route::get('', [UpgradeController::class, 'index'])->name('index');
        Route::post('', [UpgradeController::class, 'process'])->name('process');
    });

    Route::prefix('users')->as('users.')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('index');
        Route::post('', [UserController::class, 'store'])->name('store');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::get('{user:uuid}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('{user:uuid}', [UserController::class, 'update'])->name('update');
        Route::get('{user:uuid}/delete', [UserController::class, 'destroy'])->name('destroy');

        Route::get('search', [UserController::class, 'search'])->name('search');
        Route::post('batch', [UserController::class, 'batch'])->name('batch');
    });

    Route::prefix('withdraws')->as('withdraws.')->group(function () {
        Route::get('', [WithdrawController::class, 'index'])->name('index');
        Route::patch('{withdraw}', [WithdrawController::class, 'update'])->name('update');
    });
});
