<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Adventure;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Channel;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Event;
use App\Models\Love;
use App\Models\Order;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Post;
use App\Models\Song;
use App\Models\Station;
use App\Models\MESubscription;
use App\Models\User;
use App\Models\MEUserSubscription;
use App\Observers\ActivityObserver;
use App\Observers\AdventureObserver;
use App\Observers\AlbumObserver;
use App\Observers\ArtistObserver;
use App\Observers\CommentObserver;
use App\Observers\EpisodeObserver;
use App\Observers\EventObserver;
use App\Observers\LoveObserver;
use App\Observers\OrderObserver;
use App\Observers\PlaylistObserver;
use App\Observers\PodcastObserver;
use App\Observers\PostObserver;
use App\Observers\SongObserver;
use App\Observers\StationObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\UserObserver;
use App\Observers\UserSubscriptionObserver;
use App\Policies\AdventurePolicy;
use App\Policies\ChannelPolicy;
use App\Policies\PlaylistPolicy;
use App\Policies\PodcastEpisodePolicy;
use App\Policies\PodcastPolicy;
use App\Policies\SongPolicy;
use App\Policies\SubscriptionPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event as IlluminateEvent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Spotify\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Activity::observe(ActivityObserver::class);
        Adventure::observe(AdventureObserver::class);
        Album::observe(AlbumObserver::class);
        Artist::observe(ArtistObserver::class);
        Comment::observe(CommentObserver::class);
        Episode::observe(EpisodeObserver::class);
        Event::observe(EventObserver::class);
        Love::observe(LoveObserver::class);
        Order::observe(OrderObserver::class);
        Playlist::observe(PlaylistObserver::class);
        Podcast::observe(PodcastObserver::class);
        Post::observe(PostObserver::class);
        Song::observe(SongObserver::class);
        Station::observe(StationObserver::class);
        MESubscription::observe(SubscriptionObserver::class);
        User::observe(UserObserver::class);
        MEUserSubscription::observe(UserSubscriptionObserver::class);
        Paginator::useBootstrap();

        JsonResource::withoutWrapping();

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Gate::policy(MESubscription::class, SubscriptionPolicy::class);
        Gate::policy(Playlist::class, PlaylistPolicy::class);
        Gate::policy(Song::class, SongPolicy::class);
        Gate::policy(Channel::class, ChannelPolicy::class);
        Gate::policy(Podcast::class, PodcastPolicy::class);
        Gate::policy(Episode::class, PodcastEpisodePolicy::class);
        Gate::policy(Adventure::class, AdventurePolicy::class);

        IlluminateEvent::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('spotify', Provider::class);
        });
    }
}
