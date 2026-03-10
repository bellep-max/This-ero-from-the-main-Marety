<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UserLinktree\UserLinktreeUpdateRequest;
use Illuminate\Http\RedirectResponse;

class UserLinktreeController extends Controller
{
    public function update(UserLinktreeUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update($request->validated());

        auth()->setUser($user);

        return redirect()->back();
    }

    public function destroy(): RedirectResponse
    {
        $user = auth()->user();
        $user->update(['linktree_link' => null]);

        auth()->setUser($user);

        return redirect()->back();
    }
}
