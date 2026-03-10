<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\ActionStoreRequest;
use App\Services\ActionService;
use Illuminate\Http\JsonResponse;

class ActionController extends ApiController
{
    public function __construct(private readonly ActionService $actionService) {}

    public function store(ActionStoreRequest $request): JsonResponse
    {
        if (auth()->check()) {
            $this->actionService->parseAction($request);
        }

        return $this->success(null, 'Action logged');
    }
}
