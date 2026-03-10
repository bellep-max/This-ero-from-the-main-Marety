<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 21:01.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Email\EmailUpdateRequest;
use App\Models\Email;
use Illuminate\Http\RedirectResponse;

class EmailController
{
    public function index()
    {
        return view('backend.email.index')
            ->with([
                'emails' => Email::query()->paginate(20),
            ]);
    }

    public function edit(Email $email)
    {
        return view('backend.email.edit')
            ->with([
                'email' => $email,
            ]);
    }

    public function update(Email $email, EmailUpdateRequest $request): RedirectResponse
    {
        $email->update($request->validated());

        return MessageHelper::redirectMessage('Email template successfully updated!', 'backend.email.index');
    }
}
