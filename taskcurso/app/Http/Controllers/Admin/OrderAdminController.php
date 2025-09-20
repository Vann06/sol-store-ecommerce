<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class OrderAdminController extends Controller
{
    /**
     * Lista de estados válidos según nuevo requerimiento.
     */
    private array $allowedStatuses = ['Procesando','Enviado','Entregado','Cancelado'];

    /**
     * Grafo de transiciones permitidas (no se puede saltar ni retroceder).
     */
    private array $stateGraph = [
        'Procesando' => ['Enviado','Cancelado'],
        'Enviado'    => ['Entregado','Cancelado'],
        'Entregado'  => [],
        'Cancelado'  => [],
    ];

    /**
     * Mostrar listado de pedidos (con filtros opcionales)
     */
    public function index(Request $request)
    {
        $query = Pedido::with(['detalles.detalleProducto.producto', 'envio.direccion', 'user'])
            ->orderByDesc('id');

        if ($request->filled('estado') && in_array($request->estado, $this->allowedStatuses)) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $lower = strtolower($search);
            $query->where(function($q) use ($search, $lower) {
                if (is_numeric($search)) {
                    $q->where('id', $search);
                } else {
                    $q->whereHas('user', function($uq) use ($lower) {
                        $uq->whereRaw('LOWER(first_name) LIKE ?', ["%{$lower}%"]) 
                           ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$lower}%"]) 
                           ->orWhereRaw('LOWER(email) LIKE ?', ["%{$lower}%"]);
                    });
                }
            });
        }

        $pedidos = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'pedidos' => $pedidos,
            'allowed' => $this->allowedStatuses,
            'filtros' => [
                'estado' => $request->estado,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Ver detalle de un pedido.
     */
    public function show($id)
    {
        $pedido = Pedido::with(['detalles.detalleProducto.producto','envio.direccion','user'])->findOrFail($id);
        $current = $pedido->estado;
        // Normalizar legacy -> Procesando
        if (!in_array($current, $this->allowedStatuses)) {
            $current = 'Procesando';
        }
        $nextAllowed = $this->stateGraph[$current] ?? [];

        return view('admin.orders.show', [
            'pedido' => $pedido,
            'allowed' => $this->allowedStatuses,
            'nextAllowed' => $nextAllowed,
        ]);
    }

    /**
     * Actualizar estado del pedido (PUT/PATCH)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:' . implode(',', $this->allowedStatuses)
        ]);

        $pedido = Pedido::findOrFail($id);
        $from = $pedido->estado;
        $to   = $request->estado;

        // Normalizar legacy
        if (!in_array($from, $this->allowedStatuses)) {
            $from = 'Procesando';
        }

        $validTargets = $this->stateGraph[$from] ?? [];

        if ($to === $from) {
            return redirect()->route('admin.orders.show', $pedido->id)
                ->with('info', 'El pedido ya está en el estado seleccionado.');
        }

        if (!in_array($to, $validTargets)) {
            return redirect()->route('admin.orders.show', $pedido->id)
                ->with('error', 'Transición inválida de "' . $from . '" a "' . $to . '".');
        }

        $pedido->estado = $to;
        $pedido->save();

        return redirect()
            ->route('admin.orders.show', $pedido->id)
            ->with('success', 'Estado actualizado a "' . $pedido->estado . '"');
    }
}
