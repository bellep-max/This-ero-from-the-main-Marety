<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Activity extends Model
{
    private const ACTION_RELATION_NAME = 'actionable';

    protected $fillable = [
        //        'uuid', //todo implement UUID?
        'user_id',
        'activityable_id',
        'activityable_type',
        'action',
        'allow_comments',
        'comment_count',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'allow_comments' => 'boolean',
        'action' => ActivityTypeEnum::class,
    ];

    protected $appends = [
        'details',
    ];

    protected $hidden = [
        'events',
    ];

    // RELATIONS
    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actionable(): HasOne
    {
        return $this->hasOne(Actionable::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, self::ACTION_RELATION_NAME);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, self::ACTION_RELATION_NAME);
    }

    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, self::ACTION_RELATION_NAME);
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, self::ACTION_RELATION_NAME);
    }

    // GETTERS

    public function getDetailsAttribute()
    {
        if (in_array($this->action, ActivityTypeEnum::getSongActivities())) {
            return $this->songs;
        } elseif (in_array($this->action, ActivityTypeEnum::getUserActivities())) {
            return $this->users;
        } elseif ($this->action === ActivityTypeEnum::addEvent) {
            return $this->events;
        }
    }
    //    public function getDetailsAttribute(): \stdClass
    //    {
    //        $buffer = new \stdClass;
    //
    //        if ($this->action == ActivityTypeEnum::addToPlaylist) {
    // //            $buffer->model = Playlist::query()->withoutGlobalScopes()->find($this->activityable_id);
    // //            $buffer->objects = Song::query()->fullPrivacy()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::inviteCollaborate) {
    // //            $buffer->model = Playlist::query()->withoutGlobalScopes()->find($this->activityable_id);
    //        } elseif ($this->action == ActivityTypeEnum::favoriteSong || $this->action == ActivityTypeEnum::collectSong || $this->action == ActivityTypeEnum::playSong) {
    // //            $buffer->objects = Song::query()->fullPrivacy()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::followUser) {
    // //            $buffer->objects = User::query()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::followPlaylist) {
    // //            $buffer->objects = Playlist::query()->with('user')->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::followArtist) {
    // //            $buffer->objects = Artist::query()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::addSong) {
    // //            $buffer->model = Artist::find($this->activityable_id);
    // //            $buffer->objects = Song::query()->fullPrivacy()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::addEvent) {
    // //            $buffer->model = Artist::find($this->activityable_id);
    // //            $buffer->objects = Event::query()->whereIn('id', explode(',', $this->events))->get();
    //        } elseif ($this->action == ActivityTypeEnum::postFeed) {
    //            switch ($this->activityable_type) {
    //                case Song::class:
    //                    $buffer->objects = Song::query()->fullPrivacy()->where('id', $this->activityable_id)->get();
    //                    break;
    //                case Album::class:
    //                    $buffer->objects = Album::query()->where('id', $this->activityable_id)->get();
    //                    break;
    //                case Artist::class:
    //                    $buffer->objects = Artist::query()->where('id', $this->activityable_id)->get();
    //                    break;
    //                case Playlist::class:
    //                    $buffer->objects = Playlist::withoutGlobalScopes()->where('id', $this->activityable_id)->get();
    //                    break;
    //            }
    //        }
    //
    //        return $buffer;
    //    }
}
