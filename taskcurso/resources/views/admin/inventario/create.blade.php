@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8">
    <h1 class="text-2xl font-semibold text-red-700 mb-6">Añadir Inventario</h1>
    <form method="POST" action="{{ route('admin.inventario.store') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block mb-1 font-medium">Detalle Producto</label>
            <select name="id_detalle_producto" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600" required>
                <option value="">Seleccione...</option>
                @foreach($detalles as $detalle)
                    <option value="{{ $detalle->id }}">{{ $detalle->producto->nombre ?? '-' }} (ID: {{ $detalle->id }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1 font-medium">Stock Actual</label>
            <input type="number" name="stock_actual" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600" min="0" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Cantidad en Producción</label>
            <input type="number" name="cantidad_en_produccion" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600" min="0">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">Guardar</button>
            <a href="{{ route('admin.inventario.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
