<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Scopes\VisibilityScope;
use App\Traits\ArtworkTrait;
use App\Traits\HasUuidTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class Playlist extends Model implements HasMedia
{
    use ArtworkTrait;
    use HasUuidTrait;
    use ImageMediaTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'collaboration',
        'title',
        'description',
        'loves',
        'allow_comments',
        'comment_count',
        'is_visible',
        'type',
        'is_explicit',
    ];

    protected $appends = [
        'artwork',
        'slug',
        'favorite',
    ];

    protected $hidden = [
        'media',
        'approved',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_visible' => 'bool',
        'allow_comments' => 'bool',
        'collaboration' => 'bool',
        'is_explicit' => 'bool',
    ];

    //    protected static function boot()
    //    {
    //        parent::boot();
    //        static::addGlobalScope(new VisibilityScope);
    //    }

    // RELATIONS

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'playlist_songs')
            ->using(PlaylistSong::class)
            ->orderByPivot('priority', 'asc');
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable')->latest();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function followers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'loveable', 'love');
    }

    public function playlistSongs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'playlist_songs', 'playlist_id', 'song_id')
            ->withoutGlobalScopes();
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function loves(): MorphMany
    {
        return $this->morphMany(Love::class, 'loveable');
    }

    public function collaboratorsTest(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Collaborator::class, 'playlist_id', 'user_id')
            ->wherePivot('approved', DefaultConstants::TRUE);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Collaborator::class, 'playlist_id', 'friend_id');
    }

    public function approvedCollaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Collaborator::class, 'playlist_id', 'friend_id')
            ->wherePivot('approved', DefaultConstants::TRUE);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable')->withoutGlobalScopes();
    }

    // GETTERS

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    public function getFavoriteAttribute(): bool
    {
        return auth()->check() && $this->loves()
            ->where('user_id', auth()->id())
            ->exists();
    }

    // SCOPES
    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }

    //    public function collaborators()
    //    {
    //        return User::leftJoin('collaborators', 'users.id', '=', 'collaborators.friend_id')
    //            ->select('users.*', 'collaborators.id as host_id')
    //            ->where('collaborators.playlist_id', $this->id)
    //            ->where('collaborators.approved', DefaultConstants::TRUE);
    //    }
}
