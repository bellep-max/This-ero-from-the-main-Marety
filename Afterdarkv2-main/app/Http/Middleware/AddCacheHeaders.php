<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->isSuccessful()) {
            $response->header('Cache-Control', 'public, max-age=3600');
            $response->setEtag(md5($response->getContent()));
            $response->setLastModified(new \DateTime);

            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        return $response;
    }
}
