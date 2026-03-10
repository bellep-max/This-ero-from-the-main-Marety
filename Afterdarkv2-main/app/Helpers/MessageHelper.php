<?php

namespace App\Helpers;

use App\Constants\StatusConstants;
use Illuminate\Http\RedirectResponse;

class MessageHelper
{
    public static function redirectMessage(string $message, string $route = '', string $status = StatusConstants::SUCCESS): RedirectResponse
    {
        return !$route
            ? redirect()->back()
                ->with([
                    'status' => $status,
                    'message' => $message,
                ])
            : redirect()->route($route)
                ->with([
                    'status' => $status,
                    'message' => $message,
                ]);
    }
}
