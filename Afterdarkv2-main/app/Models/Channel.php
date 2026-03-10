<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Channel extends Model
{
    private const GENRE_RELATION_NAME = 'channelable';

    //    use SanitizedRequest;

    protected $fillable = [
        'user_id',
        'priority',
        'title',
        'description',
        'alt_name',
        'object_ids',
        'type',
        'meta_title',
        'meta_description',
        'allow_home',
        'allow_discover',
        'allow_radio',
        'allow_community',
        'allow_podcasts',
        'allow_trending',
        'is_visible',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, self::GENRE_RELATION_NAME);
    }

    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, self::GENRE_RELATION_NAME);
    }

    public function playlists(): MorphToMany
    {
        return $this->morphedByMany(Playlist::class, self::GENRE_RELATION_NAME);
    }

    public function albums(): MorphToMany
    {
        return $this->morphedByMany(Album::class, self::GENRE_RELATION_NAME);
    }

    public function stations(): MorphToMany
    {
        return $this->morphedByMany(Station::class, self::GENRE_RELATION_NAME);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, self::GENRE_RELATION_NAME);
    }

    public function podcasts(): MorphToMany
    {
        return $this->morphedByMany(Podcast::class, self::GENRE_RELATION_NAME);
    }

    public function podcastCategories(): BelongsToMany
    {
        return $this->belongsToMany(PodcastCategory::class, 'channel_podcast_category', 'channel_id', 'podcast_category_id');
    }

    public function radioCategories(): BelongsToMany
    {
        return $this->belongsToMany(RadioCategory::class, 'channel_radio_category', 'channel_id', 'radio_category_id');
    }
}
