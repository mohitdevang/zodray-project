<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSanctumToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Preserve any incoming Authorization header for debugging/observability
            $originalAuth = $request->headers->get('Authorization');
            if ($originalAuth) {
                $request->headers->set('X-Original-Authorization', $originalAuth);
            }

            $hasCustomToken = $request->headers->has('X-Api-Token');
            $authorization  = (string) ($originalAuth ?? '');
            $isApiRequest   = $request->is('api/*');
            $isProduction   = app()->environment('production');

            // Accept token from multiple sources to survive header filtering on some hosts
            $headerTokenCandidates = [
                (string) $request->headers->get('X-Api-Token', ''),
                (string) $request->headers->get('X-Access-Token', ''),
                (string) $request->headers->get('X-Authorization', ''),
                (string) $request->headers->get('Api-Token', ''),
            ];
            $queryTokenCandidates = [
                (string) $request->query('api_token', ''),
                (string) $request->query('token', ''),
            ];
            $candidateTokens = array_filter(array_merge($headerTokenCandidates, $queryTokenCandidates), function ($v) {
                return is_string($v) && trim($v) !== '';
            });

            // If a custom token is provided, always normalize to Bearer for Laravel
            if ($hasCustomToken || !empty($candidateTokens)) {
                // Prefer X-Api-Token if present; otherwise first non-empty candidate
                $apiToken = trim((string) $request->headers->get('X-Api-Token', ''));
                if ($apiToken === '' && !empty($candidateTokens)) {
                    $apiToken = trim((string) reset($candidateTokens));
                }
                
                // Validate token is not empty
                if (empty($apiToken)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'X-Api-Token header is empty'
                    ], 401);
                }
                
                // Remove any existing Authorization header first (especially if it's Basic)
                $request->headers->remove('Authorization');
                
                // If original was Basic, preserve it for debugging
                if (preg_match('/^Basic\s+/i', $authorization) === 1) {
                    $request->headers->set('X-IIS-Basic-Auth', $authorization);
                }
                
                // Set the Bearer token for Laravel Sanctum
                $request->headers->set('Authorization', 'Bearer ' . $apiToken);
            } else {
                // No custom token provided.
                // On production API routes, IGNORE any Authorization header (Basic or Bearer)
                // and require X-Api-Token only. This avoids IIS Basic Auth conflicts.
                if ($isApiRequest && $isProduction) {
                    if (!empty($authorization)) {
                        // Preserve for diagnostics, but do not let Laravel see it
                        $request->headers->set('X-IIS-Basic-Auth', $authorization);
                        $request->headers->remove('Authorization');
                    }
                } else {
                    // In local/dev, allow standard Bearer tokens for convenience.
                    // Still move Basic auth out of the way to avoid confusion.
                    if (preg_match('/^Basic\s+/i', $authorization) === 1) {
                        $request->headers->set('X-IIS-Basic-Auth', $authorization);
                        $request->headers->remove('Authorization');
                    }
                }
            }

            return $next($request);
        } catch (\Exception $e) {
            // Log the error but don't expose it to the client
            \Log::error('CheckSanctumToken middleware error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication error occurred'
            ], 500);
        }
    }
}
