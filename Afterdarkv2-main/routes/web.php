<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\Frontend\ActionController;
use App\Http\Controllers\Frontend\AdventureController;
use App\Http\Controllers\Frontend\AlbumController;
use App\Http\Controllers\Frontend\ArtistController;
use App\Http\Controllers\Frontend\ArtistManagementController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ChannelController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\DiscoverController;
use App\Http\Controllers\Frontend\DownloadController;
use App\Http\Controllers\Frontend\FollowerController;
use App\Http\Controllers\Frontend\FollowingController;
use App\Http\Controllers\Frontend\GenreController;
use App\Http\Controllers\Frontend\HomepageController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\Playlist\PlaylistCollaboratorController;
use App\Http\Controllers\Frontend\Playlist\PlaylistController;
use App\Http\Controllers\Frontend\Playlist\PlaylistSongController;
use App\Http\Controllers\Frontend\Podcast\PodcastController;
use App\Http\Controllers\Frontend\Podcast\PodcastEpisodeController;
use App\Http\Controllers\Frontend\Podcast\PodcastEpisodeUploadController;
use App\Http\Controllers\Frontend\Podcast\PodcastSeasonController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ReportController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ShareController;
use App\Http\Controllers\Frontend\SongController;
use App\Http\Controllers\Frontend\StreamController;
use App\Http\Controllers\Frontend\TermsController;
use App\Http\Controllers\Frontend\TrendingController;
use App\Http\Controllers\Frontend\Upload\AdventureHeadingUploadController;
use App\Http\Controllers\Frontend\Upload\AdventureRootUploadController;
use App\Http\Controllers\Frontend\Upload\TrackUploadController;
use App\Http\Controllers\Frontend\Upload\UploadController;
use App\Http\Controllers\Frontend\User\UserFavoritesController;
use App\Http\Controllers\Frontend\User\UserLinktreeController;
use App\Http\Controllers\Frontend\User\UserProfileController;
use App\Http\Controllers\Frontend\User\UserSubscriptionController;
use App\Http\Controllers\Settings\AccountController;
use App\Http\Middleware\Author;
use Illuminate\Support\Facades\Route;

Route::feeds();

Route::name('account.')->group(function () {
    Route::prefix('reset-password')->group(function () {
        Route::post('send', [AccountController::class, 'sendResetPassword'])->name('send.request.reset.password');
        Route::get('{token}', [AccountController::class, 'resetPassword'])->name('reset.password');
        Route::post('new-password', [AccountController::class, 'setNewPassword'])->name('set.new.password');
    });

    Route::get('email-verify/{code}', [AccountController::class, 'verifyEmail'])->name('verify');
});

