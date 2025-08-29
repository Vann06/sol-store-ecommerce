<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\CarritoCompra;
use App\Models\DetalleCarrito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Usuario no autenticado'], 401);

        // Soporta enviar id de dirección y/o texto
        $direccion = $request->input('direccion_envio');
        $direccionId = $request->input('direccion_id');
        if (!$direccion && !$direccionId) {
            return response()->json(['error' => 'Dirección de envío requerida'], 422);
        }

        $carrito = CarritoCompra::obtenerCarrito($user->id, null);
        if (!$carrito) return response()->json(['error' => 'Carrito no encontrado'], 404);

        $detalles = DetalleCarrito::with(['detalleProducto.producto'])
            ->where('id_carrito', $carrito->id)->get();
        if ($detalles->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío'], 400);
        }

    return DB::transaction(function () use ($detalles, $user, $direccion, $direccionId, $carrito) {
            $total = 0;
            foreach ($detalles as $d) {
                $prod = $d->detalleProducto->producto ?? null;
                if ($prod && $prod->status === 'activo') {
                    $total += ($prod->precio_base ?? 0) * ($d->cantidad ?? 1);
                }
            }

            // Nota: la migración de pedidos define un enum distinto; usamos 'Imprimiendo' como estado inicial válido
            $pedido = Pedido::create([
                'id_usuario' => $user->id,
                'fecha_pedido' => now(),
                'estado' => 'Procesando',
            ]);

            foreach ($detalles as $d) {
                $prod = $d->detalleProducto->producto ?? null;
                if (!$prod || $prod->status !== 'activo') continue;

                // Importante: la tabla detalle_pedidos requiere id_detalle_producto, no id_producto
                DetallePedido::create([
                    'id_pedido' => $pedido->id,
                    'id_detalle_producto' => $d->id_detalle_producto,
                    'cantidad' => $d->cantidad ?? 1,
                    'precio_unitario' => $prod->precio_base ?? 0,
                ]);
            }

            DetalleCarrito::where('id_carrito', $carrito->id)->delete();

            // Crear registro de envío con la dirección asociada
            if ($direccionId) {
                \App\Models\Envio::create([
                    'id_pedido' => $pedido->id,
                    'id_direccion' => $direccionId,
                    'fecha_envio' => null,
                ]);
            }

            return response()->json([
                'message' => 'Pedido creado',
                'pedido' => [ 'id' => $pedido->id, 'estado' => $pedido->estado ]
            ], 201);
        });
    }

    public function misPedidos(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Usuario no autenticado'], 401);

        // Incluir relaciones necesarias para mostrar los productos en el frontend
        $pedidos = Pedido::with(['detalles.detalleProducto.producto', 'envio'])
            ->where('id_usuario', $user->id)
            ->orderByDesc('id')
            ->get();
        return response()->json($pedidos);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        // Cargar detalles con producto para vista de detalle
        $pedido = Pedido::with(['detalles.detalleProducto.producto', 'envio'])->findOrFail($id);
        if ($pedido->id_usuario !== $user->id && !($user->is_admin ?? false)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($pedido);
    }

    public function actualizarEstado(Request $request, $id)
    {
        $user = $request->user();
        if (!($user && ($user->is_admin ?? false))) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $estado = $request->input('estado');
        if (!in_array($estado, ['Procesando','Enviado','Entregado','Cancelado'])) {
            return response()->json(['error' => 'Estado inválido'], 422);
        }
        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $estado;
        $pedido->save();

        return response()->json(['message' => 'Estado actualizado', 'pedido' => $pedido]);
    }
}