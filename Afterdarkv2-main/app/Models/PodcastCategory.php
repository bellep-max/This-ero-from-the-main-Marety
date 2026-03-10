<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class PodcastCategory extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use SanitizedRequest;

    protected $table = 'podcast_categories';

    protected $fillable = [
        'alt_name',
        'description',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'name',
        'parent_id',
        'priority',
    ];

    protected $appends = [
        'slug',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function podcasts(): BelongsToMany
    {
        return $this->belongsToMany(Podcast::class, 'podcast_podcast_category');
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->alt_name) ?: 'no-slug';
    }
}
