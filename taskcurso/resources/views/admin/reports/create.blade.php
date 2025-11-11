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

    <form method="POST" action="{{ route('admin.reports.pdf') }}" class="space-y-6" id="reportForm">
        @csrf

        <!-- Selección de tipo de reporte -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">📄 Tipo de Reporte</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="relative">
                    <input type="radio" name="tipo" value="ventas" class="sr-only peer" checked>
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 rounded-lg cursor-pointer transition">
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
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 rounded-lg cursor-pointer transition">
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

                <label class="relative">
                    <input type="radio" name="tipo" value="general" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 peer-checked:border-gray-500 peer-checked:bg-gray-100 dark:peer-checked:bg-gray-700 rounded-lg cursor-pointer transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium">Reporte General</h4>
                                <p class="text-sm text-gray-500">Resumen completo</p>
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
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ now()->subMonth()->format('Y-m-d') }}" 
                           class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                           max="{{ now()->format('Y-m-d') }}" required>
                    <p class="text-xs text-gray-500 mt-1">Fecha desde la cual generar el reporte</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ now()->format('Y-m-d') }}" 
                           class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                           max="{{ now()->format('Y-m-d') }}" required>
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
                        Esta semana (7 días)
                    </button>
                    <button type="button" onclick="setDateRange('month')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este mes (30 días)
                    </button>
                    <button type="button" onclick="setDateRange('quarter')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este trimestre (90 días)
                    </button>
                    <button type="button" onclick="setDateRange('year')" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                        Este año (365 días)
                    </button>
                </div>
            </div>

            <!-- Mensaje de validación -->
            <div id="dateValidation" class="mt-4 hidden">
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-sm text-amber-800 dark:text-amber-200" id="dateValidationMessage"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="text-sm text-gray-500">
                    <p class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        El reporte se generará en formato PDF o Excel
                    </p>
                    <p class="flex items-center mt-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tiempo estimado: <span id="estimatedTime">10-30 segundos</span>
                    </p>
                </div>
                <div class="flex space-x-3 w-full md:w-auto">
                    <button type="submit" name="action" value="pdf" class="flex-1 md:flex-none bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generar PDF
                    </button>
                    <button type="button" onclick="exportExcel()" class="flex-1 md:flex-none bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar Excel
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('javascript')
<script>
// Configurar rangos de fecha
function setDateRange(period) {
    const today = new Date();
    let startDate, endDate = today;
    
    const daysMap = {
        'today': 0,
        'week': 7,
        'month': 30,
        'quarter': 90,
        'year': 365
    };
    
    if (period && daysMap[period] !== undefined) {
        startDate = new Date(today.getTime() - daysMap[period] * 24 * 60 * 60 * 1000);
    } else {
        return;
    }
    
    document.getElementById('fecha_inicio').value = startDate.toISOString().split('T')[0];
    document.getElementById('fecha_fin').value = endDate.toISOString().split('T')[0];
    
    validateDates();
}
// Validar fechas
function validateDates() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    const validation = document.getElementById('dateValidation');
    const message = document.getElementById('dateValidationMessage');
    const estimatedTime = document.getElementById('estimatedTime');
    
    if (!fechaInicio || !fechaFin) {
        validation.classList.add('hidden');
        return true;
    }
    
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    
    if (inicio > fin) {
        validation.classList.remove('hidden');
        message.textContent = '⚠️ La fecha de inicio no puede ser mayor que la fecha de fin';
        return false;
    }
    
    const diasDif = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
    
    if (diasDif > 365) {
        validation.classList.remove('hidden');
        message.textContent = '⚠️ El rango máximo permitido es de 365 días. Actualmente: ' + diasDif + ' días';
        return false;
    }
    
    // Actualizar tiempo estimado
    if (diasDif <= 7) {
        estimatedTime.textContent = '5-10 segundos';
    } else if (diasDif <= 30) {
        estimatedTime.textContent = '10-20 segundos';
    } else if (diasDif <= 90) {
        estimatedTime.textContent = '20-40 segundos';
    } else {
        estimatedTime.textContent = '30-60 segundos';
    }
    
    validation.classList.add('hidden');
    return true;
}
// Exportar a Excel
function exportExcel() {
    if (!validateDates()) {
        alert('⚠️ Por favor corrige los errores en las fechas antes de continuar.');
        return;
    }
    
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
    tipoInput.value = document.querySelector('input[name="tipo"]:checked').value;
    
    const fechaInicioInput = document.createElement('input');
    fechaInicioInput.type = 'hidden';
    fechaInicioInput.name = 'fecha_inicio';
    fechaInicioInput.value = document.getElementById('fecha_inicio').value;
    
    const fechaFinInput = document.createElement('input');
    fechaFinInput.type = 'hidden';
    fechaFinInput.name = 'fecha_fin';
    fechaFinInput.value = document.getElementById('fecha_fin').value;
    
    form.appendChild(csrfInput);
    form.appendChild(tipoInput);
    form.appendChild(fechaInicioInput);
    form.appendChild(fechaFinInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    fechaInicio.addEventListener('change', validateDates);
    fechaFin.addEventListener('change', validateDates);
    
    // Validar antes de enviar el formulario
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
            alert('⚠️ Por favor corrige los errores en las fechas antes de generar el reporte.');
            return false;
        }
    });
    
    // Validación inicial
    validateDates();
});
</script>
@endsection