<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Producto;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
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
