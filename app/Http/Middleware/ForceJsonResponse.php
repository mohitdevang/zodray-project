<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            // Ensure downstream (auth/exception handlers) treat as API / JSON
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }

        /** @var Response $response */
        $response = $next($request);

        if ($request->is('api/*')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
