<?php

namespace App\Models;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Constants\SubscriptionStatusConstants;
use App\Constants\TypeConstants;
use App\Enums\AdventureSongTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\UserGenderEnum;
use App\Scopes\VisibilityScope;
use App\Traits\ArtworkTrait;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use ArtworkTrait;
    use HasApiTokens;
    use HasRoles;
    use HasUuidTrait;
    use ImageMediaTrait;
    use Notifiable;

    public $incrementing = true;

    protected $fillable = [
        'uuid',
        'activity_privacy',
        'allow_comments',
        'artist_id',
        'balance',
        'bio',
        'birth',
        'comment_count',
        'country_id',
        'crossfade_amount',
        'email',
        'email_verified',
        'email_verified_at',
        'email_verified_code',
        'gender',
        'hd_streaming',
        'last_activity',
        'last_seen_notif',
        'linktree_link',
        'logged_ip',
        'name',
        'notif_features',
        'notif_follower',
        'notif_playlist',
        'notif_shares',
        'notification',
        'password',
        'payment_bank',
        'payment_method',
        'payment_paypal',
        'persist_shuffle',
        'play_pause_fade',
        'disable_player_shortcuts',
        'remember_token',
        'restore_queue',
        'session_id',
        'trialed',
        'username',
        'group_id',
        'ends_at',
        'description,'
    ];

    protected $appends = [
        'artwork',
        //        'permalink',
        //        'signed_permalink',
        'total_plays',
        'slug',
    ];

    protected $hidden = [
        'media',
        'location',
        'email',
        'password',
        'remember_token',
        'balance',
        'credit',
        'email_verified_at',
        'last_seen_notif',
        'logged_ip',
        'gender',
        'birth',
        'birthyear',
        'city',
        'country_id',
        'activity_privacy',
        'created_at',
        'updated_at',
        'restore_queue',
        'persist_shuffle',
        'play_pause_fade',
        'crossfade_amount',
        'notif_follower',
        'notif_playlist',
        'notif_shares',
        'notif_features',
        'email_verified',
        'email_verified_code',
        'hd_streaming',
        'can_stream_high_quality',
        'can_upload',
        'trialed',
        'payment_method',
        'payment_paypal',
        'payment_bank',
    ];

    protected $casts = [
        'birth' => 'date',
        'last_activity' => 'datetime',
        'gender' => UserGenderEnum::class,
        'restore_queue' => 'boolean',
        'allow_comments' => 'boolean',
        'play_pause_fade' => 'boolean',
        'disable_player_shortcuts' => 'boolean',
    ];

    // RELATIONS

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class);
    }

    public function artist(): HasOne
    {
        return $this->hasOne(Artist::class, 'id', 'artist_id');
    }

    public function artistRequests(): HasMany
    {
        return $this->hasMany(ArtistRequest::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MESubscription::class)->latest();
    }

    public function userSubscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            MEUserSubscription::class,
            'user_id',
            'subscribed_user_id',
            'id',
            'id',
        )
            ->withPivot('subscription_id', 'status', 'last_payment_date', 'next_billing_date', 'amount', 'currency')
            ->latest();
    }

    public function activeUserSubscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            MEUserSubscription::class,
            'user_id',
            'subscribed_user_id',
            'id',
            'id',
        )
            ->wherePivotIn('status', [SubscriptionStatusEnum::Active, SubscriptionStatusEnum::Suspended])
            ->latest();
    }

    public function patrons(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            MEUserSubscription::class,
            'subscribed_user_id',
            'user_id',
            'id',
            'id',
        )
            ->latest();
    }

    public function activePatrons(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            MEUserSubscription::class,
            'subscribed_user_id',
            'user_id',
            'id',
            'id',
        )
            ->wherePivotIn('status', [SubscriptionStatusEnum::Active, SubscriptionStatusEnum::Suspended])
            ->latest();
    }

    public function connects(): HasMany
    {
        return $this->hasMany(Connect::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function adventures(): HasMany
    {
        return $this->hasMany(Adventure::class);
    }

    public function adventureHeaders(): HasMany
    {
        return $this->hasMany(Adventure::class)->where('type', AdventureSongTypeEnum::Heading);
    }

    public function podcasts(): HasMany
    {
        return $this->hasMany(Podcast::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    public function myPlaylists(): HasMany
    {
        return $this->hasMany(Playlist::class)->withoutGlobalScopes([VisibilityScope::class]);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)
            ->where('action', '!=', ActionConstants::ADD_EVENT);
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class)->with(TypeConstants::SONG);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function followers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'loveable', 'love');
    }

    public function freeFollowers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'loveable', 'love')
            ->whereDoesntHave('activeUserSubscriptions', function ($query) {
                $query->where('subscribed_user_id', $this->id);
            });
    }

    public function following(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'loveable', 'love');
    }

    public function lovedSongs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, 'loveable', 'love');
    }

    public function loves(): HasMany
    {
        return $this->hasMany(Love::class, 'user_id', 'id');
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function notificationsTest(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            Collaborator::class,
            'friend_id',
            'id',
            'user_id',
            'id',
        );
    }

    public function collaboratedPlaylists(): BelongsToMany
    {
        return $this->belongsToMany(
            Playlist::class,
            Collaborator::class,
            'friend_id',
            'playlist_id',
            'id',
            'id',
        );
    }

    public function approvedCollaboratedPlaylists(): BelongsToMany
    {
        return $this->collaboratedPlaylists()->wherePivot('approved', DefaultConstants::TRUE);
    }

    // GETTERS

    public function getSlugAttribute(): string
    {
        return Str::slug($this->username) ?: 'no-slug';
    }

    //    public function getPermalinkAttribute(): string
    //    {
    //        return route('user.show', $this->slug);
    //    }
    //
    //    public function getSignedPermalinkAttribute(): string
    //    {
    //        return URL::signedRoute('user.show', $this->slug);
    //    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && Love::query()
            ->where('user_id', auth()->id())
            ->where('loveable_id', $this->id)
            ->where('loveable_type', User::class)
            ->exists();
    }

    public function getCanStreamHighQualityAttribute(): bool
    {
        return $this->hd_streaming && Group::getValue('option_hd_stream');
    }

    public function getCanUploadAttribute(): bool
    {
        return (bool) Group::getValue('artist_allow_upload');
    }

    public function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getSongCountAttribute(): int
    {
        return $this->songs()->count();
    }

    public function getAlbumCountAttribute(): int
    {
        return $this->albums()->count();
    }

    public function getCommentCountAttribute(): int
    {
        return $this->comments()->count();
    }

    public function getPurchasedCountAttribute(): int
    {
        return $this->orders()->count() + $this->userSubscriptions()->count();
    }

    public function getNotificationCountAttribute(): int
    {
        $count = 0;
        $count += Activity::query()
            ->leftJoin('love', 'activities.activityable_id', '=', 'love.loveable_id')
            ->select(['activities.*', 'love.user_id as host_id'])
            ->where('love.loveable_type', Playlist::class)
            ->where('activities.action', '!=', ActionConstants::FOLLOW_PLAYLIST)
            ->where('activities.action', '!=', ActionConstants::FOLLOW_USER)
            ->where('activities.action', '!=', ActionConstants::PLAY_SONG)
            ->where('activities.action', '!=', ActionConstants::POST_FEED)
            ->where('love.user_id', $this->id)
            ->where('activities.created_at', '>', $this->last_seen_notif)
            ->count();

        $count += Activity::query()
            ->leftJoin('love', 'activities.activityable_id', '=', 'love.loveable_id')
            ->select(['activities.*', 'love.user_id as host_id'])
            ->where('activities.action', '!=', ActionConstants::FOLLOW_ARTIST)
            ->where('activities.action', '!=', ActionConstants::POST_FEED)
            ->where('love.loveable_type', Artist::class)
            ->where('love.user_id', $this->id)
            ->where('activities.created_at', '>', $this->last_seen_notif)
            ->where(function ($query) {
                $query->where('action', ActionConstants::ADD_SONG)
                    ->orWhere('action', ActionConstants::ADD_EVENT);
            })
            ->count();

        return $count + $this->notificationsTest()
            ->where('created_at', '>', $this->last_seen_notif)
            ->count();
    }

    public function getTotalPlaysAttribute(): int
    {
        return $this->tracks()->withoutGlobalScopes()->sum('referral_plays');
    }

    public function getHasAudioAttribute(): bool
    {
        return $this->adventureHeaders()->exists() ||
            $this->tracks()->exists() ||
            $this->podcasts()->exists();
    }

    // todo REFACTOR

    public function activeSubscription(bool $showSuspended = true): ?MESubscription
    {
        return $this->subscriptions()
            ->whereIn('status', array_filter([SubscriptionStatusConstants::ACTIVE, $showSuspended ? SubscriptionStatusConstants::SUSPENDED : null]))
            ->first();
    }

    public function activeUserSubscription(int $userId, bool $showSuspended = true): ?Model
    {
        return $this->userSubscriptions()
            ->where('subscribed_user_id', $userId)
            ->whereIn('status', array_filter([SubscriptionStatusConstants::ACTIVE, $showSuspended ? SubscriptionStatusConstants::SUSPENDED : null]))
            ->first();
    }

    public function hasActiveSubscription(bool $showSuspended = true): bool
    {
        return $this->subscriptions()
            ->whereIn('status', array_filter([SubscriptionStatusConstants::ACTIVE, $showSuspended ? SubscriptionStatusConstants::SUSPENDED : null]))
            ->exists();
    }

    //    public function assignRole($roleId): RoleUser
    //    {
    //        return RoleUser::query()
    //            ->updateOrCreate([
    //                'user_id' => $this->id,
    //            ], [
    //                'role_id' => $roleId,
    //            ]);
    //    }

    public function collection(): Builder
    {
        return Song::query()
            ->leftJoin('collections', (new Song)->getTable() . '.id', '=', 'collections.collectionable_id')
            ->select([(new Song)->getTable() . '.*', 'collections.user_id as host_id'])
            ->where('collections.user_id', $this->id)
            ->where('collections.collectionable_type', Song::class);
    }

    public function loved(): Builder
    {
        return Song::query()
            ->leftJoin('love', (new Song)->getTable() . '.id', '=', 'love.loveable_id')
            ->select([(new Song)->getTable() . '.*', 'love.user_id as host_id'])
            ->where('love.user_id', $this->id)
            ->where('love.loveable_type', Song::class);
    }

    public function favoriteArtists(): HasMany
    {
        return $this->loves()
            ->where('loveable_type', Artist::class)
            ->leftJoin((new Artist)->getTable(), (new Love)->getTable() . '.loveable_id', '=', (new Artist)->getTable() . '.id');
    }

    public function feed(): Builder
    {
        return Activity::query()
            ->leftJoin('love', 'activities.user_id', '=', 'love.loveable_id')
            ->select(['activities.*', 'love.user_id as host_id'])
            ->where('love.loveable_type', self::class)
            ->where('love.user_id', $this->id);
    }

    //    public function recent(): Builder
    //    {
    //        return Song::query()
    //            ->leftJoin('histories', (new Song)->getTable().'.id', '=', 'histories.historyable_id')
    //            ->select([(new Song)->getTable().'.*', 'histories.user_id as host_id'])
    //            ->where('histories.user_id', $this->id)
    //            ->where('histories.historyable_type', Song::class);
    //    }

    public function communitySongs(): Builder
    {
        return Song::query()
            ->leftJoin('histories', (new Song)->getTable() . '.id', '=', 'histories.historyable_id')
            ->leftJoin('love', 'histories.user_id', '=', 'love.loveable_id')
            ->select([(new Song)->getTable() . '.*', 'histories.user_id as host_id', 'love.user_id as love_id'])
            ->where('histories.historyable_type', Song::class)
            ->where('love.user_id', $this->id);
    }

    public function obsessed(): Builder
    {
        return Song::query()
            ->leftJoin('histories', (new Song)->getTable() . '.id', '=', 'histories.historyable_id')
            ->select([(new Song)->getTable() . '.*', 'histories.user_id as host_id'])
            ->where('histories.user_id', $this->id)
            ->where('histories.historyable_type', Song::class)
            ->where('histories.interaction_count', '>', 3)
            ->orderBy('histories.interaction_count', 'desc');
    }

    // todo REFACTOR THIS
    //    public function notifications(): Collection
    //    {
    //        $notifications = new Collection;
    //        $notifications = $notifications->merge();
    //
    //        $notifications = $notifications->merge($this->hasMany(Notification::class)->paginate(10));
    //
    //        return $notifications->sortByDesc('created_at');
    //    }

    public function collaborations(): Builder
    {
        return Playlist::query()
            ->leftJoin('collaborators', (new Playlist)->getTable() . '.id', '=', 'collaborators.playlist_id')
            ->select([(new Playlist)->getTable() . '.*', 'collaborators.user_id as host_id'])
            ->where('collaborators.friend_id', $this->id)
            ->where('collaborators.approved', DefaultConstants::TRUE);
    }

    public function subscribed(): Builder
    {
        return Playlist::query()
            ->leftJoin('love', (new Playlist)->getTable() . '.id', '=', 'love.loveable_id')
            ->select([(new Playlist)->getTable() . '.*', 'love.user_id as host_id'])
            ->where('love.user_id', $this->id)
            ->where('love.loveable_type', Playlist::class);
    }
}
