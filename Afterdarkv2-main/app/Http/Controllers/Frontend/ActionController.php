<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ActionStoreRequest;
use App\Services\ActionService;

class ActionController extends Controller
{
    public function __construct(private readonly ActionService $actionService) {}

    public function __invoke(ActionStoreRequest $request): void
    {
        if (auth()->check()) {
            $this->actionService->parseAction($request);
        }
    }
}