// Route::prefix('album')->as('album.')->group(function () {
//    Route::get('{album}', [AlbumController::class, 'show'])->name('show');
//    Route::get('{id}/{slug}/related-albums', [AlbumController::class, 'related'])->name('related');
// });
//
// Route::prefix('artist')->as('artist.')->group(function () {
//    Route::get('{artist}/{slug}', [ArtistController::class, 'show'])->name('show');
//    Route::get('{artist}/{slug}/albums', [ArtistController::class, 'albums'])->name('albums');
//    Route::get('{artist}/{slug}/podcasts', [ArtistController::class, 'podcasts'])->name('podcasts');
//    Route::get('{artist}/{slug}/similar-artists', [ArtistController::class, 'similar'])->name('similar');
//    Route::get('{artist}/{slug}/followers', [ArtistController::class, 'followers'])->name('followers');
//    Route::get('{artist}/{slug}/events', [ArtistController::class, 'events'])->name('events');
// });
//
// Route::prefix('artist-management')->as('auth.user.artist.')->middleware(['auth'])->group(function () {
//    Route::get('', [ArtistManagementController::class, 'index'])->name('manager');
//    Route::post('withdraw', [ArtistManagementController::class, 'withdraw'])->name('withdraw');
//
//    Route::name('manager.')->group(function () {
//        Route::get('uploaded', [ArtistManagementController::class, 'uploaded'])->name('uploaded');
//        Route::get('events', [ArtistManagementController::class, 'events'])->name('events');
//        Route::get('profile', [ArtistManagementController::class, 'profile'])->name('profile');
//        Route::post('profile', [ArtistManagementController::class, 'saveProfile'])->name('profile.save');
//        Route::post('genres', [ArtistManagementController::class, 'genres'])->name('genres');
//        Route::post('categories', [ArtistManagementController::class, 'categories'])->name('categories ');
//        Route::post('countries', [ArtistManagementController::class, 'countries'])->name('countries');
//        Route::post('languages', [ArtistManagementController::class, 'languages'])->name('languages');
//
//        Route::prefix('albums')->group(function () {
//            Route::get('', [ArtistManagementController::class, 'albums'])->name('albums');
//            Route::post('create', [ArtistManagementController::class, 'createAlbum'])->name('albums.create');
//            Route::post('edit', [ArtistManagementController::class, 'editAlbum'])->name('albums.edit');
//            Route::post('delete', [ArtistManagementController::class, 'deleteAlbum'])->name('albums.delete');
//            Route::post('sort', [ArtistManagementController::class, 'sortAlbumSongs'])->name('albums.sort');
//            Route::get('{id}', [ArtistManagementController::class, 'showAlbum'])->name('albums.show');
//            Route::get('{id}/upload', [ArtistManagementController::class, 'uploadAlbum'])->name('albums.upload');
//            Route::post('{id}/upload', [ArtistManagementController::class, 'handleUpload'])->name('albums.upload.post');
//        });
//
//        Route::prefix('podcasts')->group(function () {
//            Route::get('', [ArtistManagementController::class, 'podcasts'])->name('podcasts');
//            Route::post('create', [ArtistManagementController::class, 'createPodcast'])->name('podcasts.create');
//            Route::post('import', [ArtistManagementController::class, 'importPodcast'])->name('podcasts.import');
//            Route::post('edit', [ArtistManagementController::class, 'editPodcast'])->name('podcasts.edit');
//            Route::post('delete', [ArtistManagementController::class, 'deletePodcast'])->name('podcasts.delete');
//            Route::post('sort', [ArtistManagementController::class, 'sortPodcastEpisodes'])->name('podcasts.sort');
//            Route::get('{id}', [ArtistManagementController::class, 'showPodcast'])->name('podcasts.show');
//            Route::get('{id}/upload', [ArtistManagementController::class, 'uploadPodcast'])->name('podcasts.upload');
//            Route::post('{id}/upload', [ArtistManagementController::class, 'handlePodcastUpload'])->name('podcasts.upload.post');
//            Route::post('episode/edit', [ArtistManagementController::class, 'editEpisode'])->name('episode.edit.post');
//            Route::post('episode/delete', [ArtistManagementController::class, 'deleteEpisode'])->name('episode.delete');
//        });
//
//        Route::prefix('song')->group(function () {
//            Route::post('edit', [ArtistManagementController::class, 'editSongPost'])->name('song.edit.post');
//            Route::post('delete', [ArtistManagementController::class, 'deleteSong'])->name('song.delete');
//        });
//
//        Route::prefix('event')->group(function () {
//            Route::post('create', [ArtistManagementController::class, 'createEvent'])->name('event.create');
//            Route::post('edit', [ArtistManagementController::class, 'editEvent'])->name('event.edit');
//            Route::post('delete', [ArtistManagementController::class, 'deleteEvent'])->name('event.delete');
//        });
//
//        Route::prefix('chart')->group(function () {
//            Route::post('overview', [ArtistManagementController::class, 'artistChart'])->name('chart.overview');
//            Route::post('song/{id}', [ArtistManagementController::class, 'songChart'])->name('chart.song');
//        });
//    });
// });

Route::prefix('posts')->as('posts.')->group(function () {
    Route::get('', [BlogController::class, 'index'])->name('index');
    Route::get('{post:uuid}', [BlogController::class, 'show'])->name('show');
});

