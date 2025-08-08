@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Reportes de Ventas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.create') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">
                + Generar Reporte
            </a>
            <form method="POST" action="{{ route('admin.reports.pdf') }}" class="inline">
                @csrf
                <input type="hidden" name="tipo" value="general">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    📄 Exportar PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ventas</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Q{{ number_format($totalVentas ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pedidos</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalPedidos ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Productos</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalProductos ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usuarios</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalUsuarios ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de fecha -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">Filtros de Reportes</h3>
        <form method="POST" action="{{ route('admin.reports.filtros') }}" class="flex items-end space-x-4">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">Mes Específico</label>
                <select name="mes" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Seleccionar mes</option>
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico de Ventas por Mes -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">{{ $ventasPorMes->options['chart_title'] ?? 'Ventas por Mes' }}</h3>
            <div class="chart-container">
                @if(isset($ventasPorMes))
                    {!! $ventasPorMes->renderHtml() !!}
                @else
                    <p class="text-gray-500">No hay datos de ventas disponibles</p>
                @endif
            </div>
        </div>

        <!-- Gráfico de Top Productos -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">{{ $topProductos->options['chart_title'] ?? 'Top Productos' }}</h3>
            <div class="chart-container">
                @if(isset($topProductos))
                    {!! $topProductos->renderHtml() !!}
                @else
                    <p class="text-gray-500">No hay datos de productos disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráfico de Pedidos por Estado -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">{{ $pedidosPorEstado->options['chart_title'] ?? 'Pedidos por Estado' }}</h3>
        <div class="chart-container">
            @if(isset($pedidosPorEstado))
                {!! $pedidosPorEstado->renderHtml() !!}
            @else
                <p class="text-gray-500">No hay datos de pedidos disponibles</p>
            @endif
        </div>
    </div>

    <!-- Pedidos Recientes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold">Pedidos Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4">#</th>
                        <th class="py-2 px-4">Usuario</th>
                        <th class="py-2 px-4">Estado</th>
                        <th class="py-2 px-4">Fecha</th>
                        <th class="py-2 px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosRecientes ?? [] as $pedido)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 px-4">{{ $pedido->id }}</td>
                            <td class="py-2 px-4">{{ $pedido->usuario->first_name ?? 'N/A' }} {{ $pedido->usuario->last_name ?? '' }}</td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $pedido->estado === 'Entregado' ? 'bg-green-100 text-green-800' : ($pedido->estado === 'Enviando' ? 'bg-blue-100 text-blue-800' : ($pedido->estado === 'Pintando' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $pedido->estado }}
                                </span>
                            </td>
                            <td class="py-2 px-4">{{ $pedido->created_at->format('d/m/Y') }}</td>
                            <td class="py-2 px-4">
                                <a href="#" class="text-blue-600 hover:text-blue-800">Ver detalles</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No hay pedidos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@if(isset($ventasPorMes))
    {!! $ventasPorMes->renderChartJsLibrary() !!}
    {!! $ventasPorMes->renderJs() !!}
@endif
@if(isset($topProductos))
    {!! $topProductos->renderJs() !!}
@endif
@if(isset($pedidosPorEstado))
    {!! $pedidosPorEstado->renderJs() !!}
@endif
@endsection
