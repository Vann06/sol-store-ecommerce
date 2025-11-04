@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Gráficos de Reportes</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Gráficos</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(!empty($backUrl))
            <a href="{{ $backUrl }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">← Volver</a>
            @else
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">← Volver a Reportes</a>
            @endif
        </div>
    </div>

    {{-- <!-- Chart Type Selection -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold mb-4">Tipo de Gráfico</h3>
        <div class="flex flex-wrap gap-2">
            <button onclick="changeChartType('bar')" class="chart-type-btn {{ ($chartType ?? 'bar') === 'bar' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Barras
            </button>
            <button onclick="changeChartType('line')" class="chart-type-btn {{ ($chartType ?? 'bar') === 'line' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                </svg>
                Líneas
            </button>
            <button onclick="changeChartType('pie')" class="chart-type-btn {{ ($chartType ?? 'bar') === 'pie' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Circular
            </button>
            <button onclick="changeChartType('doughnut')" class="chart-type-btn {{ ($chartType ?? 'bar') === 'doughnut' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                </svg>
                Dona
            </button>
        </div>
    </div> --}}

    <!-- Gráfico Principal -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold" id="chart-title">
                @if($tipo === 'ventas')
                    Ventas Mensuales
                @elseif($tipo === 'productos')
                    Productos por Categoría
                @elseif($tipo === 'usuarios')
                    Usuarios Registrados
                @elseif($tipo === 'inventario')
                    Pedidos por Estado
                @else
                    {{ $chart->options['chart_title'] ?? 'Gráfico de Ventas' }}
                @endif
            </h3>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('admin.reports.pdf') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ $tipo ?? 'ventas' }}">
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.reports.excel') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ $tipo ?? 'ventas' }}">
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="chart-container" style="height: auto; position: relative; overflow: hidden; border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px; display: flex; align-items: center; justify-content: center;">
            @if(isset($chart))
                {!! $chart->renderHtml() !!}
            @elseif(in_array($tipo ?? 'ventas', ['ventas', 'usuarios']) && in_array($chartType ?? 'bar', ['pie', 'doughnut']))
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">📊</div>
                    <p class="text-gray-600 text-lg mb-2">Tipo de gráfico no disponible</p>
                    <p class="text-gray-500 text-sm">Los gráficos circulares no están disponibles para datos de {{ $tipo ?? 'ventas' }}</p>
                    <p class="text-gray-400 text-xs mt-2">Selecciona 'Barras' o 'Líneas' para ver los datos</p>
                </div>
            @else
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">⚠️</div>
                    <p class="text-gray-500 text-lg">No se pudo cargar el gráfico</p>
                    <p class="text-gray-400 text-sm">Intenta seleccionar un tipo de gráfico diferente</p>
                </div>
            @endif
        </div>
    </div>

        <!-- Información del Gráfico -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Información del Gráfico</h4>
            <ul class="space-y-2 text-sm">
                <li><strong>Datos mostrados:</strong> 
                    @if($tipo === 'ventas')
                        Información de ventas y transacciones
                    @elseif($tipo === 'productos')
                        Información de productos y categorías
                    @elseif($tipo === 'usuarios')
                        Información de usuarios registrados
                    @elseif($tipo === 'inventario')
                        Información de pedidos y estados
                    @else
                        Información general del sistema
                    @endif
                </li>
                <li><strong>Agrupación:</strong> Por {{ $chart->options['group_by_period'] ?? 'mes' }}</li>
                <li><strong>Período:</strong> Últimos {{ $chart->options['group_by_period'] ?? '12' }} meses</li>
                <li><strong>Actualización:</strong> Tiempo real</li>
                <li><strong>Color del gráfico:</strong>
                    <span class="px-2 py-1 rounded" style="background-color: rgb({{ $chart->options['chart_color'] ?? '239, 68, 68' }}); color: white;">RGB</span>
                </li>
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Acciones Disponibles</h4>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.metricas.view') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center text-sm flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Ver Métricas
                </a>
                <a href="{{ route('admin.reports.navegacion') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-center text-sm flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Navegar Reportes
                </a>
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
    position: relative;
    width: 100% !important;
}

.chart-container canvas {
    width: 100% !important;
    height: 100% !important;
    max-height: 380px !important;
    max-width: 100% !important;
    object-fit: contain;
    border-radius: 4px;
}

.chart-container svg {
    width: 100% !important;
    height: 100% !important;
    max-height: 380px !important;
    max-width: 100% !important;
    object-fit: contain;
}

/* Ensure chart starts from zero */
.chart-container .chartjs-render-monitor {
    min-height: 300px !important;
    max-height: 380px !important;
}

/* Fix chart scaling to start from zero */
.chart-container canvas {
    transform-origin: center center;
}

/* Dark mode support for charts */
.dark .chart-container {
    border-color: #374151 !important;
    background: #1f2937 !important;
}

.dark .chart-container canvas {
    filter: brightness(0.9) contrast(1.1);
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

function changeChartType(type) {
    // Update chart title based on type
    const titles = {
        'line': 'Gráfico de Líneas - Tendencias',
        'bar': 'Gráfico de Barras - Comparación',
        'pie': 'Gráfico Circular - Distribución',
        'doughnut': 'Gráfico de Dona - Distribución'
    };
    
    const titleElement = document.getElementById('chart-title');
    if (titleElement) {
        titleElement.textContent = titles[type] || 'Gráfico de Ventas';
    }
    
    // Update the chart type buttons
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Highlight the selected button
    const selectedBtn = document.querySelector(`[onclick="changeChartType('${type}')"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('bg-gray-200', 'text-gray-700');
        selectedBtn.classList.add('bg-blue-600', 'text-white');
    }
    
    // Show loading message
    const chartContainer = document.querySelector('.chart-container');
    if (chartContainer) {
        chartContainer.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-gray-500">Cargando nuevo gráfico...</div></div>';
    }
    
    // Reload the page with the new chart type
    const url = new URL(window.location);
    url.searchParams.set('chart_type', type);
    
    // Preserve existing parameters
    if (!url.searchParams.has('tipo')) {
        url.searchParams.set('tipo', 'ventas'); // default
    }
    
    console.log('Navigating to:', url.toString());
    
    // Navigate immediately
    window.location.href = url.toString();
}

function exportChart() {
    // Create Excel export functionality
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.reports.excel') }}";
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    const tipoInput = document.createElement('input');
    tipoInput.type = 'hidden';
    tipoInput.name = 'tipo';
    tipoInput.value = 'ventas';
    
    form.appendChild(csrfInput);
    form.appendChild(tipoInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Run after chart library had a chance to render
document.addEventListener('DOMContentLoaded', function(){ setTimeout(ensureChartsFitInContainer, 200); });
window.addEventListener('resize', function(){ setTimeout(ensureChartsFitInContainer, 80); });
</script>
@endsection
