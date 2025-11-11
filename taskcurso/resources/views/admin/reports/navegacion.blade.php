@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Navegación de Reportes</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Reportes › Navegación</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Categorías de Reportes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($tiposReporte as $tipo => $info)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900">
                        @if($info['icono'] === 'dollar')
                            <svg class="w-8 h-8 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        @elseif($info['icono'] === 'box')
                            <svg class="w-8 h-8 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        @elseif($info['icono'] === 'users')
                            <svg class="w-8 h-8 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        @elseif($info['icono'] === 'chart')
                            <svg class="w-8 h-8 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-center mb-2">{{ $info['nombre'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-2">{{ $info['descripcion'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center mb-4">Total: {{ $info['total'] }} registros</p>
                    <div class="space-y-2">
                        <!-- Generar PDF -->
                        <form method="POST" action="{{ route('admin.reports.pdf') }}" class="w-full">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $tipo }}">
                            <input type="hidden" name="fecha_inicio" value="{{ now()->subMonth()->format('Y-m-d') }}">
                            <input type="hidden" name="fecha_fin" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Generar PDF
                            </button>
                        </form>

                        <!-- Exportar Excel -->
                        <form method="POST" action="{{ route('admin.reports.excel') }}" class="w-full">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $tipo }}">
                            <input type="hidden" name="fecha_inicio" value="{{ now()->subMonth()->format('Y-m-d') }}">
                            <input type="hidden" name="fecha_fin" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Exportar Excel
                            </button>
                        </form>

                        <!-- Ver Gráfico -->
                        <button onclick="generateChart('{{ $tipo }}')" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Ver Gráfico
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h3 class="text-lg font-semibold mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Filtros Personalizados -->
            <button onclick="openFilterModal()" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-left min-h-[100px] w-full">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h4 class="font-semibold text-base">Filtros Personalizados</h4>
                    <p class="text-sm text-gray-500 mt-1">Crear filtros a medida</p>
                </div>
            </button>

            <!-- Dashboard Principal -->
            <a href="{{ route('admin.reports.index') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-left min-h-[100px] w-full">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h4 class="font-semibold text-base">Dashboard Principal</h4>
                    <p class="text-sm text-gray-500 mt-1">Ver resumen general</p>
                </div>
            </a>

            <!-- Gráficos Interactivos -->
            <a href="{{ route('admin.reports.graficos') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-left min-h-[100px] w-full">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h4 class="font-semibold text-base">Gráficos Interactivos</h4>
                    <p class="text-sm text-gray-500 mt-1">Visualización avanzada</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Reportes Frecuentes -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Reportes Frecuentes</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Reporte Diario -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Reporte Diario</h4>
                    <span class="text-xs text-green-600 bg-green-100 dark:bg-green-900 px-2 py-1 rounded">📄 Rápido</span>
                </div>
                <p class="text-sm text-gray-500 mb-3">Resumen de ventas del día actual</p>
                <button onclick="generateDailyReport()" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                    Generar Ahora
                </button>
            </div>

            <!-- Reporte Semanal -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Reporte Semanal</h4>
                    <span class="text-xs text-yellow-600 bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded">📅 7 días</span>
                </div>
                <p class="text-sm text-gray-500 mb-3">Análisis semanal de rendimiento</p>
                <button onclick="generateWeeklyReport()" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                    Generar Ahora
                </button>
            </div>

            <!-- Reporte Mensual -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Reporte Mensual</h4>
                    <span class="text-xs text-blue-600 bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">📊 30 días</span>
                </div>
                <p class="text-sm text-gray-500 mb-3">Resumen del último mes</p>
                <button onclick="generateMonthlyReport()" class="w-full bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700">
                    Generar Ahora
                </button>
            </div>

            <!-- Reporte Trimestral -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Reporte Trimestral</h4>
                    <span class="text-xs text-red-600 bg-red-100 dark:bg-red-900 px-2 py-1 rounded">📈 90 días</span>
                </div>
                <p class="text-sm text-gray-500 mb-3">Análisis de los últimos 3 meses</p>
                <button onclick="generateQuarterlyReport()" class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                    Generar Ahora
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para filtros personalizados -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Filtros Personalizados</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.reports.filtros.view') }}" id="customFilterForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Tipo de Reporte</label>
                        <select name="tipo" id="modal_tipo" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                            <option value="ventas">Ventas</option>
                            <option value="productos">Productos</option>
                            <option value="pedidos">Pedidos</option>
                            <option value="usuarios">Usuarios</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Desde</label>
                            <input type="date" name="fecha_inicio" id="modal_fecha_inicio" 
                                   class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                                   value="{{ now()->subMonth()->format('Y-m-d') }}" 
                                   max="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Hasta</label>
                            <input type="date" name="fecha_fin" id="modal_fecha_fin" 
                                   class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                                   value="{{ now()->format('Y-m-d') }}" 
                                   max="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex space-x-3">
                    <button type="button" onclick="closeFilterModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Aplicar Filtro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
function generateChart(tipo) {
    window.location.href = "{{ route('admin.reports.graficos') }}?tipo=" + tipo;
}
function openFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
}
function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}
// Validar formulario de filtro
document.getElementById('customFilterForm')?.addEventListener('submit', function(e) {
    const inicio = new Date(document.getElementById('modal_fecha_inicio').value);
    const fin = new Date(document.getElementById('modal_fecha_fin').value);
    
    if (inicio > fin) {
        e.preventDefault();
        alert('⚠️ La fecha de inicio no puede ser mayor que la fecha de fin');
        return false;
    }
    
    const diasDif = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
    if (diasDif > 365) {
        e.preventDefault();
        alert('⚠️ El rango máximo permitido es de 365 días');
        return false;
    }
});
// Funciones de reportes rápidos
function submitQuickReport(tipo, dias) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.reports.pdf') }}";
    
    const today = new Date();
    const startDate = new Date(today.getTime() - dias * 24 * 60 * 60 * 1000);
    
    const csrf = createInput('_token', '{{ csrf_token() }}');
    const tipoInput = createInput('tipo', tipo);
    const fechaInicio = createInput('fecha_inicio', startDate.toISOString().split('T')[0]);
    const fechaFin = createInput('fecha_fin', today.toISOString().split('T')[0]);
    
    form.appendChild(csrf);
    form.appendChild(tipoInput);
    form.appendChild(fechaInicio);
    form.appendChild(fechaFin);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
function createInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}
function generateDailyReport() {
    submitQuickReport('ventas', 0);
}
function generateWeeklyReport() {
    submitQuickReport('ventas', 7);
}
function generateMonthlyReport() {
    submitQuickReport('ventas', 30);
}
function generateQuarterlyReport() {
    submitQuickReport('ventas', 90);
}
</script>
@endsection