<?php

namespace App\Services;

use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordService
{
    public function resetPassword(string $email): ?bool
    {
        $user = User::query()
            ->where('email', $email)
            ->firstOrFail();

        $token = $this->generateToken();

        DB::table('password_resets')
            ->updateOrInsert([
                'email', $user->email,
            ], [
                'token' => $token,
                'created_at' => now(),
            ]);

        (new Email)->resetPassword($user, route('frontend.account.reset.password', ['token' => $token]));

        return true;
    }

    public function setPassword(array $data): User
    {
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        DB::table('password_resets')
            ->where('token', '=', $data['token'])
            ->delete();

        return $user;
    }

    private function generateToken(int $length = 60): string
    {
        return Str::random($length);
    }
}
