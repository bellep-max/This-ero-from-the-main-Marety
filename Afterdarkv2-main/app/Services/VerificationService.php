<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Models\Email;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VerificationService
{
    public function verifyEmail(string $code): ?bool
    {
        $user = User::query()
            ->where('email_verified_code', $code)
            ->first();

        if (isset($user->id) && $user->email) {
            if ($user->email_verified) {
                return __('auth.email_verification_verified');
            } else {
                $user->update([
                    'email_verified_at' => Carbon::now(),
                    'email_verified' => DefaultConstants::TRUE,
                ]);

                (new Email)->newUser($user);

                Auth::loginUsingId($user->id);
                header('Refresh: 5; URL=' . route('frontend.homepage'));

                return __('auth.email_verification_success');
            }
        }

        return __('auth.email_verification_invalid');
    }
}
