<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarritoCompra;
use App\Models\DetalleCarrito;
use App\Models\DetalleProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CarritoController extends Controller
{
    /**
     * Obtener identificadores del carrito (usuario o sesión)
     */
    private function obtenerIdentificadores(Request $request)
    {
        $usuario_id = auth()->check() ? auth()->id() : null;
        
        // Obtener session_id de forma segura
        $session_id = null;
        if (!$usuario_id) {
            try {
                // Intentar obtener session_id si las sesiones están disponibles
                if ($request->hasSession()) {
                    $session_id = $request->session()->getId();
                } else {
                    // Generar un session_id temporal basado en IP y User-Agent
                    $session_id = 'temp_' . md5(
                        $request->ip() . 
                        $request->userAgent() . 
                        date('Y-m-d')
                    );
                }
            } catch (\Exception $e) {
                // Fallback: generar session_id temporal
                $session_id = 'temp_' . Str::random(40);
            }
        }
        
        return [$usuario_id, $session_id];
    }

    public function getCarrito(Request $request)
    {
        try {
            [$usuario_id, $session_id] = $this->obtenerIdentificadores($request);

            $carrito = CarritoCompra::obtenerCarrito($usuario_id, $session_id);
            
            if (!$carrito) {
                return response()->json([
                    'carrito_id' => null,
                    'items' => [],
                    'total' => 0,
                    'cantidad_items' => 0,
                    'cantidad_total_productos' => 0,
                    'es_invitado' => !auth()->check(),
                    'session_id' => $session_id // Para debug
                ]);
            }

            $detalles = DetalleCarrito::with(['detalleProducto.producto.category', 'detalleProducto.producto.theme'])
                ->where('id_carrito', $carrito->id)
                ->get();

            $items = [];
            $total = 0;
            $cantidad_total_items = 0;

            foreach ($detalles as $detalle) {
                $producto = $detalle->detalleProducto->producto;
                if ($producto->status !== 'activo') {
                    continue;
                }
                
                $precio_unitario = $producto->precio_base;
                $cantidad = $detalle->cantidad;
                $subtotal = $precio_unitario * $cantidad;
                
                $items[] = [
                    'id' => $detalle->id,
                    'producto_id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio_unitario' => round($precio_unitario, 2),
                    'cantidad' => $cantidad,
                    'subtotal' => round($subtotal, 2),
                    'imagen' => $producto->imagen,
                    'categoria' => $producto->category->name ?? 'Sin categoría',
                    'tematica' => $producto->theme->name ?? 'Sin temática',
                ];
                
                $total += $subtotal;
                $cantidad_total_items += $cantidad;
            }

            return response()->json([
                'carrito_id' => $carrito->id,
                'items' => $items,
                'total' => round($total, 2),
                'cantidad_items' => count($items),
                'cantidad_total_productos' => $cantidad_total_items,
                'moneda' => 'Q',
                'es_invitado' => !auth()->check(),
                'requiere_login_checkout' => !auth()->check(),
                'session_id' => $session_id // Para debug
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getCarrito: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function agregarProducto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|integer|min:1|max:999',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            [$usuario_id, $session_id] = $this->obtenerIdentificadores($request);
            
            $producto_id = $request->producto_id;
            $cantidad = $request->cantidad;
            
            // Verificar que el producto esté disponible
            $producto = Producto::find($producto_id);
            if (!$producto || $producto->status !== 'activo') {
                return response()->json(['error' => 'El producto no está disponible'], 400);
            }
            
            // Obtener o crear carrito (para usuario o invitado)
            $carrito = CarritoCompra::crearOObtenerCarrito($usuario_id, $session_id);
            
            // Crear detalle_producto básico si no existe
            $detalle_producto = DetalleProducto::firstOrCreate(
                ['id_producto' => $producto_id],
                [
                    'stock' => 0,
                    'precio' => $producto->precio_base,
                    'created_by' => $usuario_id // Será null para invitados
                ]
            );
            
            // Verificar si el producto ya está en el carrito
            $detalle_existente = DetalleCarrito::where('id_carrito', $carrito->id)
                ->where('id_detalle_producto', $detalle_producto->id)
                ->first();
            
            if ($detalle_existente) {
                // Actualizar cantidad existente
                $nueva_cantidad = $detalle_existente->cantidad + $cantidad;
                $detalle_existente->update(['cantidad' => $nueva_cantidad]);
                $mensaje = 'Cantidad actualizada en el carrito';
                $detalle_carrito = $detalle_existente;
            } else {
                // Crear nuevo item en carrito
                $detalle_carrito = DetalleCarrito::create([
                    'id_carrito' => $carrito->id,
                    'id_detalle_producto' => $detalle_producto->id,
                    'cantidad' => $cantidad
                ]);
                $mensaje = 'Producto agregado al carrito';
            }
            
            // Calcular totales actualizados
            $carrito_actualizado = $this->calcularTotalesCarrito($carrito->id);
            
            return response()->json([
                'message' => $mensaje,
                'item_agregado' => [
                    'id' => $detalle_carrito->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $detalle_carrito->cantidad,
                    'precio_unitario' => $producto->precio_base,
                    'subtotal' => $producto->precio_base * $detalle_carrito->cantidad
                ],
                'carrito_totales' => $carrito_actualizado,
                'es_invitado' => !auth()->check(),
                'session_id' => $session_id // Para debug
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Error en agregarProducto: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarCantidad(Request $request, $detalle_id)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1|max:999',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        [$usuario_id, $session_id] = $this->obtenerIdentificadores($request);
        
        $carrito = CarritoCompra::obtenerCarrito($usuario_id, $session_id);
        
        if (!$carrito) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }
        
        $detalle = DetalleCarrito::where('id', $detalle_id)
            ->where('id_carrito', $carrito->id)
            ->with('detalleProducto.producto')
            ->first();
            
        if (!$detalle) {
            return response()->json(['error' => 'Producto no encontrado en el carrito'], 404);
        }
        
        $detalle->update(['cantidad' => $request->cantidad]);
        
        // Calcular nuevo subtotal
        $precio_unitario = $detalle->detalleProducto->producto->precio_base;
        $nuevo_subtotal = $precio_unitario * $request->cantidad;
        
        // Calcular totales del carrito
        $carrito_totales = $this->calcularTotalesCarrito($carrito->id);
        
        return response()->json([
            'message' => 'Cantidad actualizada',
            'item_actualizado' => [
                'id' => $detalle->id,
                'nueva_cantidad' => $request->cantidad,
                'precio_unitario' => $precio_unitario,
                'nuevo_subtotal' => round($nuevo_subtotal, 2)
            ],
            'carrito_totales' => $carrito_totales,
            'es_invitado' => !auth()->check()
        ]);
    }

    public function eliminarProducto(Request $request, $detalle_id)
    {
        [$usuario_id, $session_id] = $this->obtenerIdentificadores($request);
        
        $carrito = CarritoCompra::obtenerCarrito($usuario_id, $session_id);
        
        if (!$carrito) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }
        
        $detalle = DetalleCarrito::where('id', $detalle_id)
            ->where('id_carrito', $carrito->id)
            ->first();
            
        if (!$detalle) {
            return response()->json(['error' => 'Producto no encontrado en el carrito'], 404);
        }
        
        $detalle->delete();
        
        // Calcular totales actualizados
        $carrito_totales = $this->calcularTotalesCarrito($carrito->id);
        
        return response()->json([
            'message' => 'Producto eliminado del carrito',
            'carrito_totales' => $carrito_totales,
            'es_invitado' => !auth()->check()
        ]);
    }

    public function vaciarCarrito(Request $request)
    {
        [$usuario_id, $session_id] = $this->obtenerIdentificadores($request);
        
        $carrito = CarritoCompra::obtenerCarrito($usuario_id, $session_id);
        
        if (!$carrito) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }
        
        DetalleCarrito::where('id_carrito', $carrito->id)->delete();
        
        return response()->json([
            'message' => 'Carrito vaciado correctamente',
            'carrito_totales' => [
                'total' => 0,
                'cantidad_items' => 0,
                'cantidad_total_productos' => 0
            ],
            'es_invitado' => !auth()->check()
        ]);
    }

    /**
     * Transferir carrito al hacer login
     */
    public function transferirCarritoLogin(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $session_id = $request->session()->getId();
        $usuario_id = auth()->id();

        $carrito = CarritoCompra::transferirCarritoInvitado($session_id, $usuario_id);

        if ($carrito) {
            return response()->json([
                'message' => 'Carrito transferido exitosamente',
                'carrito_id' => $carrito->id
            ]);
        }

        return response()->json([
            'message' => 'No había carrito de invitado para transferir'
        ]);
    }

    private function calcularTotalesCarrito($carrito_id)
    {
        $total = DetalleCarrito::join('detalle_producto', 'detalle_carrito.id_detalle_producto', '=', 'detalle_producto.id')
            ->join('productos', 'detalle_producto.id_producto', '=', 'productos.id')
            ->where('detalle_carrito.id_carrito', $carrito_id)
            ->where('productos.status', 'activo')
            ->sum(DB::raw('detalle_carrito.cantidad * productos.precio_base'));
        
        $cantidad_items = DetalleCarrito::where('id_carrito', $carrito_id)->count();
        
        $cantidad_total_productos = DetalleCarrito::where('id_carrito', $carrito_id)->sum('cantidad');
        
        return [
            'total' => round($total, 2),
            'cantidad_items' => $cantidad_items,
            'cantidad_total_productos' => $cantidad_total_productos
        ];
    }
}