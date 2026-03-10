<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Comment\CommentUpdateRequest;
use App\Models\Comment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Route;

class CommentController
{
    private const DEFAULT_ROUTE = 'backend.comments.index';

    public function index(): View|Application|Factory
    {
        return view('backend.comments.index')
            ->with([
                'comments' => Route::currentRouteName() == 'backend.comments.approved'
                    ? Comment::query()->withoutGlobalScopes()->approved()->latest()->paginate(20)
                    : Comment::query()->withoutGlobalScopes()->notApproved()->latest()->paginate(20),
            ]);
    }

    public function edit(Comment $comment): View|Application|Factory
    {
        return view('backend.comments.edit')
            ->with([
                'comment' => $comment,
            ]);
    }

    public function update(Comment $comment, CommentUpdateRequest $request): RedirectResponse
    {
        $comment->update($request->input());

        return MessageHelper::redirectMessage('Comment successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return MessageHelper::redirectMessage('Comment successfully deleted!', self::DEFAULT_ROUTE);
    }
}
