@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Reportes Filtrados</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Filtros</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(!empty($backUrl))
            <a href="{{ $backUrl }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">← Volver</a>
            @else
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">← Volver a Reportes</a>
            @endif
        </div>
    </div>

    <!-- Información del filtro aplicado -->
    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-semibold text-blue-800 dark:text-blue-200">Filtro Aplicado</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Mostrando datos desde: <strong>{{ $fechaInicio->format('d/m/Y') }}</strong> 
                    hasta: <strong>{{ $fechaFin->format('d/m/Y') }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Nuevo filtro -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">Aplicar Nuevo Filtro</h3>
    <form method="POST" action="{{ route('admin.reports.filtros') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Filtros Rápidos</label>
                <select onchange="setQuickFilter(this.value)" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Seleccionar período</option>
                    <option value="today">📅 Hoy</option>
                    <option value="week">🗺 Esta semana</option>
                    <option value="month">📆 Este mes</option>
                    <option value="quarter">📊 Este trimestre</option>
                    <option value="year">📈 Este año</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Gráfico filtrado -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ $chartFiltrado->options['chart_title'] ?? 'Datos Filtrados' }}</h3>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('admin.reports.pdf') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tipo" value="ventas">
                    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                    <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 text-sm">
                        📄 PDF
                    </button>
                </form>
                <button onclick="exportChart()" class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 text-sm">
                    🗃️ Imagen
                </button>
            </div>
        </div>
        <div class="chart-container" style="height: 420px;">
            @if(isset($chartFiltrado))
                {!! $chartFiltrado->renderHtml() !!}
            @else
                <div class="flex items-center justify-center" style="min-height:320px;">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">No se encontraron datos para el período seleccionado</p>
                        <p class="text-sm text-gray-400 mt-2">Intente con un rango de fechas diferente</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas del período filtrado -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Resumen del Período</h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Días analizados:</span>
                    <span class="font-semibold">{{ $fechaInicio->diffInDays($fechaFin) + 1 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Fecha inicio:</span>
                    <span class="font-semibold">{{ $fechaInicio->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Fecha fin:</span>
                    <span class="font-semibold">{{ $fechaFin->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Acciones Rápidas</h4>
                <div class="space-y-2">
                    <a href="{{ route('admin.reports.metricas.view') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center text-sm">
                        📈 Ver Métricas Detalladas
                    </a>
                    <a href="{{ route('admin.reports.graficos') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-center text-sm">
                        📉 Ver Más Gráficos
                    </a>
                    <button onclick="shareReport()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
                        👤 Compartir Reporte
                    </button>
                </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-semibold mb-4">Filtros Guardados</h4>
            <div class="space-y-2 text-sm">
                <button onclick="applyFilter('week')" class="block w-full text-left p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    🗺 Últimos 7 días
                </button>
                <button onclick="applyFilter('month')" class="block w-full text-left p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    📆 Últimos 30 días
                </button>
                <button onclick="applyFilter('quarter')" class="block w-full text-left p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    📊 Últimos 90 días
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chart-container canvas { width:100% !important; height:100% !important; }
</style>
@endpush

@section('inline-scripts')
<script>
function ensureChartsFitInContainer() {
    document.querySelectorAll('.chart-container').forEach(function(container){
        const canvas = container.querySelector('canvas');
        if (!canvas) return;
        const style = getComputedStyle(container);
        const h = container.clientHeight - parseFloat(style.paddingTop || 0) - parseFloat(style.paddingBottom || 0);
        canvas.style.width = '100%';
        canvas.style.height = h + 'px';
    });
    window.dispatchEvent(new Event('resize'));
}

document.addEventListener('DOMContentLoaded', function(){ setTimeout(ensureChartsFitInContainer, 200); });
window.addEventListener('resize', function(){ setTimeout(ensureChartsFitInContainer, 80); });
</script>
@endsection

@section('javascript')
@if(isset($chartFiltrado))
    {!! $chartFiltrado->renderChartJsLibrary() !!}
    {!! $chartFiltrado->renderJs() !!}
@endif

<script>
function setQuickFilter(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'week':
            startDate = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            endDate = today;
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = today;
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = today;
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = today;
            break;
        default:
            return;
    }
    
    document.querySelector('input[name="fecha_inicio"]').value = startDate.toISOString().split('T')[0];
    document.querySelector('input[name="fecha_fin"]').value = endDate.toISOString().split('T')[0];
}

function exportChart() {
    // Get the current date range for the image URL
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
    
    // Create the image download URL
    const imageUrl = '{{ route("admin.reports.chart.image") }}?fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
    
    // Create a temporary link element to trigger download
    const link = document.createElement('a');
    link.href = imageUrl;
    link.download = 'grafica_filtrada_' + fechaInicio + '_' + fechaFin + '.png';
    link.target = '_blank';
    
    // Trigger download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    setTimeout(() => {
        alert('📥 Descarga iniciada. Si el archivo no se descarga automáticamente, verifique la configuración del navegador.');
    }, 500);
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'Reporte de Ventas Filtrado',
            text: 'Reporte del período {{ $fechaInicio->format("d/m/Y") }} al {{ $fechaFin->format("d/m/Y") }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('📋 URL copiada al portapapeles');
    }
}

function applyFilter(type) {
    setQuickFilter(type);
    document.querySelector('form').submit();
}
</script>
@endsection
