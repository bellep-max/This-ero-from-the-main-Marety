<?php

namespace App\Models;

use App\Constants\RoleConstants;
use App\Helpers\Helper;
use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Traits\ArtworkTrait;
use App\Traits\FullTextSearch;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Episode extends Model implements HasMedia
{
    use ArtworkTrait;
    use FullTextSearch;
    use HasUuidTrait;
    use ImageMediaTrait;
    //    use SanitizedRequest;

    protected $fillable = [
        'uuid',
        'season',
        'number',
        'type',
        'user_id',
        'hls',
        'mp3',
        'podcast_id',
        'title',
        'description',
        'access',
        'explicit',
        'stream_url',
        'allow_comments',
        'comment_count',
        'allow_download',
        'download_count',
        'loves',
        'play_count',
        'failed_count',
        'duration',
        'is_visible',
        'approved',
        'pending',
        'plays',
    ];

    protected $appends = [
        'streamable',
        'artwork',
        'slug',
        'stream_url',
        'path',
    ];

    protected $casts = [
        'play_count' => 'integer',
        'is_visible' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_download' => 'boolean',
        'explicit' => 'boolean',
    ];

    protected $hidden = [
        'media',
        'description',
        'user',
    ];

    protected $searchable = [
        'title',
        'description',
    ];

    //    protected static function boot()
    //    {
    //        parent::boot();
    //        static::addGlobalScope(new VisibilityScope);
    //        static::addGlobalScope(new ApprovedScope);
    //        static::addGlobalScope(new PublishedScope);
    //    }

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable')->withoutGlobalScopes();
    }

    // GETTERS

    public function getSlugAttribute(): string
    {
        return Str::slug($this->podcast->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('podcast.episode', ['podcast' => $this->podcast_id, 'slug' => $this->slug, 'episode' => $this->id]);
    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && Love::query()
            ->where('user_id', auth()->id())
            ->where('loveable_id', $this->id)
            ->where('loveable_type', $this->getMorphClass())
            ->exists();
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

    public function getTimeAttribute(): string
    {
        return date('H:i:s', $this->duration);
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

    // MEDIA
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('audio')
            ->singleFile();
    }
}
