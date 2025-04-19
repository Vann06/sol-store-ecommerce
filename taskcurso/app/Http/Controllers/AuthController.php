<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $role = $user->roles()->first()?->nombre;

        return response()->json([
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'email' => $user->email,
                'role' => $role 
            ]
        ], 200);
    }
}
