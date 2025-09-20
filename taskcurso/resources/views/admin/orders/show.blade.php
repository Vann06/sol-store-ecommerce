@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold dark:text-white">Pedido #{{ $pedido->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded">{{ session('info') }}</div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow space-y-4">
            <h2 class="font-semibold mb-3 dark:text-white">Resumen</h2>
            <ul class="text-sm space-y-1 dark:text-gray-200">
                <li><span class="font-medium">Fecha:</span> {{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i') : '-' }}</li>
                <li><span class="font-medium">Estado:</span> {{ $pedido->estado }}</li>
                <li><span class="font-medium">Total items:</span> {{ $pedido->detalles->count() }}</li>
            </ul>

            <form action="{{ route('admin.orders.update', $pedido->id) }}" method="POST" class="mt-4 space-y-2">
                @csrf
                @method('PUT')
                <label class="block text-sm dark:text-gray-300">Cambiar estado</label>
                <select name="estado" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-white" @if(empty($nextAllowed)) disabled @endif>
                    @if(empty($nextAllowed))
                        <option>No hay transiciones disponibles</option>
                    @else
                        @foreach($nextAllowed as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    @endif
                </select>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm" @if(empty($nextAllowed)) disabled @endif>Actualizar</button>
                @if(empty($nextAllowed))
                    <p class="text-xs text-gray-500 dark:text-gray-400">Estado final alcanzado.</p>
                @endif
            </form>

            <div class="pt-2 border-t dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-white text-sm uppercase">Cliente</h3>
                <ul class="text-sm space-y-1 dark:text-gray-200">
                    <li><span class="font-medium">Nombre:</span> {{ ($pedido->user->first_name ?? '') . ' ' . ($pedido->user->last_name ?? '') }}</li>
                    <li><span class="font-medium">Email:</span> {{ $pedido->user->email ?? '-' }}</li>
                </ul>
            </div>
            <div class="pt-2 border-t dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-white text-sm uppercase">Dirección envío</h3>
                <p class="text-sm dark:text-gray-200">{{ $pedido->envio->direccion->direccion ?? 'No registrada' }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h2 class="font-semibold mb-3 dark:text-white">Ítems</h2>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="px-2 py-1">Img</th>
                        <th class="px-2 py-1">Producto</th>
                        <th class="px-2 py-1">Cant.</th>
                        <th class="px-2 py-1">P.Unit</th>
                        <th class="px-2 py-1">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($pedido->detalles as $d)
                        @php 
                            $nombre = $d->detalleProducto->producto->nombre ?? 'N/A';
                            $img = $d->detalleProducto->producto->imagen ?? null;
                            $subtotal = ($d->cantidad ?? 0) * ($d->precio_unitario ?? 0);
                            $total += $subtotal;
                        @endphp
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-2 py-1">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $nombre }}" class="h-10 w-10 object-cover rounded border" />
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-1">{{ $nombre }}</td>
                            <td class="px-2 py-1">{{ $d->cantidad }}</td>
                            <td class="px-2 py-1">Q {{ number_format($d->precio_unitario,2) }}</td>
                            <td class="px-2 py-1">Q {{ number_format($subtotal,2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="text-right px-2 py-2 font-semibold">Total</td>
                        <td class="px-2 py-2 font-semibold">Q {{ number_format($total,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
