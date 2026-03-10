<?php

namespace App\Models;

use App\Constants\RoleConstants;
use App\Helpers\Helper;
use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\PrivacyTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class FinalSong extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use PrivacyTrait;
    use SanitizedRequest;

    protected $table = 'adventure_children_finals';

    protected $fillable = [
        'mp3',
        'hd',
        'hls',
        'adventure_children_id',
        'duration',
        'title',
        'description',
        'order',
        'approved',
        'user_id',
        'wav',
        'pending',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
        //        'signed_permalink',
        'stream_url'/* ,'favorite', 'library' */,
        'streamable',
        'slug',
    ];

    protected $hidden = [
        'media',
        'artistIds',
        /*  'description', */
        'user_id',
        'user',
    ];

    public function history(): MorphToMany
    {
        return $this->morphToMany(History::class, 'historyable', 'histories');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(ChildSong::class)->withoutGlobalScopes();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new VisibilityScope);
        static::addGlobalScope(new ApprovedScope);
        static::addGlobalScope(new PublishedScope);
    }

    public function getMinutesAttribute(): string
    {
        return date('i:s', $this->duration);
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title) ?: 'no-slug';
    }

    //    public function getSignedPermalinkAttribute(): string
    //    {
    //        $slug = str_slug($this->title) ? str_slug($this->title) : str_replace(' ', '-', $this->title);
    //
    //        return URL::signedRoute('song.show', ['song' => $this->id, 'slug' => $this->slug]);
    //    }

    public function getStreamAbleAttribute(): bool
    {
        if (isset($this->access)) {
            $options = Helper::groupPermission($this->access);
            if ($this->access && isset($options[Group::groupId()])) {
                return $options[Group::groupId()] < RoleConstants::USER_SUBSCRIPTION;
            }
        }

        return (bool) Group::getValue('option_stream');
    }

    public function getStreamUrlAttribute(): string
    {
        return isset($this->hls) && $this->hls
            ? route('song.stream.hls', $this->id) . '?role=final'
            : URL::temporarySignedRoute('song.stream.mp3', now()->addDay(), [
                'id' => $this->id,
            ]) . '?role=final';
    }

    // todo REFACTOR
    public function delete(): ?bool
    {
        /* DB::table('playlist_songs')->where('song_id', $this->id)->delete();
        DB::table('album_songs')->where('song_id', $this->id)->delete();
        Comment::where('commentable_type', $this->getMorphClass())->where('commentable_id', $this->id)->delete();
        Love::where('loveable_type', $this->getMorphClass())->where('loveable_id', $this->id)->delete();
        Notification::where('notificationable_type', $this->getMorphClass())->where('notificationable_id', $this->id)->delete();
        Activity::where('activityable_type', $this->getMorphClass())->where('activityable_id', $this->id)->delete(); */

        return parent::delete();
    }
}
