@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Inventario</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Panel</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.inventario.create') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">
                + Añadir
            </a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                    <th class="py-2 px-4">#</th>
                    <th class="py-2 px-4">Producto</th>
                    <th class="py-2 px-4">Stock Actual</th>
                    <th class="py-2 px-4">En Producción</th>
                    <th class="py-2 px-4">Última Actualización</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inventario as $item)
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-2 px-4">{{ $item->id }}</td>
                        <td class="py-2 px-4">{{ $item->detalleProducto->producto->nombre ?? '-' }}</td>
                        <td class="py-2 px-4 {{ $item->stock_actual == 0 ? 'text-red-600 font-bold' : '' }}">{{ $item->stock_actual }}</td>
                        <td class="py-2 px-4">{{ $item->cantidad_en_produccion }}</td>
                        <td class="py-2 px-4">{{ $item->fecha_actualizacion }}</td>
                        <td class="py-2 px-4">
                            <a href="{{ route('admin.inventario.edit', $item->id) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Editar</a>
                            <form action="{{ route('admin.inventario.destroy', $item->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 ml-1" onclick="return confirm('¿Eliminar este registro?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">No hay registros de inventario.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
