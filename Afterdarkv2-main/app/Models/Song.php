<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Constants\RoleConstants;
use App\Enums\ActivityTypeEnum;
use App\Enums\VocalGenderEnum;
use App\Helpers\Helper;
use App\Scopes\ConvertedScope;
use App\Services\TimeService;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Song extends Model implements HasMedia
{
    use HasUuidTrait;
    use ImageMediaTrait;

    protected $fillable = [
        'uuid',
        'mp3',
        'waveform',
        'preview',
        'wav',
        'flac',
        'hd',
        'hls',
        'file_url',
        'user_id',
        'selling',
        'price',
        'genre',
        'mood',
        'title',
        'description',
        'access',
        'duration',
        'artistIds',
        'loves',
        'collectors',
        'plays',
        'referral_plays',
        'published_at',
        'released_at',
        'copyright',
        'allow_download',
        'allow_comments',
        'download_count',
        'comment_count',
        'is_visible',
        'is_explicit',
        'private', // todo remove this field?
        'approved',
        'pending',
        'vocal_id',
        'script',
        'is_done',
        'is_done_wasabi',
        'is_adventure', // todo remove this field after migration to another model
        'is_patron',
        'liner_notes',
    ];

    protected $appends = [
        'artwork',
        'stream_url',
        'path',
        'favorite',
        'library',
        'streamable',
    ];

    protected $hidden = [
        'media',
        //        'artistIds',
        'user',
        'preview',
        'file_url',
    ];

    protected $casts = [
        'released_at' => 'datetime',
        'published_at' => 'datetime',
        'is_done' => 'boolean',
        'is_done_wasabi' => 'boolean',
        'is_patron' => 'boolean',
        'approved' => 'boolean',
        'pending' => 'boolean',
        'is_visible' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_download' => 'boolean',
        'is_explicit' => 'boolean',
        'selling' => 'boolean',
        'hd' => 'boolean',
        'preview' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ConvertedScope);
    }

    // RELATIONS

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'historyable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable')->withoutGlobalScopes();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function log(): HasOne
    {
        return $this->hasOne(SongLog::class);
    }

    //    public function tags(): HasMany
    //    {
    //        return $this->hasMany(SongTag::class);
    //    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function adventureChildSongs(): HasMany
    {
        return $this->hasMany(ChildSong::class, 'song_id')->withoutGlobalScopes();
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function fans(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'loveable', 'love', 'loveable_id', 'user_id')
            ->where('loveable_type', self::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_songs', 'song_id', 'playlist_id');
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'album_songs', 'song_id', 'album_id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function collections(): MorphMany
    {
        return $this->morphMany(Collection::class, 'collectionable');
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function vocal(): BelongsTo
    {
        return $this->belongsTo(Vocal::class, 'vocal_id');
    }

    public function slides(): MorphMany
    {
        return $this->morphMany(Slide::class, 'object');
    }

    // SCOPES

    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }

    public function scopeFemale($query)
    {
        return $query->whereHas('vocal', function ($query) {
            $query->where('name', VocalGenderEnum::FemaleName);
        });
    }

    public function scopeMale($query)
    {
        return $query->whereHas('vocal', function ($query) {
            $query->where('name', VocalGenderEnum::MaleName);
        });
    }

    public function scopePatron($query)
    {
        return $query->where('is_patron', true);
    }

    public function scopeNotPatron($query)
    {
        return $query->where('is_patron', false);
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    // GETTERS

    public function getArtworkAttribute(): string
    {
        if (!$media = $this->getFirstMedia('artwork')) {
            if (config('settings.automate')) {
                $row = $this->log();

                return isset($row->id)
                    ? $row->artwork
                    : asset('/assets/images/song.png');
            } else {
                return asset('/assets/images/song.png');
            }
        }

        //        return $media->getTemporaryUrl(Carbon::now()->addMinutes(intval(config('settings.s3_signed_time', 5))), 'lg');

        return $media->getTemporaryUrl(
            now()->addMinutes(config('settings.s3_signed_time', 5)), 'lg'
        );
    }

    //    public function getArtistsAttribute(): array
    //    {
    //        // $this->artistIds = Artist::whereIn('id', explode(',', $this->artistIds))->orderBy(DB::raw('FIELD(id, ' .  $this->artistIds. ')', 'FIELD'))->get() ?? array();
    //        // return $this->artistIds;
    //        return [];
    //    }

    public function getMinutesAttribute(): string
    {
        return TimeService::getHumanReadableTime($this->duration);
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('songs.show', $this->uuid);
    }

    public function getSignedPermalinkAttribute(): string
    {
        return URL::signedRoute('songs.show', $this->uuid);
    }

    public function getStreamableAttribute(): bool
    {
        if (isset($this->access)) {
            $options = Helper::groupPermission($this->access);

            if ($this->access && isset($options[Group::groupId()])) {
                return $options[Group::groupId()] < RoleConstants::USER_SUBSCRIPTION;
            }
        }

        return (bool) Group::getValue('option_stream');
    }

    public function getStreamUrlAttribute(): string
    {
        return $this->hls
            ? route('streams.hls', [
                'uuid' => $this->uuid,
                'type' => self::class,
            ])
            : URL::temporarySignedRoute('streams.mp3', now()->addDay(), [
                'uuid' => $this->uuid,
                'type' => self::class,
            ]);
    }

    public function getPathAttribute(): string
    {
        if (!$media = $this->getFirstMedia('audio')) {
            return '';
        }

        if (config('filesystems.disks')[$media->disk]['driver'] == 'local') {
            return $media->getPath();
        }

        return $media->getTemporaryUrl(
            now()->addMinutes(config('settings.s3_signed_time', 5))
        );
    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && $this->loves()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getPurchasedAttribute(): bool
    {
        return auth()->check() && $this->selling && $this->orders()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getLibraryAttribute(): bool
    {
        return auth()->check() && $this->collections()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getAlbumAttribute(): ?Album
    {
        return Album::query()
            ->leftJoin('album_songs', 'album_songs.album_id', '=', 'albums.id')
            ->select('albums.*', 'album_songs.id AS host_id')
            ->where('album_songs.song_id', '=', $this->id)
            ->first();
    }

    public function getSalesAttribute(): int
    {
        return $this->orders()
            ->groupBy('amount')
            ->count();
    }

    public function getTotalPlaysAttribute(): int
    {
        return $this->activities()
            ->where('action', ActivityTypeEnum::playSong)
            ->count();
    }
}
