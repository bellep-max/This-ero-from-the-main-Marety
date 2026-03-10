<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Traits\ArtworkTrait;
use App\Traits\FullTextSearch;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\MediaLibrary\HasMedia;

class Podcast extends Model implements Feedable, HasMedia
{
    use ArtworkTrait;
    use FullTextSearch;
    use HasUuidTrait;
    use ImageMediaTrait;

    protected $fillable = [
        'uuid',
        'allow_comments',
        'allow_download',
        'approved',
        'artist_id',
        'comment_count',
        'country_id',
        'country_code',
        'description',
        'explicit',
        'language_id',
        'loves',
        'rss_feed_url',
        'title',
        'user_id',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
        'slug',
        'seasons',
        'details',
    ];

    protected $hidden = ['media'];

    protected $searchable = [
        'title',
        'description',
    ];

    protected $casts = [
        'allow_comments' => 'boolean',
        'allow_download' => 'boolean',
        'approved' => 'boolean',
        'explicit' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)
            ->orderBy('season')
            ->orderBy('number');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable')
            ->with('user')
            ->latest();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'love',
            'loveable_id',
            'user_id'
        )->where('loveable_type', self::class);
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PodcastCategory::class, 'podcast_podcast_category');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable')->withoutGlobalScopes();
    }

    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(Slide::class, 'slide_podcast');
    }

    // SCOPES
    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }

    // GETTERS
    //    public function getPermalinkAttribute(): string
    //    {
    //        return route('podcast.show', ['podcast' => $this->id, 'slug' => $this->slug]);
    //    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && $this->loves()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getSeasonsAttribute()
    {
        return $this->episodes()->max('season') ?? 1;
    }

    public function getDetailsAttribute(): array
    {
        $details = [];

        for ($i = 1; $i <= $this->seasons; $i++) {
            $details[] = [
                'season' => $i,
                'episodes' => $this->episodes()->where('season', $i)->count(),
            ];
        }

        return $details;
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary($this->description ?? 'No description')
            ->updated($this->updated_at)
            ->link($this->permalink)
            ->author($this->user->name);
    }
}
