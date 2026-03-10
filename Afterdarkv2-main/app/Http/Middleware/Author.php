<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Author
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user
            ?: $request->user();

        if ($user->id === auth()->id()) {
            return $next($request);
        }

        return redirect()->back();
    }
}
