<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheStaticAssets
{
    /**
     * Add cache headers for better performance.
     * Static assets get long cache, API/pages get no-cache.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $path = $request->path();

        // Static assets: cache for 1 year
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot|webp|mp4|webm)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            return $response;
        }

        // HTML pages: no cache (dynamic content)
        if ($request->expectsJson() === false && !$request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-cache, private');
        }

        return $response;
    }
}
