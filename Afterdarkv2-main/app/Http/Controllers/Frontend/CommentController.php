<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\ActionConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Comment\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Email;
use App\Models\Group;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    private const COMMENTS_PER_PAGE = 5;

    public function __construct(private readonly CommentService $commentService) {}

    public function store(CommentStoreRequest $request): RedirectResponse
    {
        $comment = $this->commentService->createComment($request);

        $comment->load('user');

        if (config('settings.comment_notif_admin')) {
            (new Email)->newComment($comment);
        }

        if (Group::getValue('comment_modc')) {
            return redirect()->back()->with([
                'success' => true,
                'data' => trans('web.POPUP_COMMENT_MODERATION'),
            ]);
        }

        // Notify to commentable author

        //        pushNotification(
        //            $commentable->getMorphClass() == User::class ? $comment->commentable_id : $commentable->user_id,
        //            $commentable->id,
        //            $commentable->getMorphClass(),
        //            ActionConstants::COMMENT_MUSIC,
        //            $comment->id
        //        );
        //
        //        pushNotificationMentioned(
        //            $commentContent,
        //            $commentable->id,
        //            $commentable->getMorphClass(),
        //            ActionConstants::COMMENT_MENTIONED,
        //            $comment->id
        //        );

        return redirect()->back()->with([
            'success' => true,
            'data' => CommentResource::make($comment),
        ]);
    }
}
