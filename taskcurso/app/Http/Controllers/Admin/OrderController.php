<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Maintain backward compatibility with the rebase: the view expects
        // variables named `pedidos`, `allowed` and `filtros`. Build them here.
        $query = Pedido::query();

        // support both legacy and new query param names
        $estadoParam = $request->input('estado', $request->input('status'));

        if (!empty($estadoParam)) {
            $query->where('estado', $estadoParam);
        }

        // Support both legacy 'client' and new 'search' param.
        $search = $request->input('search', $request->input('client'));
        if (!empty($search)) {
            // If numeric, try matching by id
            if (is_numeric($search)) {
                $query->where('id', intval($search));
            } else {
                $query->whereHas('usuario', function($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('fecha_pedido', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('fecha_pedido', '<=', $request->to_date);
        }

        $pedidos = $query->with(['usuario','detalles','envio.direccion','user'])->orderBy('fecha_pedido', 'desc')->paginate(15);

    // Provide the allowed statuses (keep in sync with newer controller)
    $allowed = ['Procesando','Enviado','Entregado','Cancelado'];

        $filtros = [
            'estado' => $request->input('estado', $request->input('status')),
            'search' => $request->input('search', $request->input('client')),
        ];

        return view('admin.orders.index', compact('pedidos', 'allowed', 'filtros'));
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

