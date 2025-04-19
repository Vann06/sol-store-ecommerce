<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $cliente = Role::firstOrCreate([
            'nombre' => 'cliente',
        ], ['is_superadmin' => false]);

        $admin = Role::firstOrCreate([
            'nombre' => 'admin',
        ], ['is_superadmin' => true]);

        $clienteUser = User::firstOrCreate([
            'email' => 'cliente@demo.com',
        ], [
            'first_name' => 'Cliente',
            'last_name' => 'Demo',
            'password' => Hash::make('password'),
        ]);

        $adminUser = User::firstOrCreate([
            'email' => 'admin@demo.com',
        ], [
            'first_name' => 'Admin',
            'last_name' => 'Demo',
            'password' => Hash::make('admin123'),
        ]);


        $clienteUser->roles()->syncWithoutDetaching([$cliente->id]);
        $adminUser->roles()->syncWithoutDetaching([$admin->id]);

    
        $anime = Category::firstOrCreate(['name' => 'Anime']);
        $geek = Category::firstOrCreate(['name' => 'Geek']);

        $naruto = Theme::firstOrCreate(['name' => 'Naruto']);
        $onepiece = Theme::firstOrCreate(['name' => 'One Piece']);

    
        Producto::firstOrCreate([
            'nombre' => 'Figura Goku SSJ',
        ], [
            'descripcion' => 'Figura articulada de Goku Super Saiyajin.',
            'precio_base' => 35.50,
            'id_categoria' => $anime->id,
            'id_tematica' => $naruto->id,
            'status' => 'activo',
            'stock' => 15,
            'imagen' => 'products/goku.jpg'
        ]);

        Producto::firstOrCreate([
            'nombre' => 'Taza One Piece',
        ], [
            'descripcion' => 'Taza térmica con diseño de Luffy y tripulación.',
            'precio_base' => 12.99,
            'id_categoria' => $geek->id,
            'id_tematica' => $onepiece->id,
            'status' => 'activo',
            'stock' => 25,
            'imagen' => 'products/onepiece.jpg'
        ]);
    }
}
