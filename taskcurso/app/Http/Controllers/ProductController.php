<?php

namespace App\Http\Controllers;


use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('search')) {
            $query->where('nombre', 'LIKE', '%' . $request->search . '%');
        }

        $productos = $query->with(['category', 'theme'])->get();

        return view('admin.products.index', ['products' => $productos]); // <- usa 'products' aquí
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        $themes = \App\Models\Theme::all();

        return view('admin.products.create', compact('categories', 'themes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categories,id',
            'id_tematica' => 'required|exists:themes,id',
            'descripcion' => 'nullable|string',
            'precio_base' => 'required|numeric',
            'imagen' => 'nullable|image|max:2048',
            'status' => 'in:activo,inactivo',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('products', 'public');
        }

        $validated['created_by'] = auth()->id(); // si usas auth

        \App\Models\Producto::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto guardado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $product)
    {
        $categories = \App\Models\Category::all();
        $themes = \App\Models\Theme::all();

        return view('admin.products.edit', compact('product', 'categories', 'themes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $product)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categories,id',
            'id_tematica' => 'required|exists:themes,id',
            'descripcion' => 'nullable|string',
            'precio_base' => 'required|numeric',
            'imagen' => 'nullable|image|max:2048',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:activo,inactivo',
        ]);

        // Si se sube una nueva imagen, reemplazarla
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('products', 'public');
        }

        $validated['updated_by'] = auth()->id(); // solo si usás auth

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado exitosamente!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente.');
    }

    
    /**
     * JSON para utilziarlo en el frontend
     */
    public function apiIndex(Request $request)
    {
        $query = Producto::query()->with(['category', 'theme']);

        if ($request->has('search')) {
            $query->where('nombre', 'LIKE', '%' . $request->search . '%');
        }

        return response()->json($query->get());
    }

}