Route::get('channels/{channel:alt_name}', [ChannelController::class, 'show'])->name('channels.show');

Route::post('comments', [CommentController::class, 'store'])->name('comments.store');

Route::prefix('genres')->as('genres.')->group(function () {
    Route::get('', [GenreController::class, 'index'])->name('index');
    Route::get('{genre:alt_name}', [GenreController::class, 'show'])->name('show');
});

Route::get('discover', [DiscoverController::class, 'index'])->name('discover.index');

Route::get('/', HomepageController::class)->name('home.index');

Route::prefix('podcasts')->as('podcasts.')->group(function () {
    Route::get('', [PodcastController::class, 'index'])->name('index');
    Route::post('', [PodcastController::class, 'store'])->name('store')
        ->middleware(['auth', PermissionEnum::UPLOAD_AUDIO->can()]);
    Route::get('{podcast}', [PodcastController::class, 'show'])->name('show');
    Route::get('{podcast}/edit', [PodcastController::class, 'edit'])->name('edit');
    Route::patch('{podcast}', [PodcastController::class, 'update'])->name('update');

    Route::get('{podcast}/seasons/{season}', [PodcastSeasonController::class, 'show'])->name('seasons.show');
});

Route::prefix('episodes')->as('episodes.')->group(function () {
    Route::get('{episode}', [PodcastEpisodeController::class, 'show'])->name('show');

    Route::middleware(Author::class)->group(function () {
        Route::get('{episode}/edit', [PodcastEpisodeController::class, 'edit'])->name('edit');
        Route::patch('{episode}', [PodcastEpisodeController::class, 'update'])->name('update');
    });
});

Route::get('download/post/{id}/attachment/{attachment-id}', [BlogController::class, 'downloadAttachment'])->name('post.download.attachment');

Route::prefix('downloads')->as('downloads.')->group(function () {
    Route::get('{type}/{uuid}', [DownloadController::class, 'download'])->name('download');
    Route::get('{type}/{uuid}/hd', [DownloadController::class, 'downloadHd'])->name('download.hd');
});

Route::prefix('songs')->as('songs.')->group(function () {
    Route::get('{song:uuid}', [SongController::class, 'show'])->name('show');
    Route::get('{song:uuid}/edit', [SongController::class, 'edit'])->name('edit');
    Route::patch('{song:uuid}', [SongController::class, 'update'])->name('update');
    Route::delete('{song:uuid}', [SongController::class, 'destroy'])->name('destroy');
    //
    //    Route::post('autoplay', [SongController::class, 'autoplay'])->name('autoplay.get');
    //
    //    Route::get('top-daily', [SongController::class, 'topDaily'])->name('top.daily');
    //    Route::get('top-weekly', [SongController::class, 'topWeekly'])->name('top.weekly');
});

Route::prefix('adventures')->as('adventures.')->group(function () {
    Route::get('', [AdventureController::class, 'index'])->name('index');
    Route::get('{adventure:uuid}', [AdventureController::class, 'show'])
        ->name('show');
    Route::get('{adventure:uuid}/edit', [AdventureController::class, 'edit'])
        ->middleware([
            'auth',
        ])
        ->name('edit');
    Route::patch('{adventure:uuid}', [AdventureController::class, 'update'])
        ->name('update');
    Route::delete('{adventure:uuid}', [AdventureController::class, 'destroy'])
        ->name('destroy');
});

Route::get('pages/{page:alt_name}', [PageController::class, 'show'])->name('pages.show');

