<?php

namespace App\Models;

use App\Scopes\PriorityScope;
use App\Traits\ArtworkTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slide extends Model implements HasMedia
{
    use ArtworkTrait;
    use InteractsWithMedia;

    protected $fillable = [
        'allow_community',
        'allow_discover',
        'allow_home',
        'allow_podcasts',
        'allow_radio',
        'allow_trending',
        'description',
        'object_id',
        'object_type',
        'podcast',
        'priority',
        'radio',
        'title',
        'title_link',
        'user_id',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
    ];

    protected $hidden = [
        'media',
    ];

    protected $casts = [
        'allow_community' => 'boolean',
        'allow_discover' => 'boolean',
        'allow_home' => 'boolean',
        'allow_podcasts' => 'boolean',
        'allow_radio' => 'boolean',
        'allow_trending' => 'boolean',
        'is_visible' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new PriorityScope);
    }

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function podcasts(): BelongsToMany
    {
        return $this->belongsToMany(Podcast::class, 'slide_podcast');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $sizes = [
            'sm' => 60,
            'md' => 120,
            'lg' => 500,
        ];

        foreach ($sizes as $size => $width) {
            $this->addMediaConversion($size)
                ->width($width)
                ->performOnCollections('artwork')
                ->nonOptimized()
                ->nonQueued();
        }
    }

    public function object(): MorphTo
    {
        return $this->morphTo('object');
    }
}
