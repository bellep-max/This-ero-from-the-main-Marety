<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UserPassword\UserPasswordUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserPasswordController extends Controller
{
    public function update(UserPasswordUpdateRequest $request): JsonResponse
    {
        if ($request->input('email') != auth()->user()->email) {
            $user = auth()->user();
            $user->password = Hash::make($request->input('password'));
            $user->save();
            auth()->setUser($user);

            return response()->json($request->user());
        }

        return response()->json([
            'message' => 'Unauthorized',
            'errors' => ['message' => ['Your current password is incorrect.']],
        ], 401);
    }
}
