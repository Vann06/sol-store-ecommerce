<?php

namespace Tests\Unit;

use App\Models\Producto;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_producto_puede_ser_creado()
    {
        $categoria = Category::create([
            'name' => 'Electrónicos',
            'imagen' => 'https://example.com/categoria.jpg'
        ]);

        $tematica = Theme::create([
            'name' => 'Geek',
            'imagen' => 'https://example.com/tema.jpg'
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio_base' => 25.99,
            'id_categoria' => $categoria->id,
            'id_tematica' => $tematica->id,
            'status' => 'activo',
            'stock' => 10,
            'imagen' => 'https://example.com/imagen.jpg'
        ]);

        $this->assertNotNull($producto);
        $this->assertEquals('Producto Test', $producto->nombre);
        $this->assertEquals(25.99, $producto->precio_base);
        $this->assertEquals('activo', $producto->status);
        
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Test',
            'status' => 'activo'
        ]);

        echo "✅ Producto creado exitosamente:\n";
        echo "   📦 ID: {$producto->id}\n";
        echo "   🏷️  Nombre: {$producto->nombre}\n";
        echo "   📂 Categoría: {$categoria->name}\n";
        echo "   🎨 Temática: {$tematica->name}\n";
    }
}
