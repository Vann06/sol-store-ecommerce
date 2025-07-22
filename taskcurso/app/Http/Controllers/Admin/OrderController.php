<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return view('admin.orders.index', compact('orders'));
    }


    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (auth()->user()->role !== 'trabajador') {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'status' => 'required|in:pendiente,en produccion,enviado,entregado'
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Estado del pedido actualizado.');
    }
}

?>