Route::prefix('streams')->as('streams.')->group(function () {
    Route::prefix('mp3')->middleware('signed')->group(function () {
        Route::get('{uuid}/{type}', [StreamController::class, 'mp3'])->name('mp3');
        Route::get('{uuid}/{type}/hd', [StreamController::class, 'mp3HD'])->name('mp3.hd');
    });

    Route::prefix('hls')->group(function () {
        Route::get('{uuid}/{type}', [StreamController::class, 'hls'])->name('hls');
        Route::get('{uuid}/{type}/hd', [StreamController::class, 'hlsHD'])->name('hls.hd');
    });

    Route::get('youtube/{uuid}/{type}', [StreamController::class, 'youtube'])->name('youtube');

    Route::post('on-track-played', [StreamController::class, 'onTrackPlayed'])->name('track.played')
        ->middleware('GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:1000,1');
});

Route::get('share/embed/{theme}/{type}/{uuid}', [ShareController::class, 'embed'])->name('share.embed');

/**
 * Use ThrottleMiddleware to set limit the track stats user can post per minute
 * If admin leave 0 user can post 100 comment per minute.
 */
Route::prefix('trending')->as('trending.')->group(function () {
    Route::get('', [TrendingController::class, 'index'])->name('index');

    Route::get('genre/{genre}', [TrendingController::class, 'topByGenre'])->name('genre');
    Route::get('song', [TrendingController::class, 'topSongs'])->name('songs');
    Route::get('voice/{voice}', [TrendingController::class, 'topVoice'])->name('voices');

    Route::get('week', [TrendingController::class, 'index'])->name('week');
    Route::get('month', [TrendingController::class, 'index'])->name('month');
});

