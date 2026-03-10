<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Http\Requests\Backend\Comment\CommentStoreRequest;
use App\Models\Group;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function createComment(CommentStoreRequest $request): Model
    {
        $model = $request->input('commentable_type')::query()
            ->where('uuid', $request->input('uuid'))
            ->firstOrFail();

        $comment = $model->comments()->create([
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'approved' => Group::getValue('comment_modc')
                ? DefaultConstants::FALSE
                : DefaultConstants::TRUE,
        ]);

        if ($request->filled('parent_id')) {
            $comment->parent()->increment('reply_count');
        }

        $model->increment('comment_count');

        return $comment;
    }

    public function sanitizeContent(string $content): string
    {
        return trim(strip_tags(nl2br(preg_replace("/\s|&nbsp;/", ' ', $content)), '<tag>'));
    }
}
