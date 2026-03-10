<?php

namespace App\Observers;

use App\Enums\ActivityTypeEnum;
use App\Models\Activity;
use App\Models\Comment;
use App\Services\NotificationService;

readonly class CommentObserver
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Handle the activity "created" event.
     */
    public function created(Comment $comment): void
    {
        Activity::create([
            'user_id' => auth()->id(),
            'activityable_type' => $comment->commentable()->getMorphType(),
            'activityable_id' => $comment->commentable->id,
            'action' => ActivityTypeEnum::comment,
        ]);

        $this->notificationService->notify($comment->commentable->user, $comment->commentable, ActivityTypeEnum::comment);

        if ($comment->parent_id) {
            $this->notificationService->notify($comment->parent->user, $comment->commentable, ActivityTypeEnum::replyComment);
        }
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Comment $comment)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Comment $comment)
    {
        $comment->reactions()->delete();
        $comment->notifications()->delete();

        if ($comment->parent_id) {
            Comment::withoutGlobalScopes()->where('id', $comment->parent_id)->decrement('reply_count');
        } else {
            $comment->commentable()->decrement('comment_count');
        }
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }
}
