<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Locale
{
    public function handle(Request $request, Closure $next)
    {
        $language = Session::get('website_language', config('app.locale'));
        App::setLocale($language);

        return $next($request);
    }
}
