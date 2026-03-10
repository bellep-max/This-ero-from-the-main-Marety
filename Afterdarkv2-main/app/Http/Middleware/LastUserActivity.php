<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LastUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check()) {
            auth()->user()->update([
                'last_activity' => now()->addMinute(),
            ]);
        }

        return $next($request);
    }
}
