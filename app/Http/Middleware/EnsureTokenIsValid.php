<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        Log::info('Token received in middleware', ['token' => $token]);

        if (Auth::guard('api')->check()) {
            Log::info('User authenticated', ['user_id' => auth()->guard('api')->user()->user_id]);
            return $next($request);
        } else {
            Log::warning('User not authenticated');
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
