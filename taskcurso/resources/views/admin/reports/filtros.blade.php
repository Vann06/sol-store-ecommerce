@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between    <!-- Métricas Avanzadas de Pedidos -->
    @if(isset($pedidosPagados))
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Métricas Avanzadas del Período
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Pedidos Pagados -->
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium">Pedidos Pagados</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $pedidosPagados }}</p>
                    </div>
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Pedidos Pendientes -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Pedidos Pendientes</p>
                        <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $pedidosPendientes }}</p>
                    </div>
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Tasa de Conversión -->
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Tasa de Conversión</p>
                        <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ number_format($tasaConversion, 1) }}%</p>
                    </div>
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Promedio -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Ticket Promedio</p>
                        <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">Q{{ number_format($promedioVenta, 2) }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pedidos por Estado -->
        @if(isset($pedidosPorEstado) && $pedidosPorEstado->count() > 0)
        <div class="border-t dark:border-gray-700 pt-4">
            <h4 class="text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">📊 Distribución por Estado</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($pedidosPorEstado as $estado => $cantidad)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ ucfirst($estado) }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $cantidad }}</p>
                    <p class="text-xs text-gray-400">{{ number_format(($cantidad / $totalPedidos) * 100, 1) }}%</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Top Clientes del Período -->
    @if(isset($topClientes) && $topClientes->count() > 0)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            Top 5 Clientes del Período
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Posición</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedidos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Gastado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($topClientes as $index => $cliente)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            @if($index == 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">🥇 1°</span>
                            @elseif($index == 1)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">🥈 2°</span>
                            @elseif($index == 2)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800">🥉 3°</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600">{{ $index + 1 }}°</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $cliente['nombre'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $cliente['email'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $cliente['pedidos'] }} pedidos
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">Q{{ number_format($cliente['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Formulario de filtros personalizados -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">🔍 Filtrar Datos</h3>
        <form method="GET" action="{{ route('admin.reports.filtros.view') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Tipo</label>s-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Reportes Filtrados</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Filtros</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.navegacion') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Información del filtro aplicado -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Filtro Aplicado</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Tipo:</span>
                        <span class="text-blue-800 dark:text-blue-200 font-bold ml-1">{{ ucfirst(request('tipo', 'ventas')) }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Fecha Inicio:</span>
                        <span class="text-blue-800 dark:text-blue-200 font-bold ml-1">{{ $fechaInicio->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Fecha Fin:</span>
                        <span class="text-blue-800 dark:text-blue-200 font-bold ml-1">{{ $fechaFin->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Días:</span>
                        <span class="text-blue-800 dark:text-blue-200 font-bold ml-1">{{ $diasDiferencia ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del período -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Promedio por Venta</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Q{{ number_format($promedioVenta ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Nuevo filtro -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">🔍 Aplicar Nuevo Filtro</h3>
    <form method="GET" action="{{ route('admin.reports.filtros.view') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4" id="filterForm">
            <div>
                <label class="block text-sm font-medium mb-2">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="ventas" {{ request('tipo') === 'ventas' ? 'selected' : '' }}>Ventas</option>
                    <option value="productos" {{ request('tipo') === 'productos' ? 'selected' : '' }}>Productos</option>
                    <option value="pedidos" {{ request('tipo') === 'pedidos' ? 'selected' : '' }}>Pedidos</option>
                    <option value="usuarios" {{ request('tipo') === 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                       max="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                       max="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Filtros Rápidos</label>
                <select onchange="setQuickFilter(this.value)" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Seleccionar período</option>
                    <option value="today">📅 Hoy</option>
                    <option value="week">🗓 Últimos 7 días</option>
                    <option value="twoweeks">🗓 Últimos 15 días</option>
                    <option value="month">📆 Últimos 30 días</option>
                    <option value="twomonths">📆 Últimos 60 días</option>
                    <option value="quarter">📊 Últimos 90 días</option>
                    <option value="sixmonths">📈 Últimos 6 meses</option>
                    <option value="year">📈 Último año</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtrar
                </button>
            </div>
        </form>
        <p class="text-xs text-gray-500 mt-3">💡 El rango máximo permitido es de 365 días (1 año)</p>
    </div>

    <!-- Gráfico filtrado -->
    @if(isset($chartFiltrado) && $chartFiltrado)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">📊 Gráfico de Datos Filtrados</h3>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('admin.reports.pdf') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ request('tipo', 'ventas') }}">
                    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.reports.excel') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ request('tipo', 'ventas') }}">
                    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="chart-container">
            {!! $chartFiltrado->renderHtml() !!}
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="text-center py-20">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="text-gray-500">No se encontraron datos para el período seleccionado</p>
            <p class="text-sm text-gray-400 mt-2">Intente con un rango de fechas diferente</p>
        </div>
    </div>
    @endif

    <!-- Tabla de datos filtrados -->
    @if(isset($ventas) && $ventas->count() > 0)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">📋 Datos Filtrados (Ventas)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Monto</th>
                        <th class="px-4 py-2 text-left">Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas->take(50) as $venta)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">{{ $venta->id }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">Q{{ number_format($venta->monto_total, 2) }}</td>
                        <td class="px-4 py-2">{{ $venta->id_pedido ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($ventas->count() > 50)
        <p class="text-sm text-gray-500 mt-4 text-center">
            Mostrando 50 de {{ $ventas->count() }} registros. Exporta a Excel para ver todos.
        </p>
        @endif
    </div>
    @endif

    @if(isset($pedidos) && $pedidos->count() > 0)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold mb-4">📋 Datos Filtrados (Pedidos)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Usuario</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos->take(50) as $pedido)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">{{ $pedido->id }}</td>
                        <td class="px-4 py-2">
                            @if($pedido->usuario)
                                {{ $pedido->usuario->first_name }} {{ $pedido->usuario->last_name }}
                            @else
                                Usuario #{{ $pedido->id_usuario }}
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $pedido->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($pedido->estado ?? 'Pendiente') }}
                            </span>
                        </td>
                        <td class="px-4 py-2">Q{{ number_format($pedido->getTotal(), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pedidos->count() > 50)
        <p class="text-sm text-gray-500 mt-4 text-center">
            Mostrando 50 de {{ $pedidos->count() }} registros. Exporta a Excel para ver todos.
        </p>
        @endif>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.chart-container {
    height: 400px;
    max-height: 400px;
    min-height: 350px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.chart-container canvas,
.chart-container svg {
    max-width: 100%;
    max-height: 100%;
    width: 100%;
    height: auto;
    object-fit: contain;
}
.dark .chart-container canvas {
    filter: brightness(0.9) contrast(1.1);
}
</style>
@endpush

@section('javascript')
@if(isset($chartFiltrado) && $chartFiltrado)
    {!! $chartFiltrado->renderChartJsLibrary() !!}
    {!! $chartFiltrado->renderJs() !!}
@endif

<script>
document.getElementById('filterForm').addEventListener('submit', function(e) {
    const inicio = new Date(document.querySelector('input[name="fecha_inicio"]').value);
    const fin = new Date(document.querySelector('input[name="fecha_fin"]').value);
    
    if (inicio > fin) {
        e.preventDefault();
        alert('⚠️ La fecha de inicio no puede ser mayor que la fecha de fin');
        return false;
    }
    
    const diasDif = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
    if (diasDif > 365) {
        e.preventDefault();
        alert('⚠️ El rango máximo permitido es de 365 días (1 año)');
        return false;
    }
});
function setQuickFilter(period) {
    const today = new Date();
    let startDate, endDate = today;
    
    const daysMap = {
        'today': 0,
        'week': 7,
        'twoweeks': 15,
        'month': 30,
        'twomonths': 60,
        'quarter': 90,
        'sixmonths': 180,
        'year': 365
    };
    
    if (period && daysMap[period] !== undefined) {
        startDate = new Date(today.getTime() - daysMap[period] * 24 * 60 * 60 * 1000);
        
        document.querySelector('input[name="fecha_inicio"]').value = startDate.toISOString().split('T')[0];
        document.querySelector('input[name="fecha_fin"]').value = endDate.toISOString().split('T')[0];
    }
}
</script>
@endsection