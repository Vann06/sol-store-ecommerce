<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\HistorialVenta;
use App\Models\User;
use Illuminate\Http\Request;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;

class ReportAdminController extends Controller
{
    public function index()
    {
        // Asegurar que la sesión esté iniciada correctamente
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }
        
        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $ventasPorMes = new LaravelChart([
            'chart_title' => 'Ventas por Mes',
            'chart_type' => 'line',
            'report_type' => 'group_by_date',
            'model' => HistorialVenta::class,
            'group_by_field' => 'fecha_venta',
            'group_by_period' => 'month',
            'chart_color' => '239, 68, 68',
            'extra_options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                        'labels' => ['color' => '#E5E7EB']
                    ]
                ],
                'scales' => [
                    'x' => [
                        'display' => true,
                        'grid' => [
                            'display' => true,
                            'color' => 'rgba(156, 163, 175, 0.12)'
                        ],
                        'ticks' => [
                            'color' => '#CBD5E1'
                        ]
                    ],
                    'y' => [
                        'display' => true,
                        'grid' => [
                            'display' => true,
                            'color' => 'rgba(156, 163, 175, 0.12)'
                        ],
                        'ticks' => [
                            'color' => '#CBD5E1',
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ]
            ]);
        } else {
            // Fallback simple chart stub when package not available
            $pedidosPorMes = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                                ->whereYear('fecha_venta', now()->year)
                                ->groupBy('mes')
                                ->orderBy('mes')
                                ->get()
                                ->mapWithKeys(function ($row) { return [intval($row->mes) => $row->total]; });

            $svg = $this->buildChartSvg($pedidosPorMes, 600, 240, 'Ventas por Mes');
            $ventasPorMes = $this->makeFallbackChart('Ventas por Mes', $svg);
        }

        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $topProductos = new LaravelChart([
            'chart_title' => 'Top Productos por Stock',
            'chart_type' => 'bar',
            'report_type' => 'group_by_string',
            'model' => Producto::class,
            'group_by_field' => 'nombre',
            'chart_color' => '59, 130, 246',
            'extra_options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'scales' => [
                    'x' => [[ 'ticks' => [ 'color' => '#CBD5E1' ] ]],
                    'y' => [[ 'ticks' => [ 'color' => '#CBD5E1' ] ]]
                ],
                'plugins' => [ 'legend' => [ 'labels' => [ 'color' => '#E5E7EB' ] ] ]
            ]
            ]);
        } else {
            // Fallback: build a simple HTML list as chart placeholder
            $top = Producto::withCount('detalleProducto')
                ->orderBy('detalle_producto_count', 'desc')
                ->take(10)
                ->get();

            $html = '<div class="px-4 py-2"><ul class="list-disc pl-5">';
            foreach ($top as $p) {
                $html .= '<li>' . e($p->nombre) . ' (' . intval($p->detalle_producto_count) . ')</li>';
            }
            $html .= '</ul></div>';
            $topProductos = $this->makeFallbackChart('Top Productos por Stock', null, $html);
        }

        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $pedidosPorEstado = new LaravelChart([
            'chart_title' => 'Pedidos por Estado',
            'chart_type' => 'pie',
            'report_type' => 'group_by_string',
            'model' => Pedido::class,
            'group_by_field' => 'estado',
            'chart_color' => '34, 197, 94',
            'extra_options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'plugins' => [ 'legend' => [ 'labels' => [ 'color' => '#E5E7EB' ] ] ]
            ]
            ]);
        } else {
            $byState = Pedido::selectRaw('estado, COUNT(*) as total')
                        ->groupBy('estado')
                        ->pluck('total', 'estado');

            // If there is data, create a pie SVG fallback for nicer visuals
            if ($byState->isNotEmpty()) {
                $pieSvg = $this->buildPieSvg($byState, 420, 320, 'Pedidos por Estado');
                $pedidosPorEstado = $this->makeFallbackChart('Pedidos por Estado', $pieSvg);
            } else {
                // fallback to a textual list when no data
                $html = '<div class="px-4 py-2"><ul class="list-disc pl-5">';
                foreach ($byState as $st => $cnt) {
                    $html .= '<li>' . e($st) . ': ' . intval($cnt) . '</li>';
                }
                $html .= '</ul></div>';
                $pedidosPorEstado = $this->makeFallbackChart('Pedidos por Estado', null, $html);
            }
        }

        $totalVentas = HistorialVenta::sum('monto_total');
        $totalPedidos = Pedido::count();
        $totalProductos = Producto::count();
        $totalUsuarios = User::count();
        $ventasEsteMes = HistorialVenta::whereMonth('fecha_venta', now()->month)
                                     ->whereYear('fecha_venta', now()->year)
                                     ->sum('monto_total');

        // Eager load usuario and ensure the records expose a created_at attribute
        // in case the table uses a different timestamp column name.
        $pedidosRecientes = Pedido::with(['usuario'])
                                 ->select('*')
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get()
                                 ->map(function ($p) {
                                     // Normalize created_at to a Carbon instance if a different field exists
                                     if (empty($p->created_at) && !empty($p->fecha_pedido)) {
                                         $p->created_at = Carbon::parse($p->fecha_pedido);
                                     }

                                     // Ensure usuario is an object to avoid blade null property errors
                                     if (empty($p->usuario) && !empty($p->id_usuario)) {
                                         $p->usuario = User::find($p->id_usuario);
                                     }

                                     return $p;
                                 });

        return view('admin.reports.index', compact(
            'ventasPorMes',
            'topProductos', 
            'pedidosPorEstado',
            'totalVentas',
            'totalPedidos',
            'totalProductos',
            'totalUsuarios',
            'ventasEsteMes',
            'pedidosRecientes'
        ));
    }

    /**
     * Build a simple pie chart SVG from a map of label => value.
     * $data: collection label => value
     */
    private function buildPieSvg($data, $width = 420, $height = 320, $title = '')
    {
        $total = array_sum($data->toArray()) ?: 1;
        $cx = intval($width / 2);
        $cy = intval(($height / 2) + 10);
        $r = min($cx, $cy) - 20;

        $start = 0;
        $slices = '';
        $labels = '';
        $colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#06b6d4', '#f97316'];
        $i = 0;
        foreach ($data as $label => $value) {
            $angle = ($value / $total) * 2 * M_PI;
            $end = $start + $angle;

            $x1 = $cx + intval($r * cos($start));
            $y1 = $cy + intval($r * sin($start));
            $x2 = $cx + intval($r * cos($end));
            $y2 = $cy + intval($r * sin($end));

            $large = ($angle > M_PI) ? 1 : 0;
            $color = $colors[$i % count($colors)];

            $path = "M {$cx} {$cy} L {$x1} {$y1} A {$r} {$r} 0 {$large} 1 {$x2} {$y2} Z";
            $slices .= "<path d='" . $path . "' fill='" . $color . "' stroke='#ffffff' stroke-width='1'/>";

            // label positions (mid-angle)
            $mid = $start + $angle / 2;
            $lx = $cx + intval(($r + 30) * cos($mid));
            $ly = $cy + intval(($r + 30) * sin($mid));
            $pct = round(($value / $total) * 100);
            $labels .= "<text x='" . $lx . "' y='" . $ly . "' font-size='12' fill='#0f172a' text-anchor='middle' font-family='Helvetica, Arial, sans-serif'>{$label} ({$pct}%)</text>";

            $start = $end;
            $i++;
        }

     // Use currentColor for text so inline SVG inherits surrounding text color (helps dark mode)
     $titleText = $title ? "<text x='" . ($width/2) . "' y='18' font-size='16' fill='currentColor' text-anchor='middle' font-family='Helvetica, Arial, sans-serif' font-weight='600'>{$title}</text>" : '';

     // Don't draw a white background rectangle so the SVG remains transparent
     $svg = "<svg width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' xmlns='http://www.w3.org/2000/svg'>" .
         $titleText .
         "<g>{$slices}</g>" .
         "<g>{$labels}</g>" .
         "</svg>";

        return $svg;
    }

    public function calcularMetricas()
    {
        $totalVentas = HistorialVenta::sum('monto_total');
        // Use DB-specific month extraction for compatibility (MySQL vs PostgreSQL)
        $driver = config('database.default');
        if (in_array($driver, ['pgsql', 'postgres', 'postgresql'])) {
            $mesExpr = "DATE_PART('month', created_at)::int";
        } else {
            // default to MySQL style
            $mesExpr = "MONTH(created_at)";
        }

        $pedidosPorMes = Pedido::selectRaw("{$mesExpr} as mes, COUNT(*) as total")
                  ->whereYear('created_at', now()->year)
                  ->groupBy('mes')
                  ->pluck('total', 'mes');

    $topProductos = Producto::withCount('detalleProducto')
                   ->orderBy('detalle_producto_count', 'desc')
                   ->take(10)
                   ->get();

        return response()->json([
            'total_ventas' => $totalVentas,
            'pedidos_por_mes' => $pedidosPorMes,
            'top_productos' => $topProductos
        ]);
    }

    // Devuelve una vista bonita con métricas (no sólo JSON)
    public function metricas()
    {
        $totalVentas = HistorialVenta::sum('monto_total');
        // Build month extraction expression compatible with current DB
        $driver = config('database.default');
        if (in_array($driver, ['pgsql', 'postgres', 'postgresql'])) {
            $mesExpr = "DATE_PART('month', created_at)::int";
        } else {
            $mesExpr = "MONTH(created_at)";
        }

        // Use HistorialVenta->fecha_venta for sales metrics (more accurate for ventas)
        $pedidosPorMes = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                        ->whereYear('fecha_venta', now()->year)
                        ->groupBy('mes')
                        ->orderBy('mes')
                        ->get()
                        ->mapWithKeys(function ($row) {
                            return [intval($row->mes) => $row->total];
                        });

        $topProductos = Producto::withCount('detalleProducto')
                               ->orderBy('detalle_producto_count', 'desc')
                               ->take(10)
                               ->get();

        // Prefer using LaravelCharts for consistent charts across pages
        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $chartOptions = [
                'chart_title' => 'Pedidos por mes',
                'chart_type' => 'bar',
                'report_type' => 'group_by_date',
                'model' => HistorialVenta::class,
                'group_by_field' => 'fecha_venta',
                'group_by_period' => 'month',
                'chart_color' => '239, 68, 68',
                'extra_options' => [
                    'maintainAspectRatio' => false,
                    'responsive' => true,
                    'plugins' => ['legend' => ['labels' => ['color' => '#E5E7EB']]],
                    'scales' => [
                        'x' => ['ticks' => ['color' => '#CBD5E1']],
                        'y' => ['ticks' => ['color' => '#CBD5E1']]
                    ]
                ]
            ];

            $chart = new LaravelChart($chartOptions);
            $chartSvg = null;
        } else {
            // fallback to server-side SVG
            $chart = null;
            $chartSvg = $this->buildChartSvg($pedidosPorMes, 780, 320, 'Pedidos por mes');
        }

        $backUrl = route('admin.reports.index');
        return view('admin.reports.metricas', compact('totalVentas', 'pedidosPorMes', 'topProductos', 'chart', 'chartSvg', 'backUrl'));
    }

    public function mostrarGraficos()
    {
        $chartOptions = [
            'chart_title' => 'Ventas Mensuales',
            'report_type' => 'group_by_date',
            'model' => HistorialVenta::class,
            'group_by_field' => 'fecha_venta',
            'group_by_period' => 'month',
            'chart_type' => 'bar',
            'chart_color' => '239, 68, 68'
        ];

        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $chart = new LaravelChart($chartOptions);
        } else {
            // Fallback: create a small SVG based on recent ventas
            $data = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                        ->whereYear('fecha_venta', now()->year)
                        ->groupBy('mes')
                        ->orderBy('mes')
                        ->get()
                        ->mapWithKeys(function ($row) { return [intval($row->mes) => $row->total]; });

            $svg = $this->buildChartSvg($data, 700, 300, 'Ventas Mensuales');
            $chart = $this->makeFallbackChart('Ventas Mensuales', $svg);
        }

        $backUrl = route('admin.reports.index');
        return view('admin.reports.graficos', compact('chart', 'backUrl'));
    }

    public function filtrarFechas(Request $request)
    {
        // Manejar sesión y CSRF
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }
        try {
            $fechaInicio = $request->input('fecha_inicio')
                ? Carbon::parse($request->input('fecha_inicio'))
                : now()->subDays(7);
            $fechaFin = $request->input('fecha_fin')
                ? Carbon::parse($request->input('fecha_fin'))
                : now();
            $mes = $request->input('mes');
            $personalizado = $request->boolean('personalizado');

            if ($mes) {
                $fechaInicio = Carbon::create(now()->year, intval($mes), 1)->startOfMonth();
                $fechaFin = Carbon::create(now()->year, intval($mes), 1)->endOfMonth();
            } else if (!$personalizado) {
                $fechaInicio = now()->subDays(7);
                $fechaFin = now();
            }

            $ventasCount = HistorialVenta::whereBetween('fecha_venta', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])->count();
            if ($ventasCount == 0) {
                $this->crearDatosPrueba($fechaInicio, $fechaFin);
            }

            if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
                $chartFiltrado = new LaravelChart([
                    'chart_title' => 'Ventas Filtradas (' . $fechaInicio->format('d/m/Y') . ' - ' . $fechaFin->format('d/m/Y') . ')',
                    'chart_type' => 'line',
                    'report_type' => 'group_by_date',
                    'model' => HistorialVenta::class,
                    'group_by_field' => 'fecha_venta',
                    'group_by_period' => 'day',
                    'conditions' => [
                        [
                            'name' => 'Período Personalizado',
                            'condition' => 'fecha_venta >= "' . $fechaInicio->format('Y-m-d') . '" AND fecha_venta <= "' . $fechaFin->format('Y-m-d') . '"',
                            'color' => 'red'
                        ]
                    ],
                    'extra_options' => [
                        'maintainAspectRatio' => false,
                        'responsive' => true,
                        'plugins' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'top'
                            ]
                        ]
                    ]
                ]);
            } else {
                $pedidos = HistorialVenta::selectRaw("DATE(fecha_venta) as dia, COUNT(*) as total")
                            ->whereBetween('fecha_venta', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
                            ->groupBy('dia')
                            ->orderBy('dia')
                            ->get()
                            ->mapWithKeys(function ($r) { return [Carbon::parse($r->dia)->day => $r->total]; });

                $svg = $this->buildChartSvg($pedidos, 780, 320, 'Ventas Filtradas');
                $chartFiltrado = $this->makeFallbackChart('Ventas Filtradas', $svg);
            }

            $backUrl = route('admin.reports.index');
            return view('admin.reports.filtros', compact('chartFiltrado', 'fechaInicio', 'fechaFin', 'backUrl'));
        } catch (\Exception $e) {
            // En caso de error, usar valores por defecto y mostrar mensaje
            $fechaInicio = now()->subDays(7);
            $fechaFin = now();

            if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
                $chartFiltrado = new LaravelChart([
                    'chart_title' => 'Sin datos disponibles',
                    'chart_type' => 'line',
                    'report_type' => 'group_by_date',
                    'model' => HistorialVenta::class,
                    'group_by_field' => 'fecha_venta',
                    'group_by_period' => 'day',
                ]);
            } else {
                $chartFiltrado = $this->makeFallbackChart('Sin datos disponibles', $this->buildChartSvg(collect(), 780, 320, 'Sin datos'));
            }

            $backUrl = route('admin.reports.index');
            return view('admin.reports.filtros', compact('chartFiltrado', 'fechaInicio', 'fechaFin', 'backUrl'))
                ->with('error', 'Error al procesar los filtros. Mostrando datos por defecto.');
        }
    }

    public function exportarPdf(Request $request)
    {
        // Manejar sesión y CSRF
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }
        
    $tipoReporte = $request->input('tipo', 'general');
    // If 'fast' is true, skip server-side rasterization to save CPU/time.
    $fast = $request->boolean('fast', false);
        $fechaInicio = $request->input('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : now()->subMonth();
        $fechaFin = $request->input('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : now();

        $datos = $this->obtenerDatosReporte($tipoReporte, $fechaInicio, $fechaFin);

    // Generar un SVG simple para incluir en el PDF si aplicable (ventas/pedidos)
    $chartSvg = null;
    $chartPngBase64 = null;
        if (in_array($tipoReporte, ['general', 'ventas', 'pedidos'])) {
            // Reuse the metricas-style data for pedidos por mes
                // Use sales records (HistorialVenta) so the PDF shows ventas by fecha_venta
                $pedidosPorMes = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                                    ->whereBetween('fecha_venta', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
                                ->groupBy('mes')
                                ->orderBy('mes')
                                ->get()
                                ->mapWithKeys(function ($row) {
                                    return [intval($row->mes) => $row->total];
                                });

            // increase size for pdf export so the rasterized PNG is clearer
            $chartSvg = $this->buildChartSvg($pedidosPorMes, 900, 360, 'Pedidos por mes');

            // Prepare a PDF-friendly SVG that includes a white background rectangle
            // so rasterizers and DomPDF render it consistently on a white page.
            $chartSvgPdf = preg_replace('/<svg([^>]*)>/i', '<svg$1><rect width="100%" height="100%" fill="#ffffff"/>', $chartSvg, 1);

            // Replace 'currentColor' with an explicit dark color for PDF/rasterization
            // to avoid DomPDF or rasterizers inheriting unexpected colors.
            $chartSvgPdf = str_replace("currentColor", "#0f172a", $chartSvgPdf);

            // Attempt to rasterize via Imagick when available to produce a PNG for the PDF.
            // This is more reliable than relying on DomPDF to fetch or render SVGs.
            if (class_exists('Imagick')) {
                try {
                    $svgBlob = (strpos($chartSvgPdf, '<?xml') === 0) ? $chartSvgPdf : "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" . $chartSvgPdf;

                    $im = new \Imagick();
                    // Set density before reading to influence raster quality
                    try { $im->setOption('density', '150'); } catch (\Throwable $_) {}

                    $im->readImageBlob($svgBlob);

                    // Prefer png32 to keep quality; fallback to png24 if needed
                    try {
                        $im->setImageFormat('png32');
                    } catch (\Throwable $_) {
                        try { $im->setImageFormat('png24'); } catch (\Throwable $_) {}
                    }

                    // Ensure alpha channel is active (defensive)
                    try {
                        if (method_exists($im, 'getImageAlphaChannel') && $im->getImageAlphaChannel() === \Imagick::ALPHACHANNEL_UNDEFINED) {
                            try { $im->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE); } catch (\Throwable $_) {}
                        }
                    } catch (\Throwable $_) {}

                    $blob = $im->getImageBlob();

                    if (!empty($blob)) {
                        $chartPngBase64 = base64_encode($blob);
                    }

                    // free resources: only clear/destroy the Imagick instance we created
                    try { if (isset($im) && is_object($im)) { $im->clear(); $im->destroy(); } } catch (\Throwable $_) {}
                } catch (\Exception $e) {
                    // keep chartPngBase64 null; we'll embed the PDF-safe inline SVG instead
                }
            }

            // Optional debug dump: write the intermediate SVG/PNG for inspection when requested
            try {
                if ($request->boolean('debug_dump', false)) {
                    if (!empty($chartSvgPdf)) {
                        @file_put_contents(storage_path('logs/report_chartSvgPdf.svg'), $chartSvgPdf);
                    }
                    if (!empty($chartPngBase64)) {
                        @file_put_contents(storage_path('logs/report_chartPng.png'), base64_decode($chartPngBase64));
                    }
                    if (!empty($chartSvg)) {
                        @file_put_contents(storage_path('logs/report_chartSvg.svg'), $chartSvg);
                    }
                }
            } catch (\Throwable $_) {
                // ignore any filesystem errors during debug dump
            }
        }

        // Configurar DomPDF con opciones mejoradas para mejor renderizado
        try {
            Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isJavascriptEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'Arial',
                'dpi' => 150,
                'defaultPaperSize' => 'A4'
            ]);
        } catch (\Throwable $e) {
            // Fallback para versiones anteriores de DomPDF
            try {
                                // Best-effort: if Imagick exists, convert the PNG directly to a single-page PDF
                                // which guarantees the image appears in the PDF. This is fast and portable.
                                if (class_exists('Imagick')) {
                                    try {
                                        $im = new \Imagick();
                                        $im->readImageBlob($chartBlob);
                                        // Ensure proper PDF output
                                        $im->setImageFormat('pdf');
                                        $pdfBlob = $im->getImagesBlob();
                                        // cleanup
                                        try { $im->clear(); $im->destroy(); } catch (\Throwable $_) {}

                                        return response($pdfBlob, 200, [
                                            'Content-Type' => 'application/pdf',
                                            'Content-Disposition' => 'attachment; filename="reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf"'
                                        ]);
                                    } catch (\Throwable $e) {
                                        // If Imagick fails, fall back to DomPDF embedding below
                                    }
                                }

                                // Fallback: render minimal HTML with embedded PNG (DomPDF)
                                $onlyPngHtml = '<!doctype html><html><head><meta charset="utf-8"><title>Reporte</title></head><body style="font-family:Arial,sans-serif;margin:20px">'
                                    . '<h2 style="color:#dc2626">' . e($datos['titulo'] ?? 'Reporte') . '</h2>'
                                    . '<p>Generado el: ' . now()->format('d/m/Y H:i:s') . '</p>'
                                    . '<div style="text-align:center;margin-top:10px"><img src="data:image/png;base64,' . $chartPngBase64 . '" width="800" height="400" alt="Grafico"></div>'
                                    . '<div style="margin-top:20px">' . $detailsHtml . '</div>'
                                    . '</body></html>';

                                $pdf = Pdf::loadHTML($onlyPngHtml);
                                $pdf->setPaper('A4', 'portrait');
                                return $pdf->download('reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf');
        }
        // Force embedding the rasterized PNG when available — reliability over CPU
        if (!empty($chartPngBase64)) {
            $chartImageUrl = null; // ensure view selects embedded PNG
        } else {
            // if no rasterized PNG available, keep chartImageUrl (fast remote fetch) if requested
            if (!empty($chartImageUrl)) {
                // leave as-is so DomPDF can fetch the image
            }
        }

        // Generar el PDF con configuración mejorada
        // If we have a rasterized PNG, produce a small guaranteed-PNG PDF first
        if (!empty($chartPngBase64)) {
            // Build a simple details section depending on report type so PDF includes tabular data
            $detailsHtml = '';
            if ($tipoReporte === 'ventas' && !empty($datos['ventas'])) {
                $detailsHtml .= '<h3>Detalle de Ventas</h3>';
                $detailsHtml .= '<table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse">';
                $detailsHtml .= '<thead><tr><th>ID</th><th>Fecha</th><th>Monto</th><th>Pedido</th></tr></thead><tbody>';
                foreach ($datos['ventas'] as $v) {
                    $fecha = isset($v->fecha_venta) ? ( $v->fecha_venta instanceof \Carbon\Carbon ? $v->fecha_venta->format('d/m/Y') : \Carbon\Carbon::parse($v->fecha_venta)->format('d/m/Y') ) : 'N/A';
                    $detailsHtml .= '<tr>' . '<td>' . e($v->id) . '</td>' . '<td>' . e($fecha) . '</td>' . '<td>Q' . number_format($v->monto_total,2) . '</td>' . '<td>' . e($v->id_pedido ?? '') . '</td>' . '</tr>';
                }
                $detailsHtml .= '</tbody></table>';
            } elseif ($tipoReporte === 'pedidos' && !empty($datos['pedidos'])) {
                $detailsHtml .= '<h3>Detalle de Pedidos</h3>';
                $detailsHtml .= '<table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse">';
                $detailsHtml .= '<thead><tr><th>ID</th><th>Usuario</th><th>Fecha Pedido</th><th>Estado</th></tr></thead><tbody>';
                foreach ($datos['pedidos'] as $p) {
                    $fecha = isset($p->fecha_pedido) ? ( $p->fecha_pedido instanceof \Carbon\Carbon ? $p->fecha_pedido->format('d/m/Y') : \Carbon\Carbon::parse($p->fecha_pedido)->format('d/m/Y') ) : 'N/A';
                    $detailsHtml .= '<tr>' . '<td>' . e($p->id) . '</td>' . '<td>' . e($p->id_usuario ?? '') . '</td>' . '<td>' . e($fecha) . '</td>' . '<td>' . e(ucfirst($p->estado ?? '')) . '</td>' . '</tr>';
                }
                $detailsHtml .= '</tbody></table>';
            } else {
                // Generic resumen if present
                if (!empty($datos['resumen'])) {
                    $detailsHtml .= '<h3>Resumen</h3><pre>' . e(json_encode($datos['resumen'], JSON_PRETTY_PRINT)) . '</pre>';
                } elseif (!empty($datos['total'])) {
                    $detailsHtml .= '<h3>Totales</h3><p>Total registros: ' . e($datos['total']) . '</p>';
                }
            }

            $chartBlob = base64_decode($chartPngBase64);

            // Try direct PNG -> PDF conversion using Imagick (most reliable and fast).
            if (class_exists('Imagick')) {
                try {
                    $imPdf = new \Imagick();
                    $imPdf->readImageBlob($chartBlob);
                    // ensure single-page PDF output
                    $imPdf->setImageFormat('pdf');
                    $pdfBlob = $imPdf->getImagesBlob();
                    try { if (isset($imPdf) && is_object($imPdf)) { $imPdf->clear(); $imPdf->destroy(); } } catch (\Throwable $_) {}

                    return response($pdfBlob, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf"'
                    ]);
                } catch (\Throwable $_) {
                    // If Imagick PDF conversion fails, fall through to DomPDF embedding below
                }
            }

            // Fallback: render minimal HTML with embedded PNG (DomPDF)
            $onlyPngHtml = '<!doctype html><html><head><meta charset="utf-8"><title>Reporte</title></head><body style="font-family:Arial,sans-serif;margin:20px'>
                . '<h2 style="color:#dc2626">' . e($datos['titulo'] ?? 'Reporte') . '</h2>'
                . '<p>Generado el: ' . now()->format('d/m/Y H:i:s') . '</p>'
                . '<div style="text-align:center;margin-top:10px"><img src="data:image/png;base64,' . $chartPngBase64 . '" width="800" height="400" alt="Grafico"></div>'
                . '<div style="margin-top:20px">' . $detailsHtml . '</div>'
                . '</body></html>';

            $pdf = Pdf::loadHTML($onlyPngHtml);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf');
        }

        // Otherwise render full view which may use SVG/data-uri or remote fetch
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'datos' => $datos,
            'tipo' => $tipoReporte,
            'fecha_generacion' => now(),
            'chartSvg' => $chartSvg ?? null,
            'chartSvgPdf' => $chartSvgPdf ?? null,
            'chartPngBase64' => $chartPngBase64,
            'chartImageUrl' => $chartImageUrl
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf');
    }

    public function validarAccesosPorRol()
    {
        return response()->json([
            'message' => 'Validación de accesos implementada',
            'roles_permitidos' => ['admin', 'super_admin']
        ]);
    }

    public function probarExportacionFormato()
    {
        $formatos = ['pdf', 'excel', 'csv'];
        
        return response()->json([
            'message' => 'Formatos disponibles para exportación',
            'formatos_soportados' => $formatos
        ]);
    }

    public function estilizarVistaReportes()
    {
        $tiposReporte = [
            'ventas' => 'Reportes de Ventas',
            'productos' => 'Reportes de Productos', 
            'usuarios' => 'Reportes de Usuarios',
            'inventario' => 'Reportes de Inventario'
        ];

        return view('admin.reports.navegacion', compact('tiposReporte'));
    }

    /**
     * Devuelve una imagen (PNG) del gráfico de pedidos por mes para el rango solicitado.
     * Si Imagick no está disponible, devuelve el SVG con content-type image/svg+xml.
     */
    public function chartImage(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : now()->subMonth();
        $fechaFin = $request->input('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : now();

        // Use ventas (HistorialVenta) to generate the chart image for ventas/pedidos
        $pedidosPorMes = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                            ->whereBetween('fecha_venta', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
                            ->groupBy('mes')
                            ->orderBy('mes')
                            ->get()
                            ->mapWithKeys(function ($row) {
                                return [intval($row->mes) => $row->total];
                            });

        $max = $pedidosPorMes->max() ?: 1;
    // increase size for chart-image endpoint so remote fetch returns larger image
    $chartSvg = $this->buildChartSvg($pedidosPorMes, 900, 360, 'Pedidos por mes');

        // Intentar convertir a PNG si Imagick está disponible
        if (class_exists('Imagick')) {
                try {
                    $im = new \Imagick();
                    $svgBlob = (strpos($chartSvg, '<?xml') === 0) ? $chartSvg : "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" . $chartSvg;
                    try { $im->setOption('density', '150'); } catch (\Throwable $_) {}
                    $im->readImageBlob($svgBlob);

                    try {
                        $im->setImageFormat('png32');
                    } catch (\Throwable $_) {
                        try { $im->setImageFormat('png24'); } catch (\Throwable $_) {}
                    }

                    // If alpha isn't supported, attempt to enable it before exporting
                    try {
                        if (method_exists($im, 'getImageAlphaChannel') && $im->getImageAlphaChannel() === \Imagick::ALPHACHANNEL_UNDEFINED) {
                            try { $im->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE); } catch (\Throwable $_) {}
                        }
                    } catch (\Throwable $_) {}

                    $blob = $im->getImageBlob();
                    $headers = ['Content-Type' => 'image/png', 'Content-Length' => strlen($blob)];
                    return response($blob, 200, $headers);
                } catch (\Exception $e) {
                    // Fallback a SVG
                    $svgHeaders = ['Content-Type' => 'image/svg+xml; charset=utf-8', 'Content-Length' => strlen($chartSvg)];
                    return response($chartSvg, 200, $svgHeaders);
                }
        }

        $svgHeaders = ['Content-Type' => 'image/svg+xml; charset=utf-8', 'Content-Length' => strlen($chartSvg)];
        return response($chartSvg, 200, $svgHeaders);
    }

    /**
     * Endpoint de diagnóstico para verificar presencia de token CSRF y sesión.
     * Devuelve valores enmascarados (no exponemos tokens completos).
     */
    public function diagnosticoCsrf()
    {
        $hasToken = session()->has('_token');
        $sessionId = session()->getId();
        $csrf = csrf_token();

        $maskedCsrf = strlen($csrf) > 8 ? substr($csrf, 0, 6) . '...' . substr($csrf, -4) : 'masked';
        $maskedSession = $sessionId ? substr($sessionId, 0, 6) . '...' : null;

        return response()->json([
            'session_has_token' => $hasToken,
            'session_id_masked' => $maskedSession,
            'csrf_masked' => $maskedCsrf,
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain'),
            'session_samesite' => config('session.same_site')
        ]);
    }

    /**
     * Construye un SVG estilizado para un set de datos 'pedidos por mes'.
     * $data: colección clave=mes (1..12) => valor
     */
    private function buildChartSvg($data, $width = 520, $height = 200, $title = '')
    {
        // padding and internal dimensions
        $padLeft = 50;
        $padRight = 20;
        $padTop = 36;
        $padBottom = 56;
        $innerW = $width - $padLeft - $padRight;
        $innerH = $height - $padTop - $padBottom;

        // ensure $data has 12 months keys to maintain spacing even if empty
        $months = range(1, 12);
        $values = array_map(function ($m) use ($data) { return (int) $data->get($m, 0); }, $months);
        $max = max(1, max($values));

        // compute bar width based on actual available width
        $barW = intval($innerW / (count($months) * 1.6));
        $totalBarsW = $barW * count($months);
        $gap = intval(($innerW - $totalBarsW) / (count($months) - 1));

        $bars = '';
        $ticks = '';

        // y ticks (5 ticks) - darker for better contrast
        for ($t = 0; $t <= 4; $t++) {
            $yy = $padTop + intval($innerH - ($t * ($innerH / 4)));
            $val = intval(($t * $max) / 4);
            $ticks .= "<line x1='{$padLeft}' y1='{$yy}' x2='" . ($padLeft + $innerW) . "' y2='{$yy}' stroke='#9CA3AF' stroke-width='1' opacity='0.35'/>";
            $ticks .= "<text x='" . ($padLeft - 10) . "' y='" . ($yy + 5) . "' font-size='12' fill='#111827' text-anchor='end' font-family='Helvetica, Arial, sans-serif'>{$val}</text>";
        }

        $i = 0;
        foreach ($months as $m) {
            $val = (int) $data->get($m, 0);
            $barH = $max > 0 ? intval(($val / $max) * $innerH) : 0;
            $x = $padLeft + $i * ($barW + $gap);
            $y = $padTop + ($innerH - $barH);
            $monthLabel = Carbon::create()->month($m)->format('M');

            // draw bar with solid fill and subtle stroke for visibility in dark modes
            $bars .= "<rect x='" . ($x) . "' y='" . ($y) . "' width='{$barW}' height='{$barH}' fill='#ef4444' stroke='#b91c1c' stroke-width='1'/>";

            // value label above the bar (skip for zero to avoid clutter)
            if ($val > 0) {
                $labelY = max($padTop + 12, $y - 8);
                $bars .= "<text x='" . ($x + intval($barW/2)) . "' y='" . ($labelY) . "' font-size='12' fill='#0f172a' font-weight='600' text-anchor='middle' font-family='Helvetica, Arial, sans-serif'>{$val}</text>";
            }

            // month label below
            $bars .= "<text x='" . ($x + intval($barW/2)) . "' y='" . ($padTop + $innerH + 20) . "' font-size='12' fill='#374151' text-anchor='middle' font-family='Helvetica, Arial, sans-serif'>{$monthLabel}</text>";
            $i++;
        }

     // Use currentColor for text so inline SVG inherits surrounding text color (helps dark mode)
     $titleText = $title ? "<text x='" . ($width/2) . "' y='18' font-size='16' fill='currentColor' text-anchor='middle' font-family='Helvetica, Arial, sans-serif' font-weight='600'>{$title}</text>" : '';

     // Transparent background so SVG respects dark mode. When rasterizing, we will try to preserve transparency.
     $svg = "<svg width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' xmlns='http://www.w3.org/2000/svg' role='img' aria-label='" . htmlentities($title) . "'>" .
         "{$titleText}" .
         "<g>{$ticks}</g>" .
         "<g>{$bars}</g>" .
         "</svg>";

        return $svg;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }
        
        return view('admin.reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Redirect to PDF export since reports are not stored
        return $this->exportarPdf($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Crear datos de prueba para demostración cuando no hay datos reales
     */
    private function crearDatosPrueba($fechaInicio, $fechaFin)
    {
        $diasEntre = $fechaInicio->diffInDays($fechaFin);
        
        for ($i = 0; $i <= min($diasEntre, 10); $i++) {
            $fecha = $fechaInicio->copy()->addDays($i);
            
            // Verificar si ya existe una venta para esa fecha
            $existeVenta = HistorialVenta::where('fecha_venta', $fecha->format('Y-m-d'))->exists();
            
            if (!$existeVenta) {
                HistorialVenta::create([
                    'id_pedido' => rand(1, 100),
                    'monto_total' => rand(50, 500) + (rand(0, 99) / 100), // Valor entre 50.00 y 500.99
                    'fecha_venta' => $fecha->format('Y-m-d'),
                    'created_at' => $fecha,
                    'updated_at' => $fecha
                ]);
            }
        }
    }
    
    private function obtenerDatosReporte($tipo, $fechaInicio, $fechaFin)
    {
        switch ($tipo) {
            case 'ventas':
                return [
                    'titulo' => 'Reporte de Ventas',
                    'ventas' => HistorialVenta::whereBetween('fecha_venta', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])->get(),
                    'total' => HistorialVenta::whereBetween('fecha_venta', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])->sum('monto_total')
                ];
            
            case 'productos':
                return [
                    'titulo' => 'Reporte de Productos',
                    'productos' => Producto::with(['category', 'theme'])->get(),
                    'total_productos' => Producto::count()
                ];
                
            case 'pedidos':
                return [
                    'titulo' => 'Reporte de Pedidos', 
                    'pedidos' => Pedido::whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])->get(),
                        'total_pedidos' => Pedido::whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])->count()
                ];
                
            default:
                return [
                    'titulo' => 'Reporte General',
                    'resumen' => [
                        'total_ventas' => HistorialVenta::sum('monto_total'),
                        'total_pedidos' => Pedido::count(),
                        'total_productos' => Producto::count(),
                        'usuarios_registrados' => User::count()
                    ]
                ];
        }
    }

    /**
     * Crea un objeto 'chart-like' de reemplazo para las vistas cuando la
     * clase LaravelChart no está disponible. El objeto expone:
     * - options (array)
     * - renderHtml() -> string (HTML/SVG)
     * - renderJs() -> string (JS placeholder)
     * - renderChartJsLibrary() -> string (script tag placeholder)
     */
    private function makeFallbackChart($title, $svg = null, $html = null)
    {
        $options = ['chart_title' => $title];

        return new class($options, $svg, $html) {
            public $options;
            private $svg;
            private $html;

            public function __construct($options, $svg = null, $html = null)
            {
                $this->options = $options;
                $this->svg = $svg;
                $this->html = $html;
            }

            public function renderHtml()
            {
                if (!empty($this->svg)) {
                    return $this->svg;
                }
                if (!empty($this->html)) {
                    return $this->html;
                }
                return '<div class="p-4 text-gray-600">No hay datos para mostrar</div>';
            }

            public function renderJs()
            {
                return ''; // no-op for fallback
            }

            public function renderChartJsLibrary()
            {
                return ''; // no library to include
            }
        };
    }
}
