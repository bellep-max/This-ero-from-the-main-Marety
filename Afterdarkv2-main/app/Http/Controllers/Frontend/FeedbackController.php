<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Feedback\FeedbackStoreRequest;
use App\Models\Email;
use Exception;
use Illuminate\Http\JsonResponse;
use stdClass;

class FeedbackController extends Controller
{
    public function store(FeedbackStoreRequest $request): JsonResponse
    {
        $feedback = new stdClass;
        $feedback->email = $request->input('email');
        $feedback->feeling = $request->input('feeling');
        $feedback->about = $request->input('about');
        $feedback->comment = $request->input('comment');

        try {
            (new Email)->feedback($feedback);
        } catch (Exception $e) {
        }

        return response()->json($feedback);
    }
}
