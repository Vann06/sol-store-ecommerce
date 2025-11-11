<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\HistorialVenta;

class ReportsDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Usuarios demo mínimos
            if (User::count() === 0) {
                User::create([
                    'first_name' => 'Admin',
                    'last_name'  => 'Demo',
                    'email'      => 'admin_demo@example.com',
                    'password'   => 'demo1234', // hashed por el cast del modelo
                    'id_rol'     => 1,
                ]);
                User::create([
                    'first_name' => 'Cliente',
                    'last_name'  => 'Demo',
                    'email'      => 'cliente_demo@example.com',
                    'password'   => 'demo1234',
                    'id_rol'     => 2,
                ]);
            }

            // Categorías y Temáticas demo
            if (Category::count() === 0) {
                foreach (['Posters', 'Camisetas', 'Tazas'] as $name) {
                    Category::create(['name' => $name]);
                }
            }

            if (Theme::count() === 0) {
                foreach (['Minimal', 'Retro', 'Geek'] as $name) {
                    Theme::create(['name' => $name]);
                }
            }

            // Productos demo
            if (Producto::count() < 12) {
                $catIds = Category::pluck('id');
                $themeIds = Theme::pluck('id');

                for ($i = Producto::count(); $i < 12; $i++) {
                    Producto::create([
                        'nombre'       => 'Producto Demo ' . ($i + 1),
                        'id_categoria' => $catIds->random(),
                        'id_tematica'  => $themeIds->random(),
                        'descripcion'  => 'Producto de ejemplo para reportes',
                        'precio_base'  => rand(10, 80),
                        'stock'        => rand(5, 50),
                    ]);
                }
            }

            // Pedidos demo (con usuarios válidos)
            $userIds = User::pluck('id');
            // Estados actuales permitidos según constraint pgsql (ver migración update_pedidos_estado_enum)
            $estadoPool = ['Procesando', 'Enviado', 'Entregado', 'Cancelado'];

            if (Pedido::count() < 20) {
                $target = 20 - Pedido::count();
                for ($i = 0; $i < $target; $i++) {
                    Pedido::create([
                        'id_usuario'   => $userIds->random(),
                        'fecha_pedido' => now()->subDays(rand(1, 45)),
                        'estado'       => $estadoPool[array_rand($estadoPool)],
                    ]);
                }
            }

            // Ventas demo: una por pedido que no tenga aún
            $pedidoIdsSinVenta = Pedido::leftJoin('historial_ventas', 'historial_ventas.id_pedido', '=', 'pedidos.id')
                ->whereNull('historial_ventas.id_pedido')
                ->select('pedidos.id')
                ->pluck('id');

            foreach ($pedidoIdsSinVenta as $pid) {
                HistorialVenta::create([
                    'id_pedido'  => $pid,
                    'monto_total'=> rand(25, 250) + (rand(0, 99) / 100),
                    'fecha_venta'=> now()->subDays(rand(0, 45)),
                ]);
            }
        });
    }
}
