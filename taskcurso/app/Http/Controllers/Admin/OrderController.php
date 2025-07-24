<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::query();

        if ($request->filled('status')) {
            $query->where('estado', $request->status);
        }

        if ($request->filled('client')) {
            $query->whereHas('usuario', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('fecha_pedido', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('fecha_pedido', '<=', $request->to_date);
        }

        $orders = $query->with('usuario')->orderBy('fecha_pedido', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Pedido $pedido)
    {
        return view('admin.orders.show', compact('pedido'));
    }

    public function updateStatus(Request $request, Pedido $pedido)
    {

        $request->validate([
            'status' => 'required|in:Imprimiendo,Pintando,Enviando,Entregado'
        ]);

        $pedido->estado = $request->status;
        $pedido->save();

        return redirect()->back()->with('success', 'Estado del pedido actualizado.');
    }
}

