<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\AcceptTermsRequest;
use Illuminate\Http\JsonResponse;

class TermsController extends ApiController
{
    public function accept(AcceptTermsRequest $request): JsonResponse
    {
        if ($request->input('accept')) {
            session(['is_adult' => true]);
        }

        return $this->success(null, 'Terms accepted');
    }
}
