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

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            \DB::table('detalle_pedidos')->truncate();
            \DB::table('pedidos')->truncate();
        }

        $cliente = Role::firstOrCreate(['nombre' => 'cliente'], ['is_superadmin' => false]);
        $admin = Role::firstOrCreate(['nombre' => 'admin'], ['is_superadmin' => true]);
        $trabajador = Role::firstOrCreate(['nombre' => 'trabajador'], ['is_superadmin' => false]);

        $clienteUser = User::firstOrCreate(
            ['email' => 'cliente@demo.com'],
            ['first_name' => 'Cliente', 'last_name' => 'Demo', 'password' => Hash::make('password')]
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            ['first_name' => 'Admin', 'last_name' => 'Demo', 'password' => Hash::make('admin123')]
        );

        $clienteUser->roles()->syncWithoutDetaching([$cliente->id]);
        $adminUser->roles()->sync([$admin->id, $trabajador->id]);

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
                'imagen' => 'https://res.cloudinary.com/drv2wctxj/image/upload/v1746934290/sol-store/products/bbcthfqef3ip1ycclgqe.jpg',
            ],
            [
                'nombre' => 'Taza One Piece',
                'descripcion' => 'Taza térmica con diseño de Luffy y tripulación.',
                'precio_base' => 12.99,
                'id_categoria' => $geek->id,
                'id_tematica' => $onepiece->id,
                'status' => 'activo',
                'stock' => 25,
                'imagen' => 'https://res.cloudinary.com/drv2wctxj/image/upload/v1746934190/sol-store/products/ysfywvepllsn0v3bqcg7.jpg',
            ],
        ];

        $productosCreados = [];
        $detallesCreados = [];

        foreach ($productosDummy as $prodData) {
            $producto = Producto::firstOrCreate($prodData);
            $productosCreados[] = $producto;

            $detalle = DetalleProducto::firstOrCreate(
                ['id_producto' => $producto->id],
                [
                    'stock' => rand(5, 50),
                    'precio' => $producto->precio_base,
                    'created_by' => $adminUser->id,
                ]
            );

            $detallesCreados[$producto->id] = $detalle;

            Inventario::firstOrCreate(
                ['id_detalle_producto' => $detalle->id],
                [
                    'stock_actual' => $detalle->stock,
                    'cantidad_en_produccion' => rand(0, 10),
                    'fecha_actualizacion' => Carbon::now(),
                ]
            );
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

        // Map legacy estados to the database-allowed values to avoid CHECK constraint failures on Postgres.
        $estadoMap = [
            'Imprimiendo' => 'Procesando',
            'Pintando' => 'Procesando',
            'Enviando' => 'Enviado',
            'Entregado' => 'Entregado',
        ];

        $pedidosDummy = [
            [
                'id_usuario' => $clienteUser->id,
                'estado' => $estadoMap['Imprimiendo'],
                'fecha_pedido' => Carbon::now()->subDays(3),
                'productos' => [$productosCreados[0]],
            ],
            [
                'id_usuario' => $clienteUser->id,
                'estado' => $estadoMap['Pintando'],
                'fecha_pedido' => Carbon::now()->subDays(2),
                'productos' => [$productosCreados[0], $productosCreados[1]],
            ],
            [
                'id_usuario' => $adminUser->id,
                'estado' => $estadoMap['Enviando'],
                'fecha_pedido' => Carbon::now()->subDay(),
                'productos' => [$productosCreados[1]],
            ],
            [
                'id_usuario' => $adminUser->id,
                'estado' => $estadoMap['Entregado'],
                'fecha_pedido' => Carbon::now(),
                'productos' => [$productosCreados[0], $productosCreados[1]],
            ],
        ];

        // Create a few deterministic sample pedidos (idempotent)
        foreach ($pedidosDummy as $pedidoData) {
            $productosAsociados = $pedidoData['productos'];
            unset($pedidoData['productos']);

            // Avoid creating duplicate pedidos by looking up an existing one
            // matching user, estado and fecha_pedido (date only)
            $existing = Pedido::where('id_usuario', $pedidoData['id_usuario'])
                ->where('estado', $pedidoData['estado'])
                ->whereDate('fecha_pedido', $pedidoData['fecha_pedido']->format('Y-m-d'))
                ->first();

            if ($existing) {
                $pedido = $existing;
            } else {
                $pedido = Pedido::create($pedidoData);
            }

            foreach ($productosAsociados as $producto) {
                $detalleProducto = $detallesCreados[$producto->id] ?? null;

                if ($detalleProducto) {
                    // ensure detalle_pedidos is not duplicated
                    DetallePedido::firstOrCreate(
                        ['id_pedido' => $pedido->id, 'id_detalle_producto' => $detalleProducto->id],
                        [
                            'cantidad' => rand(1, 3),
                            'precio_unitario' => $producto->precio_base,
                        ]
                    );
                }
            }
        }
        
    // Use mapped/allowed estados for generated pedidos to match DB CHECK constraint.
    $estadosPedidos = [$estadoMap['Imprimiendo'], $estadoMap['Pintando'], $estadoMap['Enviando'], $estadoMap['Entregado']];
        // Create a small set of random pedidos (idempotent by checking date+user)
        for ($i = 1; $i <= 10; $i++) {
            $fecha_random = now()->subDays(rand(1, 30));
            // choose an existing user id (prefer clienteUser, adminUser)
            $user_id_random = (rand(0, 1) === 0) ? $clienteUser->id : $adminUser->id;
            $estado_random = $estadosPedidos[array_rand($estadosPedidos)];

            $existing = \App\Models\Pedido::where('id_usuario', $user_id_random)
                ->where('estado', $estado_random)
                ->whereDate('fecha_pedido', $fecha_random->format('Y-m-d'))
                ->first();

            if ($existing) {
                $pedido = $existing;
            } else {
                $pedido = \App\Models\Pedido::create([
                    'id_usuario' => $user_id_random,
                    'fecha_pedido' => $fecha_random,
                    'estado' => $estado_random,
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ]);
            }

            if ($pedido->estado === 'Entregado') {
                // Avoid duplicating historial_venta for same pedido
                \App\Models\HistorialVenta::firstOrCreate([
                    'id_pedido' => $pedido->id,
                    'fecha_venta' => $pedido->fecha_pedido,
                ], [
                    'monto_total' => rand(50, 500) + (rand(0, 99) / 100),
                ]);
            }
        }

        // Monthly sales data: create only when not present to avoid bulk duplicates
        for ($mes = 1; $mes <= 12; $mes++) {
            // Reduce monthly generated sales for lighter demo data
            // previously rand(3,8) produced many pedidos; use rand(0,2) to keep demo small
            $ventasDelMes = rand(0, 2);
            for ($v = 1; $v <= $ventasDelMes; $v++) {
                $fechaVenta = now()->setMonth($mes)->setDay(rand(1, 28))->startOfDay();

                $existingVenta = \App\Models\HistorialVenta::whereDate('fecha_venta', $fechaVenta->format('Y-m-d'))
                    ->first();

                if ($existingVenta) {
                    continue; // skip - already have a sale for that date
                }

                $pedidoVenta = \App\Models\Pedido::create([
                    'id_usuario' => rand(1, 2),
                    'fecha_pedido' => $fechaVenta,
                    'estado' => 'Entregado',
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ]);

                \App\Models\HistorialVenta::create([
                    'id_pedido' => $pedidoVenta->id,
                    'fecha_venta' => $fechaVenta,
                    'monto_total' => rand(25, 300) + (rand(0, 99) / 100),
                ]);
            }
        }

    }
}
