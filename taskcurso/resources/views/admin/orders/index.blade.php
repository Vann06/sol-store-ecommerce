@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold dark:text-white">Pedidos</h1>
</div>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
@endif

<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-sm mb-1 dark:text-gray-300">Estado</label>
        <select name="estado" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
            <option value="">-- Todos --</option>
            @foreach($allowed as $st)
                <option value="{{ $st }}" @selected(($filtros['estado'] ?? '') === $st)>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1 dark:text-gray-300">Buscar (ID)</label>
        <input type="text" name="search" value="{{ $filtros['search'] ?? '' }}" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-white" />
    </div>
    <div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Filtrar</button>
    </div>
</form>

<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="text-left border-b dark:border-gray-700">
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Cliente</th>
                <th class="px-3 py-2">Dirección</th>
                <th class="px-3 py-2">Fecha</th>
                <th class="px-3 py-2">Estado</th>
                <th class="px-3 py-2">Items</th>
                <th class="px-3 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $p)
                <tr class="border-b dark:border-gray-700">
                    <td class="px-3 py-2">{{ $p->id }}</td>
                    <td class="px-3 py-2">{{ ($p->user->first_name ?? '') . ' ' . ($p->user->last_name ?? '') }}</td>
                    <td class="px-3 py-2 max-w-[180px] truncate" title="{{ $p->envio->direccion->direccion ?? '-' }}">{{ $p->envio->direccion->direccion ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $p->fecha_pedido ? $p->fecha_pedido->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-3 py-2">{{ $p->estado }}</td>
                    <td class="px-3 py-2">{{ $p->detalles->count() }}</td>
                    <td class="px-3 py-2">
                        <a href="{{ route('admin.orders.show', $p->id) }}" class="text-blue-600 hover:underline">Ver</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">Sin pedidos</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pedidos->links() }}</div>
@endsection
