<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 09:02.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Profile\ProfileStoreRequest;
use App\Services\ArtworkService;
use Illuminate\Http\RedirectResponse;

class ProfileController
{
    public function index()
    {
        return view('backend.profile.edit')
            ->with([
                'user' => auth()->user(),
            ]);
    }

    public function store(ProfileStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $user->update($request->all());

        if ($request->input('removeArtwork')) {
            $user->clearMediaCollection('artwork');
        } elseif ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $user);
        }

        if ($request->input('deleteComments')) {
            $user->comments()->delete();
        }

        return MessageHelper::redirectMessage('Your profile has been successfully updated!');
    }
}
