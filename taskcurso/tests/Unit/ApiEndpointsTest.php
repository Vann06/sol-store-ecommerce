<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Producto;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        echo "🔧 Configurando entorno de pruebas API...\n";
        
        $rolCliente = Role::create([
            'nombre' => 'cliente',
            'is_superadmin' => false
        ]);

        $this->usuario = User::create([
            'first_name' => 'Test',
            'last_name' => 'Usuario',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        
        $this->usuario->roles()->attach($rolCliente->id);
        
        echo "   👤 Usuario de prueba creado: {$this->usuario->email}\n";
    }

    public function test_ping_endpoint_funciona()
    {
        echo "🏓 Probando endpoint /ping...\n";
        
        $response = $this->getJson('/api/ping');
        
        $response->assertStatus(200)
                 ->assertJson(['message' => 'pong']);
        
        echo "✅ Ping endpoint funcionando correctamente\n";
    }

    public function test_registro_de_usuario_funciona()
    {
        echo "📝 Probando registro de usuario...\n";
        
        $userData = [
            'first_name' => 'Nuevo',
            'last_name' => 'Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        
        $response = $this->postJson('/api/register', $userData);
        
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'first_name', 'last_name', 'email']
                 ]);
        
        $this->assertDatabaseHas('usuarios', [
            'email' => 'nuevo@test.com',
            'first_name' => 'Nuevo'
        ]);
        
        echo "✅ Registro de usuario funcionando correctamente\n";
    }

    public function test_login_de_usuario_funciona()
    {
        echo "🔐 Probando login de usuario...\n";
        
        $loginData = [
            'email' => $this->usuario->email,
            'password' => 'password123'
        ];
        
        $response = $this->postJson('/api/login', $loginData);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'user'
                 ]);
        
        $this->token = $response->json('access_token');
        
        echo "✅ Login funcionando correctamente, token obtenido\n";
    }

    public function test_endpoint_productos_publico()
    {
        echo "📦 Probando endpoint público de productos...\n";
        
        $categoria = Category::create([
            'name' => 'Electrónicos Test',
            'imagen' => 'https://example.com/categoria.jpg'
        ]);
        
        $tematica = Theme::create([
            'name' => 'Geek Test',
            'imagen' => 'https://example.com/tema.jpg'
        ]);
        
        $producto = Producto::create([
            'nombre' => 'Producto API Test',
            'descripcion' => 'Descripción para API',
            'precio_base' => 19.99,
            'id_categoria' => $categoria->id,
            'id_tematica' => $tematica->id,
            'status' => 'activo',
            'stock' => 5,
            'imagen' => 'https://example.com/producto.jpg'
        ]);
        
        echo "   📦 Producto de prueba creado: {$producto->nombre}\n";
        
        $response = $this->getJson('/api/productos');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'nombre',
                         'descripcion',
                         'precio_base',
                         'status',
                         'category',
                         'theme'
                     ]
                 ]);
        
        $productos = $response->json();
        $this->assertCount(1, $productos);
        $this->assertEquals('Producto API Test', $productos[0]['nombre']);
        
        echo "✅ Endpoint /api/productos funcionando correctamente\n";
    }

    public function test_endpoint_producto_individual()
    {
        echo "🔍 Probando endpoint de producto individual...\n";
        
        $categoria = Category::create([
            'name' => 'Test Category',
            'imagen' => 'test.jpg'
        ]);
        
        $tematica = Theme::create([
            'name' => 'Test Theme',
            'imagen' => 'test.jpg'
        ]);
        
        $producto = Producto::create([
            'nombre' => 'Producto Individual',
            'descripcion' => 'Producto para test individual',
            'precio_base' => 29.99,
            'id_categoria' => $categoria->id,
            'id_tematica' => $tematica->id,
            'status' => 'activo',
            'stock' => 10,
            'imagen' => 'individual.jpg'
        ]);
        
        $response = $this->getJson("/api/productos/{$producto->id}");
        
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $producto->id,
                     'nombre' => 'Producto Individual',
                     'precio_base' => 29.99,
                     'status' => 'activo'
                 ])
                 ->assertJsonStructure([
                     'category' => ['id', 'name'],
                     'theme' => ['id', 'name']
                 ]);
        
        echo "✅ Endpoint /api/productos/{id} funcionando correctamente\n";
    }

    public function test_endpoint_categorias_y_tematicas()
    {
        echo "📂 Probando endpoints de categorías y temáticas...\n";
        
        $categoria1 = Category::create(['name' => 'Categoría 1', 'imagen' => 'cat1.jpg']);
        $categoria2 = Category::create(['name' => 'Categoría 2', 'imagen' => 'cat2.jpg']);
        
        $tematica1 = Theme::create(['name' => 'Temática 1', 'imagen' => 'tema1.jpg']);
        $tematica2 = Theme::create(['name' => 'Temática 2', 'imagen' => 'tema2.jpg']);
        
        $responseCategorias = $this->getJson('/api/categorias');
        $responseCategorias->assertStatus(200)
                          ->assertJsonStructure([
                              '*' => ['id', 'name', 'imagen']
                          ]);
        
        $categorias = $responseCategorias->json();
        $this->assertCount(2, $categorias);
        
        $responseTematicas = $this->getJson('/api/tematicas');
        $responseTematicas->assertStatus(200)
                         ->assertJsonStructure([
                             '*' => ['id', 'name', 'imagen']
                         ]);
        
        $tematicas = $responseTematicas->json();
        $this->assertCount(2, $tematicas);
        
        echo "✅ Endpoints /api/categorias y /api/tematicas funcionando correctamente\n";
    }

    public function test_endpoint_carrito_sin_autenticacion()
    {
        echo "🛒 Probando endpoint de carrito sin autenticación...\n";
        
        $categoria = Category::create(['name' => 'Test Cat', 'imagen' => 'test.jpg']);
        $tematica = Theme::create(['name' => 'Test Theme', 'imagen' => 'test.jpg']);
        
        $producto = Producto::create([
            'nombre' => 'Producto Carrito',
            'descripcion' => 'Para test de carrito',
            'precio_base' => 15.99,
            'id_categoria' => $categoria->id,
            'id_tematica' => $tematica->id,
            'status' => 'activo',
            'stock' => 20,
            'imagen' => 'carrito.jpg'
        ]);
        
        $response = $this->postJson('/api/carrito/agregar', [
            'producto_id' => $producto->id,
            'cantidad' => 2
        ]);
        
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'item_agregado' => [
                         'producto_id',
                         'cantidad',
                         'precio_unitario'
                     ]
                 ]);
        
        echo "✅ Sistema de carrito para invitados funcionando\n";
    }

    public function test_endpoint_faqs()
    {
        echo "❓ Probando endpoint de FAQs...\n";
        
        $response = $this->getJson('/api/faqs');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'category',
                         'faqs' => [
                             '*' => ['id', 'question', 'answer']
                         ]
                     ]
                 ]);
        
        echo "✅ Endpoint /api/faqs funcionando correctamente\n";
    }
}