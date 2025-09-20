<?php
// filepath: taskcurso/app/Http/Middleware/JWTAuthMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtener token desde Authorization header o cookie
            $token = $this->getTokenFromRequest($request);
            
            if (!$token) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            // Establecer el token para JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 401);
            }

            // Agregar usuario a request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Error de token'], 401);
        }

        return $next($request);
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        // Prioridad: Bearer token > Cookie
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            return $bearerToken;
        }

        // Fallback a cookie (para compatibilidad)
        return $request->cookie('access_token');
    }
}
