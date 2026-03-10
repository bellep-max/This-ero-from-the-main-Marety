<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserGenderEnum;
use App\Http\Requests\Frontend\User\UserPassword\UserPasswordUpdateRequest;
use App\Http\Requests\Settings\AccountUpdateRequest;
use App\Http\Requests\Settings\PreferencesUpdateRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Requests\Frontend\User\UserProfile\UserProfileAvatarUpdateRequest;
use App\Http\Resources\ConnectedServiceResource;
use App\Http\Resources\CountryShortResource;
use App\Models\Country;
use App\Models\Service;
use App\Models\MESubscription;
use App\Services\ArtworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class SettingsController extends ApiController
{
    public function profile(): JsonResponse
    {
        return $this->success([
            'genders' => UserGenderEnum::toOptions(),
            'countries' => CountryShortResource::collection(Country::all()),
        ]);
    }

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return $this->success($request->user(), 'Your profile settings have been updated');
    }

    public function account(): JsonResponse
    {
        return $this->success(auth()->user());
    }

    public function updateAccount(AccountUpdateRequest $request): JsonResponse
    {
        $request->user()->update($request->all());

        return $this->success($request->user(), 'Your account settings have been updated');
    }

    public function password(): JsonResponse
    {
        return $this->success([
            'mustVerifyEmail' => auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
        ]);
    }

    public function updatePassword(UserPasswordUpdateRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->success(null, 'Your password has been updated');
    }

    public function preferences(): JsonResponse
    {
        return $this->success(auth()->user());
    }

    public function updatePreferences(PreferencesUpdateRequest $request): JsonResponse
    {
        $request->user()->update($request->all());

        return $this->success($request->user(), 'Your preferences have been updated');
    }

    public function subscription(): JsonResponse
    {
        Gate::authorize('edit', MESubscription::class);

        return $this->success([
            'plans' => Service::all(),
            'subscription' => auth()->user()->activeSubscription(),
        ]);
    }

    public function connectedServices(): JsonResponse
    {
        return $this->success([
            'connections' => ConnectedServiceResource::collection(auth()->user()->connects),
        ]);
    }

    public function updateAvatar(UserProfileAvatarUpdateRequest $request): JsonResponse
    {
        ArtworkService::updateArtwork($request, auth()->user());

        return $this->success(null, 'Your avatar was updated');
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(null, 'Account deleted successfully');
    }
}
