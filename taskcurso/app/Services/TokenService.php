<?php
// filepath: taskcurso/app/Services/TokenService.php

namespace App\Services;

use App\Models\User;
use App\Models\RefreshToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TokenService
{
    public function generateTokens(User $user, Request $request = null): array
    {
        // Generar access token JWT
        $accessToken = JWTAuth::fromUser($user);
        
        // Generar refresh token seguro
        $refreshToken = $this->generateRefreshToken($user, $request);
        
        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // en segundos
            'refresh_expires_in' => config('jwt.refresh_ttl') * 60,
        ];
    }

    private function generateRefreshToken(User $user, Request $request = null): string
    {
        // Limpiar tokens expirados del usuario
        $this->cleanUserExpiredTokens($user);
        
        // Generar token único
        $token = Str::random(64);
        
        // Guardar en base de datos (hasheado)
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl')),
            'user_agent' => $request ? $request->header('User-Agent') : null,
            'ip_address' => $request ? $request->ip() : null,
        ]);
        
        return $token;
    }

    public function refreshTokens(string $refreshToken, Request $request = null): array
    {
        $hashedToken = hash('sha256', $refreshToken);
        
        $tokenRecord = RefreshToken::where('token', $hashedToken)
            ->where('expires_at', '>', Carbon::now())
            ->first();
            
        if (!$tokenRecord) {
            throw new \Exception('Refresh token inválido o expirado');
        }
        
        $user = $tokenRecord->user;
        
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }
        
        // Revocar el refresh token usado
        $tokenRecord->delete();
        
        // Generar nuevos tokens
        return $this->generateTokens($user, $request);
    }

    public function revokeRefreshToken(string $refreshToken): void
    {
        $hashedToken = hash('sha256', $refreshToken);
        RefreshToken::where('token', $hashedToken)->delete();
    }

    public function revokeAllUserTokens(User $user): void
    {
        // Revocar refresh tokens
        RefreshToken::where('user_id', $user->id)->delete();
        
        // Invalidar JWT actual si existe
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Exception $e) {
            // Token ya inválido o no existe
        }
    }

    private function cleanUserExpiredTokens(User $user): void
    {
        RefreshToken::where('user_id', $user->id)
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }
}