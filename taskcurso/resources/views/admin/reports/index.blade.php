@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">📊 Reportes de Ventas</h1>
            <nav class="flex text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <span class="text-gray-400">Admin</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="mx-2">›</span>
                            <span class="font-medium text-red-600">Reportes</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.navegacion') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                Navegación
            </a>
            <a href="{{ route('admin.reports.metricas.view') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Métricas
            </a>
            <a href="{{ route('admin.reports.graficos') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Gráficos
            </a>
            <a href="{{ route('admin.reports.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Crear Reporte
            </a>
            <form method="POST" action="{{ route('admin.reports.pdf') }}" class="inline">
                @csrf
                <input type="hidden" name="tipo" value="general">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
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

    <!-- Gráficos - TAMAÑO CONSISTENTE PARA TODOS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Gráfico de Ventas por Mes -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow cursor-pointer hover:shadow-lg transition-shadow" onclick="navigateToChart('ventas')">
            <h3 class="text-lg font-semibold mb-4">{{ $ventasPorMes->options['chart_title'] ?? 'Ventas por Mes' }}</h3>
            <div class="chart-wrapper">
                @if(isset($ventasPorMes))
                    {!! $ventasPorMes->renderHtml() !!}
                @else
                    <p class="text-gray-500">No hay datos de ventas disponibles</p>
                @endif
            </div>
        </div>

        <!-- Gráfico de Top Productos -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow cursor-pointer hover:shadow-lg transition-shadow" onclick="navigateToChart('productos')">
            <h3 class="text-lg font-semibold mb-4">{{ $topProductos->options['chart_title'] ?? 'Top Productos' }}</h3>
            <div class="chart-wrapper">
                @if(isset($topProductos))
                    {!! $topProductos->renderHtml() !!}
                @else
                    <p class="text-gray-500">No hay datos de productos disponibles</p>
                @endif
            </div>
        </div>

        <!-- Gráfico de Pedidos por Estado - REDUCIDO -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow cursor-pointer hover:shadow-lg transition-shadow" onclick="navigateToChart('inventario')">
            <h3 class="text-lg font-semibold mb-4">{{ $pedidosPorEstado->options['chart_title'] ?? 'Estado de Pedidos' }}</h3>
            <div class="chart-wrapper chart-wrapper-pie">
                @if(isset($pedidosPorEstado))
                    {!! $pedidosPorEstado->renderHtml() !!}
                @else
                    <p class="text-gray-500">No hay datos de pedidos disponibles</p>
                @endif
            </div>
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
                            <td class="py-2 px-4">{{ optional($pedido)->id }}</td>
                            @php $u = optional(optional($pedido)->usuario); $estado = optional($pedido)->estado; @endphp
                            <td class="py-2 px-4">{{ $u->first_name ?? 'N/A' }} {{ $u->last_name ?? '' }}</td>
                            <td class="py-2 px-4">
                                @php
                                    $estadoClasses = [
                                        'Entregado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'Enviando' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'Pintando' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'Pendiente' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        'Cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'Procesando' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        'Enviado' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                    ];
                                    $badgeClass = $estadoClasses[$estado] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $badgeClass }}">
                                    {{ $estado ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2 px-4">{{ optional(optional($pedido)->created_at)->format('d/m/Y') ?? '' }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('admin.orders.show', $pedido->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Ver detalles</a>
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

@push('styles')
<style>
/* En index.blade.php - Reemplazar la sección de estilos */
.chart-wrapper {
    position: relative;
    width: 100%;
    height: 280px;
    max-height: 280px;
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.chart-wrapper canvas,
.chart-wrapper svg {
    width: 100% !important;
    height: 100% !important;
    max-width: 100%;
    max-height: 280px !important;
    object-fit: contain;
}

/* PIE CHART - MÁS PEQUEÑO Y CENTRADO */
.chart-wrapper-pie {
    height: 280px !important;
}

.chart-wrapper-pie canvas,
.chart-wrapper-pie svg {
    max-width: 200px !important;
    max-height: 200px !important;
    width: 200px !important;
    height: 200px !important;
    margin: 0 auto;
}
</style>
@endpush

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

<script>
function navigateToChart(tipo) {
    window.location.href = "{{ route('admin.reports.graficos') }}?tipo=" + tipo;
}
</script>
@endsection