<?php

namespace App\Http\Controllers;


use App\Models\Producto ;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;


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
            // Crear instancia de Cloudinary con configuración
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'drv2wctxj'),
                    'api_key' => env('CLOUDINARY_API_KEY', '298872777779474'),
                    'api_secret' => env('CLOUDINARY_API_SECRET', 'bPsMsut064CSWHBD1VfzEtvf6aU'),
                ],
                'url' => ['secure' => true]
            ]);
            
            $uploadResult = $cloudinary->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'sol-store/products']
            );
            
            $validated['imagen'] = $uploadResult['secure_url'];
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
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'drv2wctxj'),
                    'api_key' => env('CLOUDINARY_API_KEY', '298872777779474'),
                    'api_secret' => env('CLOUDINARY_API_SECRET', 'bPsMsut064CSWHBD1VfzEtvf6aU'),
                ],
                'url' => ['secure' => true]
            ]);
            
            $uploadResult = $cloudinary->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'sol-store/products']
            );
            
            $validated['imagen'] = $uploadResult['secure_url'];
        }

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
            $search = strtolower($request->search);
    
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(descripcion) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('category', function ($catQuery) use ($search) {
                      $catQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }
    
        return response()->json($query->get());
    }
    

    public function apiShow($id)
    {
        $producto = Producto::with(['category', 'theme'])->find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

    public function productosRecientes()
    {
        $productos = Producto::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($productos);
    }

}
