<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Scopes\ApprovedScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    protected $fillable = [
        'parent_id',
        'reply_count',
        'user_id',
        'commentable_id',
        'commentable_type',
        'content',
        'edited',
        'ip',
        'reaction_count',
        'approved',
    ];

    protected $appends = [
        //        'time_elapsed',
        //        'replies',
        'reactions',
        'reacted',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ApprovedScope);
    }

    // RELATIONS
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    // GETTERS
    //    public function getContentAttribute(): string
    //    {
    //        return nl2br(mentionToLink($this->attributes['content'], false));
    //    }

    //    public function getTimeElapsedAttribute(): string
    //    {
    //        return timeElapsedShortString($this->created_at);
    //    }

    //    public function getRepliesAttribute(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    //    {
    //        return $this->getReplies($this->id, 2);
    //    }

    public function getReactionsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->reactions()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();
    }

    public function getReactedAttribute(): null
    {
        return null;
    }

    public function getObjectAttribute()
    {
        return (new $this->commentable_type)::find($this->commentable_id);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', DefaultConstants::TRUE);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approved', DefaultConstants::FALSE);
    }

    // todo REFACTOR THIS
    //    public function getReplies($parentId, $limit = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    //    {
    //        $replies = self::with('user')
    //            ->where('parent_id', $parentId)
    //            ->paginate($limit, ['*'], 'page', 1);
    //
    //        $replies->setPath(route('comments.get.replies'));
    //
    //        return $replies;
    //    }
}
