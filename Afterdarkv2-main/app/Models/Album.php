<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 08:47.
 */

namespace App\Models;

use App\Constants\DiskConstants;
use App\Scopes\PublishedScope;
use App\Traits\ImageMediaTrait;
use App\Traits\PrivacyTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Album extends Model implements HasMedia
{
    use ImageMediaTrait;
    use PrivacyTrait;
    //    use SanitizedRequest;

    protected $fillable = [
        'user_id',
        'selling',
        'price',
        'genre',
        'type',
        'title',
        'description',
        'released_at',
        'copyright',
        'allow_comments',
        'comment_count',
        'is_visible',
        'is_explicit',
        'approved',
    ];

    protected $appends = [
        'artwork',
        'permalink',
        'slug',
    ];

    protected $hidden = [
        'media',
        'user_id',
        //        'artistIds',
        'description',
        'approved',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'released_at',
    ];

	protected $casts = [
        'selling' => 'boolean',
        'released_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_visible' => 'boolean',
        'is_explicit' => 'boolean',
        'approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
	];

    protected static function boot()
    {
        parent::boot();
        //        static::addGlobalScope(new PublishedScope);
    }

    // RELATIONS
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return Song::leftJoin('album_songs', 'album_songs.song_id', '=', (new Song)->getTable() . '.id')
            ->select((new Song)->getTable() . '.*', 'album_songs.id as host_id')
            ->where('album_songs.album_id', $this->id)
            ->orderBy('album_songs.priority', 'asc')
            ->orderBy('album_songs.id', 'asc');
    }

    public function songTest()
    {
        return $this->belongsToMany(Song::class, 'album_songs', 'album_id', 'song_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function love(): MorphOne
    {
        return $this->morphOne(Love::class, 'loveable')
            ->where('user_id', auth()->id());
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_album');
    }

    // GETTERS
    public function getArtworkAttribute(): string
    {
        if (!$media = $this->getFirstMedia('artwork')) {
            if (config('settings.automate')) {
                $row = AlbumLog::query()->where('album_id', $this->id)->first();

                return isset($row->id)
                    ? $row->artwork
                    : asset('assets/images/album.png');
            } else {
                return asset('assets/images/album.png');
            }
        }

        return $media->disk === DiskConstants::WASABI
            ? $media->getTemporaryUrl(now()->addMinutes(config('settings.s3_signed_time', 5)), 'lg')
            : $media->getFullUrl('lg');
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('album.show', ['album' => $this->id, 'slug' => $this->slug]);
    }

    public function getSongCountAttribute(): int
    {
        return $this->songTest()->count();
    }

    public function getSalesAttribute(): int
    {
        return Order::query()
            ->groupBy('amount')
            ->where('orderable_type', $this->getMorphClass())
            ->where('orderable_id', $this->id)
            ->count();
    }

    public function getPurchasedAttribute(): bool
    {
        return auth()->check() && $this->selling && Order::query()
            ->where('user_id', auth()->id())
            ->where('orderable_id', $this->id)
            ->where('orderable_type', $this->getMorphClass())
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

    // SCOPES
    public function scopeRelated($query)
    {
        return $query->whereNot('id', $this->id);
    }

    // todo REFACTOR THIS
    private function checkFavorite($album_id): bool
    {
        if (auth()->check()) {
            $row = DB::table('loves')
                ->select('loves.id')
                ->where('loves.user_id', auth()->id())
                ->where('loves.item_id', $album_id)
                ->where('loves.type', 3)
                ->first();

            return (object) $row && isset($row->id);
        } else {
            return false;
        }
    }
}
