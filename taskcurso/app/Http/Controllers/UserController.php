<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255', 
            'email'      => 'required|string|email|max:255|unique:usuarios',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        // Crear usuario y encriptar la contraseña
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => \Hash::make($validated['password']), //  encriptacion de contraseña
        ]);

        DB::table('usuarios_roles')->insert([
            'id_usuario' => $user->id,
            'id_rol' => Role::where('nombre','cliente')->first()->id,
        ]);

        

        return response()->json([
            'message' => 'Usuario registrado correctamente', 
            'user' => $user
        ], 201);
    }
}
