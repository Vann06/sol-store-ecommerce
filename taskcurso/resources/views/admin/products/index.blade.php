@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Productos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Panel</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.products.create') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">
                + Agregar Producto
            </a>
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..."
                       class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600">
            </form>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                    <th class="py-2 px-4">#</th>
                    <th class="py-2 px-4">Nombre</th>
                    <th class="py-2 px-4">Precio</th>
                    <th class="py-2 px-4">Stock</th>
                    <th class="py-2 px-4">Categoría</th>
                    <th class="py-2 px-4">Temática</th>
                    <th class="py-2 px-4">Estado</th>
                    <th class="py-2 px-4">Imagen</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-2 px-4">{{ $loop->iteration }}</td>
                        <td class="py-2 px-4">{{ $product->nombre }}</td>
                        <td class="py-2 px-4">${{ number_format($product->precio_base, 2) }}</td>
                        <td class="py-2 px-4">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</td>
                        <td class="py-2 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ $product->theme->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ ucfirst($product->status) }}</td>
                        <td class="py-2 px-4">
                            @if ($product->imagen)
                                <img src="{{ asset('storage/' . $product->imagen) }}" class="h-10 w-10 rounded">
                            @else
                                <span class="text-xs text-gray-500">Sin imagen</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.products.edit', $product->id) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition">
            Editar
        </a>
        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                Eliminar
            </button>
        </form>
    </div>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-gray-500">No hay productos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
