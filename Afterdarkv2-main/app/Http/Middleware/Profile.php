<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Profile
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     */
    public function handle($request, Closure $next): mixed
    {
        if ($request->username == auth()->user()->username) {
            return $next($request);
        }

        return redirect()->route('frontend.homepage');
    }
}
