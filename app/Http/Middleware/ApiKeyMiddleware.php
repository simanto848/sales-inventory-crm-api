<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('services.integration.api_key', 'simulated-integration-key-12345');

        if (!$apiKey || $apiKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Key.',
                'data' => null
            ], 401);
        }

        return $next($request);
    }
}