Route::middleware('auth')->group(function () {
    Route::prefix('upload')->as('upload.')->middleware(PermissionEnum::UPLOAD_AUDIO->can())->group(function () {
        Route::get('', UploadController::class)->name('index');

        Route::prefix('tracks')->as('tracks.')->group(function () {
            Route::post('', TrackUploadController::class)->name('store');
        });

        Route::prefix('adventures')->as('adventures.')->group(function () {
            Route::post('heading', [AdventureHeadingUploadController::class, 'store'])
                ->name('heading.store');

            Route::post('root', [AdventureRootUploadController::class, 'store'])
                ->name('root.store');

            Route::delete('{adventure:uuid}', [AdventureHeadingUploadController::class, 'destroy'])
                ->name('destroy')
                ->middleware([Author::class]);
        });

        Route::prefix('episodes')->as('episodes.')->group(function () {
            Route::post('', PodcastEpisodeUploadController::class)->name('store');
        });
        //        Route::post('', [UploadController::class, 'store'])->name('store');
        //        Route::post('beat/upload', [UploadController::class, 'uploadBeat'])->name('beat.post');
    });

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::prefix('linktree')->name('linktree.')->group(function () {
            Route::put('', [UserLinktreeController::class, 'update'])->name('update');
            Route::delete('', [UserLinktreeController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('users/{user:uuid}')->as('users.')->group(function () {
        Route::get('', [UserProfileController::class, 'show'])->name('show');

        Route::middleware(Author::class)->group(function () {
            Route::prefix('followers')->as('followers.')->group(function () {
                Route::get('', [FollowerController::class, 'show'])->name('show');
                Route::post('', [FollowerController::class, 'store'])->name('store');
                Route::delete('', [FollowerController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('following')->as('following.')->group(function () {
                Route::get('', [FollowingController::class, 'index'])->name('index');
                Route::post('', [FollowingController::class, 'store'])->name('store');
                Route::delete('', [FollowingController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('favorites')->as('favorites.')->group(function () {
                Route::get('', [UserFavoritesController::class, 'index'])->name('index');
                Route::post('', [UserFavoritesController::class, 'store'])->name('store');
                Route::delete('', [UserFavoritesController::class, 'destroy'])->name('destroy');
            });

            Route::get('notifications', [UserProfileController::class, 'notifications'])->name('notifications');
            Route::get('purchased', [UserProfileController::class, 'purchased'])->name('purchased');
        });

        Route::get('tracks', [UserProfileController::class, 'tracks'])->name('tracks');
        Route::get('adventures', [UserProfileController::class, 'adventures'])->name('adventures');
        Route::get('albums', [UserProfileController::class, 'albums'])->name('albums');
        Route::get('playlists', [UserProfileController::class, 'playlists'])->name('playlists');

        Route::get('podcasts', [UserProfileController::class, 'podcasts'])->name('podcasts');
        Route::get('podcasts/{podcast}/episodes', [UserProfileController::class, 'episodes'])->name('podcasts.episodes');

        Route::get('feed', [ProfileController::class, 'feed'])->name('feed');
        Route::get('posts/{id}', [ProfileController::class, 'posts'])->name('posts');
        Route::get('collection', [ProfileController::class, 'collection'])->name('collection');
        Route::get('playlists/subscribed', [ProfileController::class, 'subscribed'])->name('playlists.subscribed');

        Route::get('now-playing', [ProfileController::class, 'now_playing'])->name('now_playing');
        Route::post('now-playing', [ProfileController::class, 'now_playing'])->name('now_playing.post');

        Route::prefix('subscription')->as('subscriptions.')->group(function () {
            Route::post('checkout', [UserSubscriptionController::class, 'checkout'])->name('checkout');
            Route::get('success', [UserSubscriptionController::class, 'success'])->name('success');
            Route::get('cancel', [UserSubscriptionController::class, 'checkoutCancel'])->name('checkout-cancel');
            Route::post('suspend', [UserSubscriptionController::class, 'suspend'])->name('suspend');
            Route::post('activate', [UserSubscriptionController::class, 'activate'])->name('activate');
            Route::post('cancel', [UserSubscriptionController::class, 'cancel'])->name('cancel');
        });
    });

    Route::prefix('playlists')->as('playlists.')->group(function () {
        Route::post('', [PlaylistController::class, 'store'])->name('store');
        Route::get('{playlist:uuid}', [PlaylistController::class, 'show'])->name('show');
        Route::get('{playlist:uuid}/edit', [PlaylistController::class, 'edit'])->name('edit');
        Route::patch('{playlist:uuid}', [PlaylistController::class, 'update'])->name('update');
        Route::delete('{playlist:uuid}', [PlaylistController::class, 'destroy'])->name('destroy');

        Route::prefix('{playlist:uuid}/songs')->as('songs.')->group(function () {
            Route::post('', [PlaylistSongController::class, 'store'])->name('store');
            Route::delete('{song:uuid}', [PlaylistSongController::class, 'destroy'])->name('destroy');
        });

        Route::post('{playlist:uuid}/collaborators', [PlaylistCollaboratorController::class, 'store'])->name('collaborators.store');
        Route::post('{playlist:uuid}/collaborators/{user:uuid}/response', [PlaylistCollaboratorController::class, 'response'])
            ->withoutScopedBindings()
            ->name('collaborators.response');

        Route::post('{playlist:uuid}/collab', [PlaylistController::class, 'setCollab'])->name('collab.set');

        //        Route::get('{playlist}', [PlaylistController::class, 'subscribers'])->name('subscribers');
        //        Route::get('{playlist}/{slug}/collaborators', [PlaylistController::class, 'collaborators'])->name('collaborators');
    });

    Route::get('search', [SearchController::class, 'show'])->name('search.show');
    Route::post('report', ReportController::class)->name('report.store');
    Route::post('{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    //    Route::prefix('auth/user')->group(function () {
    //
    //        Route::name('auth.user.')->group(function () {
    //            Route::post('notifications', [AuthController::class, 'notifications'])->name('notifications');
    //
    //            Route::post('notification-count', [AuthController::class, 'notificationCount'])->name('notification.count');
    //            Route::post('favorite', [AuthController::class, 'favorite'])->name('favorite');
    //            Route::post('song/favorite', [AuthController::class, 'songFavorite'])->name('song.favorite');
    //
    //            Route::post('library', [AuthController::class, 'library'])->name('library');
    //            Route::post('song/library', [AuthController::class, 'songLibrary'])->name('song.library');
    //
    //            Route::post('playlists', [AuthController::class, 'playlists'])->name('playlists');
    //            Route::post('playlists/subscribed', [AuthController::class, 'subscribed'])->name('playlists.subscribed');
    //
    //            Route::prefix('playlist')->group(function () {
    //                Route::post('createPlaylist', [AuthController::class, 'createPlaylist'])->name('create.playlist');
    //                Route::post('delete', [AuthController::class, 'deletePlaylist'])->name('playlist.delete');
    //                Route::post('edit', [AuthController::class, 'editPlaylist'])->name('playlist.edit');
    //                Route::post('addToPlaylist', [AuthController::class, 'addToPlaylist'])->name('playlist.add.item');
    //                Route::post('removeFromPlaylist', [AuthController::class, 'removeFromPlaylist'])->name('playlist.remove.item');
    //                Route::post('managePlaylist', [AuthController::class, 'managePlaylist'])->name('playlist.manage');
    //
    //                Route::prefix('collaboration')->group(function () {
    //                    Route::post('set', [AuthController::class, 'setPlaylistCollaboration'])->name('playlist.collaboration.set');
    //                    Route::post('invite', [AuthController::class, 'collaborativePlaylist'])->name('playlist.collaboration.invite');
    //                    Route::post('accept', [AuthController::class, 'collaborativePlaylist'])->name('playlist.collaboration.accept');
    //                    Route::post('cancel', [AuthController::class, 'collaborativePlaylist'])->name('playlist.collaboration.cancel');
    //                });
    //            });
    //
    //            Route::post('removeActivity', [AuthController::class, 'removeActivity'])->name('removeActivity');
    //            Route::post('artistClaim', [AuthController::class, 'artistClaim'])->name('artistClaim');
    //            Route::post('checkRole', [AuthController::class, 'checkRole'])->name('role');
    //            Route::post('subscription/cancel', [AuthController::class, 'cancelSubscription'])->name('cancel.subscription');
    //            Route::post('get-mention', [AuthController::class, 'getMention'])->name('get.mention');
    //            Route::post('get-hashtag', [AuthController::class, 'getHashTag'])->name('get.hashtag');
    //            Route::post('post-feed', [AuthController::class, 'postFeed'])->name('post.feed');
    //            Route::post('report', [AuthController::class, 'report'])->name('report');
    //
    //            Route::prefix('podcast')->group(function () {
    //                Route::post('createPodcast', [AuthController::class, 'createPodcast'])->name('create.podcast');
    //                Route::patch('{podcastVisible}/updatePodcast', [AuthController::class, 'updatePodcast'])->name('update.podcast');
    //            });
    //
    //            Route::post('delete-notification', [AuthController::class, 'deleteNotification'])->name('notification.destroy');
    //        });
    //    });

    //    Route::get('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    //
    //
    //    Route::prefix('upload-episode')->as('auth.episode-upload.')->group(function () {
    //        Route::post('info', [PodcastEpisodeUploadController::class, 'store'])->name('info');
    //        Route::get('get-info/{id?}', [PodcastEpisodeUploadController::class, 'getInfo'])->name('get-info');
    //        Route::post('{episode?}/artwork', [PodcastEpisodeUploadController::class, 'uploadArtwork'])->name('artwork.store');
    //        Route::post('track', [PodcastEpisodeUploadController::class, 'uploadBeat'])->name('track.store');
    //    });
    //
    //    Route::get('adventure-diagram/{id?}', [AdventureUploadController::class, 'adventureDiagram'])->name('diagram');
    //
    //    Route::post('/bulk-download', [SongController::class, 'bulkDownload'])->name('auth.bulk-download.download-all-files');
    //    Route::post('/delete-profile', [AccountController::class, 'deleteProfile'])->name('auth.delete-profile.download-all-files');
    //    Route::get('/view/{username}', [NewProfileController::class, 'viewProfile'])->where('username', '[A-Za-z0-9]+')->name('user.view_profile');
});

Route::post('accept', TermsController::class)->name('terms.accept');
Route::post('action', ActionController::class)->name('actions.store');

require __DIR__ . '/settings.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
