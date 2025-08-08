@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Generar Reporte Personalizado</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Crear</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver a Reportes
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.reports.pdf') }}" class="space-y-6">
        @csrf
        
        <!-- Selección de tipo de reporte -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">📄 Tipo de Reporte</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="relative">
                    <input type="radio" name="tipo" value="general" class="sr-only peer" checked>
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Reporte General</h4>
                                <p class="text-sm text-gray-500">Resumen completo del negocio</p>
                            </div>
                        </div>
                    </div>
                </label>
                
                <label class="relative">
                    <input type="radio" name="tipo" value="ventas" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Reporte de Ventas</h4>
                                <p class="text-sm text-gray-500">Transacciones e ingresos</p>
                            </div>
                        </div>
                    </div>
                </label>
                
                <label class="relative">
                    <input type="radio" name="tipo" value="productos" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Reporte de Productos</h4>
                                <p class="text-sm text-gray-500">Inventario y catálogo</p>
                            </div>
                        </div>
                    </div>
                </label>
                
                <label class="relative">
                    <input type="radio" name="tipo" value="pedidos" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Reporte de Pedidos</h4>
                                <p class="text-sm text-gray-500">Estados y seguimiento</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Rango de fechas -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">📅 Rango de Fechas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ now()->subMonth()->format('Y-m-d') }}" 
                           class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                    <p class="text-xs text-gray-500 mt-1">Fecha desde la cual generar el reporte</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ now()->format('Y-m-d') }}" 
                           class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                    <p class="text-xs text-gray-500 mt-1">Fecha hasta la cual generar el reporte</p>
                </div>
            </div>
            
            <!-- Filtros rápidos -->
            <div class="mt-4">
                <p class="text-sm font-medium mb-2">Filtros Rápidos:</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setDateRange('today')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Hoy
                    </button>
                    <button type="button" onclick="setDateRange('week')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Esta semana
                    </button>
                    <button type="button" onclick="setDateRange('month')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este mes
                    </button>
                    <button type="button" onclick="setDateRange('quarter')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este trimestre
                    </button>
                    <button type="button" onclick="setDateRange('year')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este año
                    </button>
                </div>
            </div>
        </div>

        <!-- Opciones adicionales -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">⚙️ Opciones Adicionales</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="incluir_graficos" id="incluir_graficos" value="1" class="mr-3" checked>
                    <label for="incluir_graficos" class="text-sm font-medium">Incluir gráficos en el PDF</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="incluir_detalles" id="incluir_detalles" value="1" class="mr-3" checked>
                    <label for="incluir_detalles" class="text-sm font-medium">Incluir detalles completos</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="incluir_resumen" id="incluir_resumen" value="1" class="mr-3" checked>
                    <label for="incluir_resumen" class="text-sm font-medium">Incluir resumen ejecutivo</label>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <p>📄 El reporte se generará en formato PDF</p>
                    <p>⏱️ Tiempo estimado: 10-30 segundos</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="previewReport()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        👁️ Vista Previa
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        📄 Generar Reporte PDF
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('javascript')
<script>
function setDateRange(period) {
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

function previewReport() {
    const tipoReporte = document.querySelector('input[name="tipo"]:checked').value;
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
    
    const url = "{{ route('admin.reports.index') }}?preview=1&tipo=" + tipoReporte + 
                "&fecha_inicio=" + fechaInicio + "&fecha_fin=" + fechaFin;
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
    const fechaFin = document.querySelector('input[name="fecha_fin"]');
    
    function validateDates() {
        if (fechaInicio.value && fechaFin.value) {
            if (new Date(fechaInicio.value) > new Date(fechaFin.value)) {
                alert('⚠️ La fecha de inicio no puede ser mayor que la fecha de fin');
                fechaInicio.focus();
                return false;
            }
        }
        return true;
    }
    
    fechaInicio.addEventListener('change', validateDates);
    fechaFin.addEventListener('change', validateDates);
    
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
