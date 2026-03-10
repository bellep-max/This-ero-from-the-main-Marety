<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UserPassword\UserPasswordUpdateRequest;
use App\Http\Resources\ConnectedServiceResource;
use App\Models\Connect;
use App\Models\User;
use App\Services\SocialiteService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class ConnectedServiceController extends Controller
{
    public function __construct(private readonly SocialiteService $socialiteService) {}

    /**
     * Show the user's password settings page.
     */
    public function index(): Response
    {
        return Inertia::render('Settings/ConnectedServices', [
            'connections' => ConnectedServiceResource::collection(auth()->user()->connects),
        ]);
    }

    public function redirect(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request): JsonResponse|RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();

            $connect = Connect::query()
                ->where('provider_id', $socialiteUser->id)
                ->where('service', $provider)
                ->first();

            if (auth()->check()) {
                if (isset($connect->id) && $connect->user_id != auth()->id()) {
                    return redirect()->route('home.index');
                }

                Connect::updateOrCreate([
                    'user_id' => auth()->id(),
                    'provider_id' => $socialiteUser->id,
                    'service' => $provider,
                ], [
                    'provider_name' => $socialiteUser->name,
                    'provider_artwork' => $socialiteUser->avatar ?? '',
                    'provider_email' => $socialiteUser->email ?? null,
                ]);
            } else {
                if (isset($connect->user_id)) {
                    $authUser = User::find($connect->user_id);

                    if (isset($authUser->id)) {
                        if ($request->is('api.*')) {
                            return $this->socialiteService->createdToken($authUser);
                        } else {
                            Auth::loginUsingId($authUser->id);

                            return redirect()->route('home.index');
                        }
                    } else {
                        $connect->delete();
                    }
                }

                if (isset($socialiteUser->email)) {
                    if ($authUser = User::firstWhere('email', $socialiteUser->email)) {

                        if ($request->is('api.*')) {
                            return $this->socialiteService->createdToken($authUser);
                        }

                        auth()->loginUsingId($authUser->id);

                        Connect::create([
                            'user_id' => auth()->id(),
                            'provider_id' => $socialiteUser->id,
                            'provider_name' => $socialiteUser->name,
                            'provider_email' => $socialiteUser->email,
                            'provider_artwork' => $socialiteUser->avatar ?? null,
                            'service' => $provider,
                        ]);

                        $this->socialiteService->userBannedCheck();

                        return redirect()->route('home.index');
                    }
                }

                $user = User::create([
                    'name' => $socialiteUser->name ?? $socialiteUser->nickname,
                    'username' => Str::slug($socialiteUser->nickname) ?? strtolower(Str::random()),
                    'password' => bcrypt(Str::random()),
                    'email' => $socialiteUser->email ?? null,
                ]);

                if ($socialiteUser->avatar) {
                    $user->addMediaFromUrl($socialiteUser->avatar)
                        ->usingFileName(time() . '.jpg')
                        ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
                }

                Connect::create([
                    'user_id' => $user->id,
                    'provider_id' => $socialiteUser->id,
                    'provider_name' => $socialiteUser->name,
                    'provider_email' => $socialiteUser->email ?? null,
                    'provider_artwork' => $socialiteUser->avatar ?? '',
                    'service' => $provider,
                ]);

                if ($request->is('api.*')) {
                    return $this->socialiteService->createdToken($user);
                }

                auth()->loginUsingId($user->id);
            }

            return redirect()->route('home.index');
        } catch (Exception $exception) {
            return redirect()->route('home.index');
        }
    }

    //
    //    /**
    //     * Update the user's password.
    //     */
    //    public function update(UserPasswordUpdateRequest $request): RedirectResponse
    //    {
    //        $request->user()->update([
    //            'password' => Hash::make($request->input('password')),
    //        ]);
    //
    //        session()->flash('message', [
    //            'level' => 'success',
    //            'content' => 'Your password has been updated',
    //        ]);
    //
    //        return redirect()->back(303);
    //    }
}
