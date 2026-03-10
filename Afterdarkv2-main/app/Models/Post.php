<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Constants\DiskConstants;
use App\Constants\TypeConstants;
use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Traits\FullTextSearch;
use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use FullTextSearch;
    use HasUuidTrait;
    use InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'access',
        'allow_comments',
        'allow_main',
        'alt_name',
        'approved',
        'category_id',
        'comment_count',
        'disable_index',
        'fixed',
        'full_content',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'short_content',
        'title',
        'user_id',
        'view_count',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
    ];

    protected array $searchable = [
        'title',
        'short_content',
        'full_content',
    ];

    protected $casts = [
        'is_visible' => 'bool',
        'allow_comments' => 'bool',
        'allow_main' => 'bool',
        'approved' => 'bool',
    ];

    //    protected static function boot()
    //    {
    //        parent::boot();
    //        static::addGlobalScope(new PublishedScope);
    //        static::addGlobalScope(new VisibilityScope);
    //        static::addGlobalScope(new ApprovedScope);
    //    }

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    //    public function tags(): HasMany
    //    {
    //        return $this->hasMany(PostTag::class, 'post_id');
    //    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function poll(): HasOne
    {
        return $this->hasOne(Poll::class, 'object_id')
            ->where('object_type', TypeConstants::POST);
    }

    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'post_id');
    }

    // GETTERS
    public function getArtworkAttribute(): string
    {
        if (!$media = $this->getFirstMedia('artwork')) {
            return asset('assets/images/artist.png');
        }

        if ($media->disk === DiskConstants::WASABI) {
            try {
                return $media->getTemporaryUrl(now()->addMinutes(config('settings.s3_signed_time', 5)), 'thumbnail');
            } catch (\Exception $e) {
                // Fallback to full URL if temporary URL generation fails (e.g., missing S3 config)
                try {
                    return $media->getFullUrl('thumbnail');
                } catch (\Exception $e) {
                    return asset('assets/images/artist.png');
                }
            }
        }

        return $media->getFullUrl('thumbnail');
    }

    public function getCategoriesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()
            ->whereIn('id', explode(',', $this->category))
            ->get();
    }

    public function getPermalinkAttribute(): string
    {
        if (!$this->uuid) {
            return '#';
        }
        return route('posts.show', $this->uuid);
    }

    // SCOPES
    public function scopeApproved($query)
    {
        return $query->where('approved', DefaultConstants::TRUE);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approved', DefaultConstants::FALSE);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }

    public function scopeNotVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::FALSE);
    }

    // MISC
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(config('settings.image_max_thumbnail_size'))
            ->keepOriginalImageFormat()
            ->performOnCollections('artwork')
            ->nonOptimized()->nonQueued();
    }
}
