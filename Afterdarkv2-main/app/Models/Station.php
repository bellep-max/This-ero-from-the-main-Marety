<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\FullTextSearch;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Station extends Model implements HasMedia
{
    use ArtworkTrait;
    use FullTextSearch;
    use ImageMediaTrait;

    protected $fillable = [
        'allow_comments',
        'category',
        'city_id',
        'comment_count',
        'country_id',
        'country_code',
        'description',
        'failed_count',
        'language_id',
        'play_count',
        'stream_url',
        'title',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
        'permalink',
        'slug',
    ];

    protected $hidden = ['media'];

    protected array $searchable = [
        'title',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function getCategoriesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return RadioCategory::query()
            ->whereIn('id', explode(',', $this->category))
            ->get();
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('station.show', ['station' => $this->id, 'slug' => $this->slug]);
    }
}
