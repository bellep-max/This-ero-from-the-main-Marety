<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\User\MyProfileResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Email;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $loginField = config('settings.authorization_method', 0) == 0 ? 'username' : 'email';

        $rules = $loginField === 'username'
            ? [
                'username' => ['required', 'string', 'alpha_dash'],
                'password' => ['required', 'string'],
            ]
            : [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ];

        $request->validate($rules);

        $credentials = $request->only([$loginField, 'password']);

        if (!Auth::attempt($credentials, $request->boolean('remember', true))) {
            return $this->error(__('auth.failed'), 422);
        }

        $user = Auth::user();

        if (config('settings.registration_method') == 1 && !$user->email_verified) {
            Auth::logout();
            return $this->error(__('auth.email_verification_required'), 422);
        }

        if ($user->ban()->exists()) {
            $banInfo = $user->ban;
            if ($banInfo->end_at) {
                if ($banInfo->end_at->isPast()) {
                    $banInfo->delete();
                } else {
                    Auth::logout();
                    return $this->error(__('auth.banned', [
                        'banned_reason' => $banInfo->reason,
                        'banned_time' => $banInfo->end_at->format('H:i F j Y'),
                    ]), 403);
                }
            }
        }

        $user->update(['logged_ip' => $request->ip()]);

        $request->session()->regenerate();

        return $this->success([
            'user' => $this->getUserData($user),
        ], 'Login successful');
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string'],
            'group' => ['nullable', 'string'],
        ]);

        $verifyCode = Str::random(32);

        $groupId = Group::query()
            ->where('name', $request->input('group'))
            ->value('id');

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name ? strip_tags($request->name) : strip_tags($request->username),
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email_verified_code' => $verifyCode,
            'group_id' => $groupId,
        ]);

        $user->assignRole($request->input('role'));

        if (config('settings.registration_method') == 1) {
            (new Email)->verifyAccount($user, route('frontend.account.verify', ['code' => $verifyCode]));

            return $this->success([
                'activation' => true,
                'email' => 'sent',
            ], 'Please verify your email to activate your account');
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, true)) {
            return $this->error('Registration failed', 400);
        }

        (new Email)->newUser(Auth::user());

        $request->session()->regenerate();

        return $this->success([
            'user' => $this->getUserData(Auth::user()),
        ], 'Registration successful', 201);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(null, 'Logged out successfully');
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        return $this->success([
            'user' => $this->getUserData($user),
        ]);
    }

    private function getUserData(User $user): MyProfileResource
    {
        return MyProfileResource::make($user->loadMissing([
            'playlists',
            'podcasts',
            'approvedCollaboratedPlaylists',
            'group',
            'unreadNotifications' => function ($query) {
                $query->with([
                    'notificationable',
                    'hostable',
                ])
                    ->orderBy('read_at', 'asc')
                    ->orderBy('created_at', 'desc');
            },
        ]));
    }
}
