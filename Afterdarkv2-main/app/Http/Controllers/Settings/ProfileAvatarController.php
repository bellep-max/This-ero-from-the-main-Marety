<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UserProfile\UserProfileAvatarUpdateRequest;
use App\Services\ArtworkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAvatarController extends Controller
{
    public function update(UserProfileAvatarUpdateRequest $request): RedirectResponse
    {
        ArtworkService::updateArtwork($request, auth()->user());

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Your avatar was updated',
        ]);

        return redirect()->back(303);
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
