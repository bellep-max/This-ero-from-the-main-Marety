<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Scopes\PublishedScope;
use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\PrivacyTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class ChildSong extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use PrivacyTrait;
    use SanitizedRequest;

    protected $table = 'adventure_children';

    protected $fillable = [
        'title',
        'genre',
        'album_id',
        'artworkId',
        'releasedOn',
        'copyright',
        'approve',
        'duration',
        'vocal',
        'order',
        'description',
        'song_id',
    ];

    protected $appends = [
        'artwork',
        /*  'artists', */
        'permalink',
        'signed_permalink',
        'stream_url',
        /*  'favorite', */
        /* 'library', */
        'streamable',
        'slug',
    ];

    protected $hidden = [
        'media',
        /* 'artistIds',  'description' , */
        'user_id',
        'user',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PublishedScope);
    }

    // RELATIONS
    public function history(): MorphToMany
    {
        return $this->morphToMany(History::class, 'historyable', 'histories');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function adventureFinalSongs(): HasMany
    {
        return $this->hasMany(FinalSong::class, 'adventure_children_id')->withoutGlobalScopes();
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, 'song_id')->withoutGlobalScopes();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function songTest(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    // GETTERS

    public function getMinutesAttribute()
    {
        $this->minutes = date('i:s', $this->duration);

        return $this->minutes;
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('song.show', ['song' => $this->id, 'slug' => $this->slug]);
    }

    public function getSignedPermalinkAttribute(): string
    {
        return URL::signedRoute('song.show', ['song' => $this->id, 'slug' => $this->slug]);
    }

    public function getStreamAbleAttribute(): bool
    {
        if (isset($this->access)) {
            $options = Helper::groupPermission($this->access);
            if ($this->access && isset($options[Group::groupId()])) {
                return $options[Group::groupId()] < 3;
            }
        }

        return (bool) Group::getValue('option_stream');
    }

    public function getStreamUrlAttribute(): string
    {
        if (isset($this->hls) && $this->hls) {
            return route('song.stream.hls', $this->id) . '?role=child';
        }

        return URL::temporarySignedRoute('song.stream.mp3', now()->addDay(), [
            'id' => $this->id,
        ]) . '?role=child';
    }

    public function delete(): ?bool
    {
        /*  DB::table('playlist_songs')->where('song_id', $this->id)->delete();
         DB::table('album_songs')->where('song_id', $this->id)->delete();
         Comment::where('commentable_type', $this->getMorphClass())->where('commentable_id', $this->id)->delete();
         Love::where('loveable_type', $this->getMorphClass())->where('loveable_id', $this->id)->delete();
         Notification::where('notificationable_type', $this->getMorphClass())->where('notificationable_id', $this->id)->delete();
         Activity::where('activityable_type', $this->getMorphClass())->where('activityable_id', $this->id)->delete();
 */
        return parent::delete();
    }
}
