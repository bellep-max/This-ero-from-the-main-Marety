<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptTermsRequest;
use Illuminate\Http\RedirectResponse;

class TermsController extends Controller
{
    public function __invoke(AcceptTermsRequest $request): RedirectResponse
    {
        if ($request->input('accept')) {
            session(['is_adult' => true]);
        }

        return redirect()->back(303);
    }
}
