<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OmniRouteAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // OmniRoute API key doğrulama
        $apiKey = $request->header('X-OmniRoute-Key') ?? $request->query('api_key');
        
        if (!$apiKey || $apiKey !== env('OMNIR_ROUTE_API_KEY')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid OmniRoute API key'
            ], 401);
        }

        return $next($request);
    }
}
