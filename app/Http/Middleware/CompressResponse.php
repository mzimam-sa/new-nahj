<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    /**
     * Add performance headers and ETags for responses.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add security + performance headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Add ETag for cache validation (only for successful responses)
        if ($response->isSuccessful() && $response->getContent()) {
            $etag = md5($response->getContent());
            $response->headers->set('ETag', '"' . $etag . '"');

            // If client sent If-None-Match and it matches, return 304
            $ifNoneMatch = $request->header('If-None-Match');
            if ($ifNoneMatch && trim($ifNoneMatch, '"') === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }

        return $response;
    }
}
