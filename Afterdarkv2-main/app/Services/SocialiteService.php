<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Models\Ban;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SocialiteService
{
    public function createdToken($user): JsonResponse
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(30);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }

    public function userBannedCheck(): void
    {
        if (auth()->user()->banned) {
            $banned = Ban::find(auth()->id());

            if ($banned->end_at) {
                if (Carbon::parse($banned->end_at)->isPast()) {
                    User::query()
                        ->where('id', $banned->user_id)
                        ->update([
                            'banned' => DefaultConstants::FALSE,
                        ]);

                    Ban::query()
                        ->where('user_id', $banned->user_id)
                        ->delete();
                } else {
                    response()->json([
                        'message' => 'Unauthorized',
                        'errors' => ['message' => [__('auth.banned', ['banned_reason' => $banned->reason, 'banned_time' => Carbon::parse($banned->end_at)->format('H:i F j Y')])]],
                    ], 403);

                    return;
                }
            }
        }

        auth()->user()->update([
            'logged_ip' => request()->ip(),
        ]);
    }
}
