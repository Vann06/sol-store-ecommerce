<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use App\Models\Inventario;
use App\Models\DetalleProducto;
use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\ProductoPedido;

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
        $productosDummy = [
            [
                'nombre' => 'Figura Goku SSJ',
                'descripcion' => 'Figura articulada de Goku Super Saiyajin.',
                'precio_base' => 35.50,
                'id_categoria' => $anime->id,
                'id_tematica' => $naruto->id,
                'status' => 'activo',
                'stock' => 15,
                'imagen' => 'https://res.cloudinary.com/drv2wctxj/image/upload/v1746934290/sol-store/products/bbcthfqef3ip1ycclgqe.jpg'
            ],
            [
                'nombre' => 'Taza One Piece',
                'descripcion' => 'Taza térmica con diseño de Luffy y tripulación.',
                'precio_base' => 12.99,
                'id_categoria' => $geek->id,
                'id_tematica' => $onepiece->id,
                'status' => 'activo',
                'stock' => 25,
                'imagen' => 'https://res.cloudinary.com/drv2wctxj/image/upload/v1746934190/sol-store/products/ysfywvepllsn0v3bqcg7.jpg'
            ]
        ];

        $productosCreados = [];
        $detallesCreados = [];
        foreach ($productosDummy as $prodData) {
            $producto = Producto::firstOrCreate([
                'nombre' => $prodData['nombre'],
                'descripcion' => $prodData['descripcion'],
                'precio_base' => $prodData['precio_base'],
                'id_categoria' => $prodData['id_categoria'],
                'id_tematica' => $prodData['id_tematica'],
                'status' => $prodData['status'],
                'stock' => $prodData['stock'],
                'imagen' => $prodData['imagen'],
            ]);
            $productosCreados[] = $producto;
            $detalle = DetalleProducto::firstOrCreate([
                'id_producto' => $producto->id,
            ], [
                'stock' => rand(5, 50),
                'precio' => $producto->precio_base,
                'created_by' => $adminUser->id,
            ]);
            $detallesCreados[$producto->id] = $detalle;
            Inventario::firstOrCreate([
                'id_detalle_producto' => $detalle->id,
            ], [
                'stock_actual' => $detalle->stock,
                'cantidad_en_produccion' => rand(0, 10),
                'fecha_actualizacion' => Carbon::now(),
            ]);
        }

        $faqCatGeneral = FaqCategory::firstOrCreate(['name' => 'General']);
        $faqCatPagos = FaqCategory::firstOrCreate(['name' => 'Pagos']);
        $faqCatCuenta = FaqCategory::firstOrCreate(['name' => 'Cuenta']);

        Faq::firstOrCreate([
            'question' => '¿Cómo puedo registrarme?',
            'answer' => 'Haz clic en el botón de registro y completa el formulario.',
            'faq_category_id' => $faqCatGeneral->id
        ]);
        Faq::firstOrCreate([
            'question' => '¿Qué métodos de pago aceptan?',
            'answer' => 'Aceptamos tarjetas de crédito y PayPal.',
            'faq_category_id' => $faqCatPagos->id
        ]);
        Faq::firstOrCreate([
            'question' => '¿Cómo recupero mi contraseña?',
            'answer' => 'Utiliza la opción "Olvidé mi contraseña" en la página de inicio de sesión.',
            'faq_category_id' => $faqCatCuenta->id
        ]);

        $pedidosDummy = [
            [
                'id_usuario' => $clienteUser->id,
                'estado' => 'pendiente',
                'fecha_pedido' => Carbon::now()->subDays(3),
                'productos' => [$productosCreados[0]],
            ],
            [
                'id_usuario' => $clienteUser->id,
                'estado' => 'en produccion',
                'fecha_pedido' => Carbon::now()->subDays(2),
                'productos' => [$productosCreados[0], $productosCreados[1]],
            ],
            [
                'id_usuario' => $adminUser->id,
                'estado' => 'enviado',
                'fecha_pedido' => Carbon::now()->subDay(),
                'productos' => [$productosCreados[1]],
            ],
            [
                'id_usuario' => $adminUser->id,
                'estado' => 'entregado',
                'fecha_pedido' => Carbon::now(),
                'productos' => [$productosCreados[0], $productosCreados[1]],
            ],
        ];

        foreach ($pedidosDummy as $pedidoData) {
            $productosAsociados = $pedidoData['productos'];
            unset($pedidoData['productos']);
            // Asegura que el usuario existe
            if (!User::find($pedidoData['id_usuario'])) {
                User::firstOrCreate([
                    'id' => $pedidoData['id_usuario'],
                    'first_name' => 'Usuario',
                    'last_name' => 'Dummy',
                    'email' => 'dummy' . $pedidoData['id_usuario'] . '@demo.com',
                    'password' => Hash::make('password'),
                ]);
            }
            $pedido = \App\Models\Pedido::create($pedidoData);

            foreach ($productosAsociados as $producto) {
                $detalleProducto = isset($detallesCreados[$producto->id]) ? $detallesCreados[$producto->id] : null;
                if ($detalleProducto) {
                    \App\Models\DetallePedido::firstOrCreate([
                        'id_pedido' => $pedido->id,
                        'id_detalle_producto' => $detalleProducto->id,
                    ], [
                        'id_pedido' => $pedido->id,
                        'id_detalle_producto' => $detalleProducto->id,
                        'cantidad' => rand(1, 3),
                        'precio_unitario' => $producto->precio_base,
                    ]);
                }
            }
        }

    }
}
