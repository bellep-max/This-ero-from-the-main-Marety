<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Backend\Comment\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Email;
use App\Models\Group;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends ApiController
{
    public function __construct(private readonly CommentService $commentService) {}

    public function store(CommentStoreRequest $request): JsonResponse
    {
        $comment = $this->commentService->createComment($request);

        $comment->load('user');

        if (config('settings.comment_notif_admin')) {
            (new Email)->newComment($comment);
        }

        if (Group::getValue('comment_modc')) {
            return $this->success(null, trans('web.POPUP_COMMENT_MODERATION'));
        }

        return $this->success(CommentResource::make($comment), 'Comment posted successfully', 201);
    }
}
