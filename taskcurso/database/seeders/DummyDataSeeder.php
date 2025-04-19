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
        'is_superadmin' => false
    ]);

    $admin = Role::firstOrCreate([
        'nombre' => 'admin',
        'is_superadmin' => true
    ]);

    $clienteUser = User::create([
        'first_name' => 'Cliente',
        'last_name' => 'Demo',
        'email' => 'cliente@demo.com',
        'password' => Hash::make('password'),
    ]);

    $adminUser = User::create([
        'first_name' => 'Admin',
        'last_name' => 'Demo',
        'email' => 'admin@demo.com',
        'password' => Hash::make('admin123'),
    ]);

      // Asignar roles usando tabla intermedia usuarios_roles
      $clienteUser->roles()->attach($cliente->id);
      $adminUser->roles()->attach($admin->id);


    $anime = Category::create(['name' => 'Anime']);
    $geek = Category::create(['name' => 'Geek']);

    $naruto = Theme::create(['name' => 'Naruto']);
    $onepiece = Theme::create(['name' => 'One Piece']);

    Producto::create([
        'nombre' => 'Figura Goku SSJ',
        'descripcion' => 'Figura articulada de Goku Super Saiyajin.',
        'precio_base' => 35.50,
        'id_categoria' => $anime->id,
        'id_tematica' => $naruto->id,
        'status' => 'activo',
        'stock' => 15,
        'imagen' => 'products/goku.jpg'
    ]);

    Producto::create([
        'nombre' => 'Taza One Piece',
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
