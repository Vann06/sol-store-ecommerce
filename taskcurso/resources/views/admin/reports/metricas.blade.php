@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Métricas del reporte</h1>

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

    <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium text-gray-800 dark:text-gray-100">Pedidos por mes</h3>
            <div class="space-x-2">
                @if(!empty($backUrl))
                <a href="{{ $backUrl }}" class="inline-block px-3 py-1 bg-gray-200 dark:bg-gray-700 text-sm rounded mr-2">&larr; Volver</a>
                @endif
                <form action="{{ route('admin.reports.pdf') }}" method="POST" target="_blank" class="inline-block">
                    @csrf
                    <input type="hidden" name="tipo" value="ventas">
                    <input type="hidden" name="fecha_inicio" value="{{ now()->startOfYear()->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ now()->endOfYear()->format('Y-m-d') }}">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Exportar PDF</button>
                </form>
                <a href="{{ route('admin.reports.chart.image', ['fecha_inicio' => now()->startOfYear()->format('Y-m-d'), 'fecha_fin' => now()->endOfYear()->format('Y-m-d')]) }}" target="_blank" class="inline-block px-4 py-2 bg-green-600 text-white rounded">Descargar Imagen</a>
            </div>
        </div>

        <div class="chart-area" style="height:360px; display:flex; align-items:center; justify-content:center;">
            <div style="width:100%; max-width:900px;">
                <div class="chart-inline">
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

        <table class="w-full mt-4 table-auto text-sm">
            <thead>
                <tr>
                    <th class="text-left">Mes</th>
                    <th class="text-left">Pedidos</th>
                </tr>
            </thead>
            <tbody>
                @foreach(range(1,12) as $m)
                    <tr>
                        <td class="py-1 text-gray-700 dark:text-gray-300">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                        <td class="py-1 font-semibold text-gray-900 dark:text-white">{{ $pedidosPorMes->get($m, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
/* Ensure inline SVGs from server have fixed height and do not overflow */
.chart-inline svg { 
    width: 100% !important; 
    height: 360px !important; 
    display: block; 
    background: #ffffff !important;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dark .chart-inline svg {
    background: #374151 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.chart-area { 
    overflow: visible;
    background: transparent;
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
}
</style>
@endpush
@section('javascript')
    @stack('scripts')
@endsection
@endsection
