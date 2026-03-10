<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\LoginStoreRequest;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Check if banned and get user IP.
     */
    private function userBannedCheck(): void
    {
        if (auth()->user()->ban()->exists()) {
            $banInfo = auth()->user()->ban;

            if ($banInfo->end_at) {
                if ($banInfo->end_at->isPast()) {
                    $banInfo->delete();
                } else {
                    response()->json([
                        'success' => false,
                        'message' => __('auth.banned', ['banned_reason' => $banInfo->reason, 'banned_time' => $banInfo->end_at->format('H:i F j Y')]),
                    ], 403);

                    return;
                }
            }
        }

        auth()->user()->update([
            'logged_ip' => request()->ip(),
        ]);
    }

    public function store(LoginStoreRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials, true)) {
            return redirect()->back()->withErrors([
                'message' => __('auth.failed'),
            ]);
        }

        if (config('settings.registration_method') == 1 && !auth()->user()->email_verified) {
            auth()->logout();

            return redirect()->back()->withErrors([
                'message' => __('auth.email_verification_required'),
            ]);
        }

        $this->userBannedCheck();

        return redirect()->back();
    }

    public function destroy(): RedirectResponse
    {
        auth()->logout();
        session()->invalidate();

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Successfully logged out.',
        ]);

        return redirect()->back();
    }
}
