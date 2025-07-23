<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\DetalleProducto;

class InventarioAdminController extends Controller
{
    public function index()
    {
        $inventario = Inventario::with('detalleProducto.producto')->get();
        return view('admin.inventario.index', compact('inventario'));
    }

    public function create()
    {
        $detalles = DetalleProducto::with('producto')->get();
        return view('admin.inventario.create', compact('detalles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_detalle_producto' => 'required|exists:detalle_producto,id',
            'stock_actual' => 'required|integer|min:0',
            'cantidad_en_produccion' => 'nullable|integer|min:0',
        ]);
        $data['fecha_actualizacion'] = now();
        Inventario::create($data);
        return redirect()->route('admin.inventario.index')->with('success', 'Inventario creado');
    }

    public function edit($id)
    {
        $item = Inventario::findOrFail($id);
        $detalles = DetalleProducto::with('producto')->get();
        return view('admin.inventario.edit', compact('item', 'detalles'));
    }

    public function update(Request $request, $id)
    {
        $item = Inventario::findOrFail($id);
        $data = $request->validate([
            'stock_actual' => 'required|integer|min:0',
            'cantidad_en_produccion' => 'nullable|integer|min:0',
        ]);
        $data['fecha_actualizacion'] = now();
        $item->update($data);
        return redirect()->route('admin.inventario.index')->with('success', 'Inventario actualizado');
    }

    public function destroy($id)
    {
        $item = Inventario::findOrFail($id);
        $item->delete();
        return redirect()->route('admin.inventario.index')->with('success', 'Inventario eliminado');
    }
}
