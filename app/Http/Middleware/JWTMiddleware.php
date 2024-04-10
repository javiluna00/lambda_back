<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            JWTAuth::parseToken()->authenticate();
        }catch(Exception $e)
        {
            if($e instanceof TokenInvalidException)
            {
                return response()->json(['message' => 'token_invalid'], 403);
            }
            if($e instanceof TokenExpiredException)
            {
                return response()->json(['message' => 'token_expired'], 401);
            }
            return response()->json(['message' => 'token_not_found'], 401);
        }

        return $next($request);
    }
}
