<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'trackingNumber' => $order->tracking_number ?? null,
            'timestamps' => [
                'painting'  => $order->painting_at,
                'printing'  => $order->printed_at,
                'shipping'  => $order->shipped_at,
                'delivered' => $order->delivered_at,
            ],
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Verifica que solo trabajadores puedan actualizar
        if (auth()->user()->role !== 'trabajador') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validación del estado
        $request->validate([
            'status' => 'required|in:pendiente,en produccion,enviado,entregado'
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Estado del pedido actualizado',
            'order' => $order
        ]);
    }
}
