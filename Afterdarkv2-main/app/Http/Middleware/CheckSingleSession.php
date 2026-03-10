<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckSingleSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check() && config('settings.log_hash')) {
            $previousSession = auth()->user()->session_id;

            if ($previousSession !== Session::getId()) {
                Session::getHandler()->destroy($previousSession);
                $request->session()->regenerate();
                auth()->user()->session_id = Session::getId();
                auth()->user()->save();
            }
        }

        return $next($request);
    }
}
