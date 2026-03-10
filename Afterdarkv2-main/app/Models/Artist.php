<?php

namespace App\Models;

use App\Constants\ActionConstants;
use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\PrivacyTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Artist extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use PrivacyTrait;

    protected $fillable = [
        'title',
        'artworkId',
        'bio',
        'name',
    ];

    protected $appends = [
        'artwork',
        'permalink',
        'slug',
    ];

    protected $hidden = [
        'media',
        'bio',
        'is_visible',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        //        static::addGlobalScope(new VisibilityScope());
    }

    // RELATIONS
    public function podcasts(): HasMany
    {
        return $this->hasMany(Podcast::class, 'artist_id', 'id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'love', 'loveable_id', 'user_id')
            ->where('loveable_type', self::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ArtistRequest::class);
    }

    public function activitiesTest(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'artist_album');
    }

    // GETTERS

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('artist.show', ['artist' => $this->id, 'slug' => $this->slug]);
    }

    public function getLovedAttribute(): bool
    {
        return auth()->user() && $this->morphOne(Love::class, 'loveable')
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && Love::query()
            ->where('user_id', auth()->id())
            ->where('loveable_id', $this->id)
            ->where('loveable_type', $this->getMorphClass())
            ->exists();
    }

    // todo REFACTOR THIS!

    public function similar(): Builder
    {
        return self::query()->whereIn('genre', explode(',', $this->genre));
    }

    public function activities(): Builder
    {
        return Activity::query()
            ->where('activityable_type', self::class)
            ->where('activityable_id', $this->id)
            ->where('action', '!=', ActionConstants::ADD_EVENT);
    }
}
