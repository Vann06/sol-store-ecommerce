@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Gráficos de Reportes</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Gráficos</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Gráfico Principal -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ $chart->options['chart_title'] ?? 'Gráfico de Ventas' }}</h3>
            <div class="text-sm text-gray-500">
                Tipo: {{ ucfirst($chart->options['chart_type'] ?? 'bar') }} | 
                Período: {{ ucfirst($chart->options['group_by_period'] ?? 'month') }}
            </div>
        </div>
        <div class="chart-container" style="height: 400px; position: relative; overflow: hidden;">
            @if(isset($chart))
                {!! $chart->renderHtml() !!}
            @else
                <p class="text-gray-500 text-center py-20">No se pudo cargar el gráfico</p>
            @endif
        </div>
    </div>

    <!-- Información adicional -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Información del Gráfico</h4>
            <ul class="space-y-2 text-sm">
                <li><strong>Fuente de datos:</strong> {{ class_basename($chart->options['model'] ?? 'N/A') }}</li>
                <li><strong>Campo agrupado:</strong> {{ $chart->options['group_by_field'] ?? 'N/A' }}</li>
                <li><strong>Período:</strong> {{ $chart->options['group_by_period'] ?? 'N/A' }}</li>
                <li><strong>Tipo de reporte:</strong> {{ $chart->options['report_type'] ?? 'N/A' }}</li>
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Acciones Disponibles</h4>
            <div class="space-y-2">
                <form method="POST" action="{{ route('admin.reports.pdf') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="tipo" value="ventas">
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">
                        📄 Exportar como PDF
                    </button>
                </form>
                <a href="{{ route('admin.reports.metricas.view') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center text-sm">
                    📈 Ver Métricas
                </a>
                <a href="{{ route('admin.reports.navegacion') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-center text-sm">
                    🧭 Navegar Reportes
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Configuraciones</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Color del gráfico:</span>
                    <span class="px-2 py-1 rounded" style="background-color: rgb({{ $chart->options['chart_color'] ?? '239, 68, 68' }}); color: white;">RGB</span>
                </div>
                <div class="flex justify-between">
                    <span>Responsivo:</span>
                    <span class="text-green-600">✓ Sí</span>
                </div>
                <div class="flex justify-between">
                    <span>Animaciones:</span>
                    <span class="text-green-600">✓ Habilitadas</span>
                </div>
                <div class="flex justify-between">
                    <span>Interactivo:</span>
                    <span class="text-green-600">✓ Sí</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@if(isset($chart))
    {!! $chart->renderChartJsLibrary() !!}
    {!! $chart->renderJs() !!}
@endif
@endsection

@push('styles')
<style>
/* Controlar altura de gráficas y prevenir overflow */
.chart-container {
    max-height: 400px !important;
    min-height: 300px !important;
    height: 400px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-sizing: border-box;
}

.chart-container canvas {
    width: 100% !important;
    height: 100% !important;
    max-height: 380px !important;
    object-fit: contain;
}

/* Asegurar que las gráficas no interfieran con otros elementos */
.bg-white.dark\:bg-gray-800.p-6.rounded-lg.shadow.mb-8 {
    margin-bottom: 2rem !important;
    z-index: 1;
}

/* Responsividad mejorada */
@media (max-width: 768px) {
    .chart-container {
        height: 300px !important;
        max-height: 300px !important;
    }
    .chart-container canvas {
        max-height: 280px !important;
    }
}

@media (max-width: 640px) {
    .chart-container {
        height: 250px !important;
        max-height: 250px !important;
    }
    .chart-container canvas {
        max-height: 230px !important;
    }
}
</style>
@endpush

@section('inline-scripts')
<script>
// Ensure Chart.js canvases fill their container height and trigger a redraw.
function ensureChartsFitInContainer() {
    document.querySelectorAll('.chart-container').forEach(function(container){
        const canvas = container.querySelector('canvas');
        if (!canvas) return;
        const style = getComputedStyle(container);
        const h = container.clientHeight - parseFloat(style.paddingTop || 0) - parseFloat(style.paddingBottom || 0);
        canvas.style.width = '100%';
        canvas.style.height = h + 'px';
    });
    // Let Chart.js respond to resize event
    window.dispatchEvent(new Event('resize'));
}

// Run after chart library had a chance to render
document.addEventListener('DOMContentLoaded', function(){ setTimeout(ensureChartsFitInContainer, 200); });
window.addEventListener('resize', function(){ setTimeout(ensureChartsFitInContainer, 80); });
</script>
@endsection
