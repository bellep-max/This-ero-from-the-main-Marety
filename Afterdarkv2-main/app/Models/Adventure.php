<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Enums\ActivityTypeEnum;
use App\Enums\AdventureSongTypeEnum;
use App\Services\TimeService;
use App\Traits\ArtworkTrait;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;

class Adventure extends Model implements HasMedia
{
    use ArtworkTrait;
    use HasUuidTrait;
    use ImageMediaTrait;

    protected $table = 'adventures';

    protected $fillable = [
        'uuid',
        'mp3',
        'hd',
        'hls',
        'parent_id',
        'type',
        'order',
        'file_url',
        'user_id',
        'title',
        'description',
        'duration',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'artwork',
        //        'signed_permalink',
        //        'stream_url',
        //        'streamable',
    ];

    protected $hidden = [
        'media',
        'user_id',
    ];

    protected $casts = [
        'type' => AdventureSongTypeEnum::class,
        'is_visible' => 'boolean',
        'approved' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_download' => 'boolean',
        'released_at' => 'timestamp',
    ];

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function history(): MorphToMany
    {
        return $this->morphToMany(History::class, 'historyable', 'histories');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Adventure::class, 'parent_id')
            ->where(function ($query) {
                $query->where('type', AdventureSongTypeEnum::Root)
                    ->orWhere('type', AdventureSongTypeEnum::Heading);
            });
    }

    public function children(): HasMany
    {
        return $this->hasMany(Adventure::class, 'parent_id')
            ->whereIn('type', [AdventureSongTypeEnum::Root, AdventureSongTypeEnum::Final])
            ->orderBy('order', 'ASC');
    }

    public function roots(): HasMany
    {
        return $this->hasMany(Adventure::class, 'parent_id')
            ->where('type', AdventureSongTypeEnum::Root)
            ->orderBy('order', 'ASC');
    }

    public function finals(): HasMany
    {
        return $this->hasMany(Adventure::class, 'parent_id')
            ->where('type', AdventureSongTypeEnum::Final)
            ->orderBy('order', 'ASC');
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function fans(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'loveable', 'love', 'loveable_id', 'user_id')
            ->where('loveable_type', self::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable')->withoutGlobalScopes();
    }

    public function property(): HasOne
    {
        return $this->hasOne(AdventureProperty::class);
    }

    // GETTERS

    public function getMinutesAttribute(): string
    {
        return TimeService::getHumanReadableTime($this->duration);
    }

    public function getArtworkAttribute(): string
    {
        if (!$media = $this->getFirstMedia('artwork')) {
            if (config('settings.automate')) {
                $row = $this->log();

                return isset($row->id)
                    ? $row->artwork
                    : asset('/assets/images/song.png');
            } else {
                return asset('/assets/images/song.png');
            }
        }

        //        return $media->getTemporaryUrl(Carbon::now()->addMinutes(intval(config('settings.s3_signed_time', 5))), 'lg');

        return $media->getTemporaryUrl(
            now()->addMinutes(
                config('settings.s3_signed_time', 5)
            ), 'lg'
        );
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

    public function getTotalPlaysAttribute(): int
    {
        return $this->activities()
            ->where('action', ActivityTypeEnum::playSong)
            ->count();
    }

    // SCOPES

    public function scopeHeading($query)
    {
        return $query->where('type', AdventureSongTypeEnum::Heading);
    }

    public function scopeVisible($query)
    {
        return $query->whereHas('property', function ($query) {
            $query->where('is_visible', DefaultConstants::TRUE);
        });
    }

    //    public function getPermalinkAttribute(): string
    //    {
    //        return route('song.show', ['song' => $this->id, 'slug' => $this->slug]);
    //    }
    //
    //    public function getSignedPermalinkAttribute(): string
    //    {
    //        $slug = str_slug($this->title) ? str_slug($this->title) : str_replace(' ', '-', $this->title);
    //
    //        return URL::signedRoute('song.show', ['song' => $this->id, 'slug' => $this->slug]);
    //    }
    //
    //    public function getStreamAbleAttribute(): bool
    //    {
    //        if (isset($this->access)) {
    //            $options = groupPermission($this->access);
    //            if ($this->access && isset($options[Group::groupId()])) {
    //                return $options[Group::groupId()] < RoleConstants::USER_SUBSCRIPTION;
    //            }
    //        }
    //
    //        return (bool) Group::getValue('option_stream');
    //    }

    //    public function getStreamUrlAttribute(): string
    //    {
    //        return isset($this->hls) && $this->hls
    //            ? route('song.stream.hls', $this->id).'?role=final'
    //            : URL::temporarySignedRoute('song.stream.mp3', now()->addDay(), [
    //                'id' => $this->id,
    //            ]).'?role=final';
    //    }
}
