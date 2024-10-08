<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
class Authenticate
{
    public function handle($request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'statuscode' => 401,
                    'data' => null,
                    'message' => 'User not found'
                ], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'statuscode' => 401,
                'data' => null,
                'message' => 'Token has expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'statuscode' => 401,
                'data' => null,
                'message' => 'Token is invalid'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'statuscode' => 401,
                'data' => null,
                'message' => 'Token not found'
            ], 401);
        }

        return $next($request);
    }
}
