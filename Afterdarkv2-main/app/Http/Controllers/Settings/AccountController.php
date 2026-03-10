<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Password\PasswordResetRequest;
use App\Http\Requests\Frontend\Password\PasswordUpdateRequest;
use App\Http\Requests\Settings\AccountUpdateRequest;
use App\Models\User;
use App\Services\PasswordService;
use App\Services\UserService;
use App\Services\VerificationService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    private PasswordService $passwordService;

    private UserService $userService;

    private VerificationService $verificationService;

    public function __construct(
        PasswordService $passwordService,
        UserService $userService,
        VerificationService $verificationService,
    ) {
        $this->passwordService = $passwordService;
        $this->userService = $userService;
        $this->verificationService = $verificationService;
    }

    public function edit(): Response
    {
        return Inertia::render('Settings/Account');
    }

    public function update(AccountUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->all());

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Your profile settings have been updated',
        ]);

        return to_route('settings.account.edit');
    }

    public function sendResetPassword(PasswordResetRequest $request): JsonResponse
    {
        $this->passwordService->resetPassword($request->input('email'));

        return response()->json([
            'message' => __('passwords.sent'),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $row = DB::table('password_resets')
            ->where('token', '=', $request->route('token'))
            ->first();

        if (!$row->email) {
            abort(403, 'Reset password token is invalid.');
        }

        auth()->logout();

        $user = User::query()
            ->where('email', '=', $row->email)
            ->firstOrFail();

        auth()->loginUsingId($user->id);

        $view = View::make('account.reset-password')
            ->with([
                'token' => $request->route('token'),
            ]);

        if ($request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK);
        }

        getMetatags();

        return $view;
    }

    public function setNewPassword(PasswordUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $email = DB::table('password_resets')->where('token', $data['token'])->value('email');

        if ($email !== auth()->user()->email) {
            abort(403, 'Reset password token is invalid.');
        }

        $user = $this->passwordService->setPassword($data);

        Auth::setUser($user);

        return response()->json($request->user());
    }

    public function verifyEmail(Request $request)
    {
        return view('account.verify-email')
            ->with([
                'message' => $this->verificationService->verifyEmail($request->route('code')),
            ]);
    }

    public function deleteProfile()
    {
        return $this->userService->delete(auth()->id());
    }
}
