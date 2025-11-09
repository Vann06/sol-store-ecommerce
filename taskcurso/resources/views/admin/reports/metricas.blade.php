@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Métricas del reporte</h1>
    @if(!empty($backUrl))
    <a href="{{ $backUrl }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Volver
    </a>
    @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
            <h2 class="text-sm text-gray-500 dark:text-gray-400">Total ventas</h2>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalVentas, 2) }}</p>
        </div>
        <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
            <h2 class="text-sm text-gray-500 dark:text-gray-400">Meses (pedidos)</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">Mostrar pedidos por mes (año actual)</p>
        </div>
        <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
            <h2 class="text-sm text-gray-500 dark:text-gray-400">Top productos</h2>
            <ol class="list-decimal ml-5 text-gray-700 dark:text-gray-300">
                @foreach($topProductos as $prod)
                    <li>{{ $prod->nombre ?? $prod->titulo ?? 'Producto #' . $prod->id }}</li>
                @endforeach
            </ol>
        </div>
    </div>

    <!-- Título y Volver -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Gráfico de Pedidos por Mes</h2>

    </div>

    <!-- Gráfico Separado -->
    <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium text-gray-800 dark:text-gray-100">Gráfico de Pedidos por Mes</h3>
            <div class="flex space-x-2">
                <form action="{{ route('admin.reports.pdf') }}" method="POST" target="_blank" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="ventas">
                    <input type="hidden" name="fecha_inicio" value="{{ now()->startOfYear()->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ now()->endOfYear()->format('Y-m-d') }}">
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
                    </button>
                </form>
                <form action="{{ route('admin.reports.excel') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="ventas">
                    <input type="hidden" name="fecha_inicio" value="{{ now()->startOfYear()->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ now()->endOfYear()->format('Y-m-d') }}">
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </button>
                </form>
            </div>
        </div>

        <div class="chart-area" style="height:auto; max-height:inherit; min-height:300px; display:flex; align-items:center; justify-content:center; border: 2px solid #e5e7eb; border-radius: 8px; padding: 16px; background: transparent; overflow:hidden;">
            <div style="width:100%; height:100%; max-width:100%;">
                <div class="chart-inline" style="width:100%; height:100%;">
                    @if(!empty($chart) && method_exists($chart, 'renderHtml'))
                        {{-- Render the LaravelChart object (when package is installed) --}}
                        {!! $chart->renderHtml() !!}
                        @push('scripts')
                            {!! $chart->renderChartJsLibrary() !!}
                            {!! $chart->renderJs() !!}
                        @endpush
                    @elseif(!empty($chartSvg))
                        @php
                            // Remove hard-coded white background rect so CSS can control it for dark mode
                            $inlineSvg = str_replace("<rect width='100%' height='100%' fill='#ffffff'/>", '', $chartSvg);
                            // Ensure text adapts to theme using CSS classes instead of inline fills
                            $inlineSvg = str_replace(["fill='#0f172a'", "fill='#374151'", "fill='#111827'", "fill='#ffffff'"], 'class="chart-text"', $inlineSvg);
                            // Replace grid lines with theme-aware class
                            $inlineSvg = str_replace(["stroke='#9CA3AF'", "stroke='#6B7280'"], 'class="chart-grid"', $inlineSvg);
                        @endphp
                        {!! $inlineSvg !!}
                    @else
                        <div class="text-gray-500">No hay gráfico disponible</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Datos Separada -->
    <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium text-gray-800 dark:text-gray-100">Datos Detallados por Mes</h3>
            <div class="text-sm text-gray-500">
                Total de pedidos: {{ $pedidosPorMes->sum() }}
            </div>
        </div>

        <table class="w-full table-auto text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-2 font-semibold">Mes</th>
                    <th class="text-left py-2 font-semibold">Pedidos</th>
                    <th class="text-left py-2 font-semibold">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @php $total = $pedidosPorMes->sum(); @endphp
                @foreach(range(1,12) as $m)
                    @php $count = $pedidosPorMes->get($m, 0); $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0; @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-2 text-gray-700 dark:text-gray-300">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                        <td class="py-2 font-semibold text-gray-900 dark:text-white">{{ $count }}</td>
                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
/* Consistent chart sizing to match index page */
.chart-inline svg { 
    width: 100% !important; 
    height: 300px !important; 
    max-height: 300px !important;
    min-height: 250px !important;
    display: block; 
    background: #ffffff !important;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 2px solid #e5e7eb;
    padding: 16px;
    box-sizing: border-box;
    object-fit: contain;
}
.chart-inline canvas {
    width: 100% !important; 
    height: 300px !important; 
    max-height: 300px !important;
    min-height: 250px !important;
    object-fit: contain !important;
}
.dark .chart-inline svg {
    background: #374151 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    border-color: #4b5563;
}
.chart-area { 
    overflow: hidden;
    background: #f9fafb;
    border-radius: 8px;
    padding: 16px;
    border: 2px solid #e5e7eb;
    height: 350px !important;
    max-height: 350px !important;
    min-height: 300px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative;
}
.dark .chart-area {
    background: #1f2937;
    border-color: #374151;
}
/* Theme-aware text and grid styles for SVG elements */
.chart-text {
    fill: #0f172a;
}
.dark .chart-text {
    fill: #ffffff;
}
.chart-grid {
    stroke: #9CA3AF;
    opacity: 0.35;
}
.dark .chart-grid {
    stroke: #6B7280;
    opacity: 0.5;
}
/* Better chart container styling */
.chart-inline {
    border-radius: 8px;
    padding: 10px;
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    overflow: hidden !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative;
}
/* Ensure chart doesn't overflow into data table */
.chart-area {
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}
/* Data table styling */
table {
    margin-top: 1rem;
    border-collapse: separate;
    border-spacing: 0;
}
table th, table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.dark table th, .dark table td {
    border-bottom-color: #374151;
}
/* Full screen responsive adjustments */
@media (min-width: 1920px) {
    .chart-area {
        height: 500px !important;
        max-height: 500px !important;
        min-height: 450px !important;
    }
    .chart-inline svg,
    .chart-inline canvas {
        height: 450px !important;
        max-height: 450px !important;
        min-height: 400px !important;
    }
}
@media (min-width: 2560px) {
    .chart-area {
        height: 600px !important;
        max-height: 600px !important;
        min-height: 550px !important;
    }
    .chart-inline svg,
    .chart-inline canvas {
        height: 550px !important;
        max-height: 550px !important;
        min-height: 500px !important;
    }
}
/* Tablet landscape and larger screens */
@media (min-width: 1024px) and (max-width: 1919px) {
    .chart-area {
        height: 400px !important;
        max-height: 400px !important;
        min-height: 350px !important;
    }
    .chart-inline svg,
    .chart-inline canvas {
        height: 350px !important;
        max-height: 350px !important;
        min-height: 300px !important;
    }
}
</style>
@endpush
@section('javascript')
    @stack('scripts')
@endsection
@endsection