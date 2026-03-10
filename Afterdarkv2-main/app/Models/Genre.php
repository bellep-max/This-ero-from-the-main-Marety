<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Enums\AdventureSongTypeEnum;
use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;

class Genre extends Model implements HasMedia
{
    private const GENRE_RELATION_NAME = 'genreable';

    use ArtworkTrait;
    use ImageMediaTrait;
    //    use SanitizedRequest;

    protected $fillable = [
        'parent_id',
        'priority',
        'name',
        'alt_name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'discover',
    ];

    protected $hidden = [
        'media',
        'created_at',
        'description',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'updated_at',
    ];

    protected $appends = [
        'artwork',
    ];

    //    protected static function boot()
    //    {
    //        parent::boot();
    //        static::addGlobalScope(new PriorityScope);
    //    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, self::GENRE_RELATION_NAME);
    }

    public function adventures(): MorphToMany
    {
        return $this->morphedByMany(Adventure::class, self::GENRE_RELATION_NAME)
            ->where('type', AdventureSongTypeEnum::Heading);
    }

    public function channels(): MorphToMany
    {
        return $this->morphedByMany(Channel::class, self::GENRE_RELATION_NAME);
    }

    public function slides(): MorphToMany
    {
        return $this->morphedByMany(Slide::class, self::GENRE_RELATION_NAME);
    }

    public function playlists(): MorphToMany
    {
        return $this->morphedByMany(Playlist::class, self::GENRE_RELATION_NAME);
    }

    public function scopeDiscover($query)
    {
        return $query->where('discover', DefaultConstants::TRUE);
    }
}
