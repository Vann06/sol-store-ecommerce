<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    /**
     * Redirigir al usuario a Google para autenticación
     */
    public function redirectToGoogle()
    {
        try {
            // Configurar explícitamente el redirect_uri
            $redirectUri = config('services.google.redirect');
            
            $url = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUri)
                ->redirect()
                ->getTargetUrl();
            
            return response()->json([
                'url' => $url,
                'redirect_uri' => $redirectUri // Para debugging
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar URL de Google',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manejar el callback de Google
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Obtener el código de autorización
            $code = $request->input('code');
            
            if (!$code) {
                return response()->json([
                    'error' => 'Código de autorización no proporcionado'
                ], 400);
            }

            // Configurar explícitamente el redirect_uri
            $redirectUri = config('services.google.redirect');

            // Obtener información del usuario desde Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUri)
                ->user();

            // Buscar o crear el usuario en la base de datos
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Crear nuevo usuario
                $user = User::create([
                    'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), // Contraseña aleatoria
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);

                // Asignar rol de cliente por defecto
                $clientRole = \App\Models\Role::where('nombre', 'cliente')->first();
                if ($clientRole) {
                    $user->roles()->attach($clientRole->id);
                }
            } else {
                // Actualizar google_id si no existe
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            }

            // Generar token JWT
            $token = JWTAuth::fromUser($user);

            // Obtener rol del usuario
            $role = $user->roles()->first()?->nombre ?? 'cliente';

            // Preparar datos del usuario
            $userData = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $role,
            ];

            // URL del frontend
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            // Redirigir al frontend con el token y datos del usuario
            return redirect()->away(
                $frontendUrl . '/auth/google/callback?' . http_build_query([
                    'token' => $token,
                    'user' => base64_encode(json_encode($userData))
                ])
            );

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en autenticación con Google',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}