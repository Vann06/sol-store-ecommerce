@props([
    'backUrl' => null,
    'showPdf' => true,
    'showExcel' => true,
    'showImage' => false,
    'pdfRoute' => 'admin.reports.pdf',
    'excelRoute' => 'admin.reports.excel',
    'imageRoute' => 'admin.reports.chart.image',
    'tipo' => 'general',
    'fechaInicio' => null,
    'fechaFin' => null,
    'size' => 'normal' // 'small', 'normal', 'large'
])

@php
    $sizeClasses = [
        'small' => 'px-3 py-1 text-sm',
        'normal' => 'px-4 py-2',
        'large' => 'px-6 py-3 text-lg'
    ];
    $buttonClass = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="flex items-center space-x-2">
    @if($backUrl)
        <a href="{{ $backUrl }}" class="bg-gray-600 text-white {{ $buttonClass }} rounded-lg hover:bg-gray-700 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
    @endif

    @if($showPdf)
        <form method="POST" action="{{ route($pdfRoute) }}" class="inline">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">
            @if($fechaInicio)
                <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
            @endif
            @if($fechaFin)
                <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
            @endif
            <button type="submit" class="bg-red-600 text-white {{ $buttonClass }} rounded-lg hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                PDF
            </button>
        </form>
    @endif

    @if($showExcel)
        <form method="POST" action="{{ route($excelRoute) }}" class="inline">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">
            @if($fechaInicio)
                <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
            @endif
            @if($fechaFin)
                <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
            @endif
            <button type="submit" class="bg-green-600 text-white {{ $buttonClass }} rounded-lg hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Excel
            </button>
        </form>
    @endif

    @if($showImage)
        <a href="{{ route($imageRoute, ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" target="_blank" class="bg-blue-600 text-white {{ $buttonClass }} rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Imagen
        </a>
    @endif
</div>
