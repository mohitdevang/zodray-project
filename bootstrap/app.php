<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'sanctum.token' => \App\Http\Middleware\CheckSanctumToken::class,
            'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        // Apply the Sanctum token middleware to all API routes
        $middleware->group('api', [
            'sanctum.token',
        ]);

        // Redirect guests on web, but ensure API requests get JSON 401 instead of HTML
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*')) {
                return null; // No redirect for API; will yield 401 JSON
            }
            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force JSON for unauthenticated API responses
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return null; // fall back to default for non-API
        });

        // Force JSON shape for all unexpected errors on API routes
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $message = $status === 500 ? 'Server Error' : ($e->getMessage() ?: 'Error');
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            }
            return null;
        });
    })->create();
