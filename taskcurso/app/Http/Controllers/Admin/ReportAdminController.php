<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\HistorialVenta;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportesExport;
use Carbon\Carbon;

class ReportAdminController extends Controller
{
    public function index()
    {
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }

        // SOLO generar datos ficticios en entorno local si no existen (evita FK y lentitud en producción)
        if (app()->environment('local') && (HistorialVenta::count() === 0 || Pedido::count() === 0)) {
            $this->crearDatosPrueba(now()->subMonth(), now());
        }

        // Gráficos con LaravelCharts
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
                    'responsive' => true
                ]
            ]);

            $topProductos = new LaravelChart([
                'chart_title' => 'Top Productos por Stock',
                'chart_type' => 'bar',
                'report_type' => 'group_by_string',
                'model' => Producto::class,
                'group_by_field' => 'nombre',
                'chart_color' => '59, 130, 246',
                'extra_options' => [
                    'maintainAspectRatio' => false,
                    'responsive' => true
                ]
            ]);

            $pedidosPorEstado = new LaravelChart([
                'chart_title' => 'Estado de Pedidos',
                'chart_type' => 'pie',
                'report_type' => 'group_by_string',
                'model' => Pedido::class,
                'group_by_field' => 'estado',
                'chart_color' => '34, 197, 94',
                'extra_options' => [
                    'maintainAspectRatio' => false,
                    'responsive' => true
                ]
            ]);
        } else {
            // Fallback manual con datos agregados si la librería de charts no está instalada
            $ventasMesData = HistorialVenta::selectRaw("DATE_PART('month', fecha_venta) as mes, COUNT(*) as total")
                ->whereYear('fecha_venta', now()->year)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->mapWithKeys(fn($r)=>[intval($r->mes)=>intval($r->total)]);
            $ventasPorMesSvg = $this->buildChartSvg(collect($ventasMesData), 520, 220, 'Ventas por Mes');
            $ventasPorMesHtml = $this->buildSimpleTableHtml('Ventas por Mes (Conteo)', ['Mes','Total'],
                collect(range(1,12))->map(function($m) use ($ventasMesData){
                    return [Carbon::create()->month($m)->format('M'), $ventasMesData[$m] ?? 0];
                })->toArray()
            );
            $ventasPorMes = $this->makeFallbackChart('Ventas por Mes', $ventasPorMesSvg, $ventasPorMesHtml);

            $topProductosData = Producto::select('nombre','stock')
                ->orderByDesc('stock')
                ->take(8)
                ->get()
                ->map(fn($p)=>[$p->nombre, $p->stock]);
            $topProductosHtml = $this->buildSimpleTableHtml('Top Productos por Stock', ['Producto','Stock'], $topProductosData->toArray());
            $topProductos = $this->makeFallbackChart('Top Productos', null, $topProductosHtml);

            $estadoData = Pedido::select('estado',\DB::raw('COUNT(*) as total'))
                ->groupBy('estado')
                ->get()
                ->map(fn($e)=>[$e->estado, $e->total]);
            $pedidosPorEstadoHtml = $this->buildSimpleTableHtml('Pedidos por Estado', ['Estado','Total'], $estadoData->toArray());
            $pedidosPorEstado = $this->makeFallbackChart('Estado de Pedidos', null, $pedidosPorEstadoHtml);
        }

        // Cachear métricas pesadas por 60 segundos para evitar múltiples COUNT/SUM en cada request
        [$totalVentas, $totalPedidos, $totalProductos, $totalUsuarios, $ventasEsteMes] = Cache::remember('admin_reports_totals', 60, function () {
            return [
                HistorialVenta::sum('monto_total'),
                Pedido::count(),
                Producto::count(),
                User::count(),
                HistorialVenta::whereMonth('fecha_venta', now()->month)
                    ->whereYear('fecha_venta', now()->year)
                    ->sum('monto_total'),
            ];
        });

        $pedidosRecientes = Pedido::with(['usuario'])
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get()
                                 ->map(function ($p) {
                                     if (empty($p->created_at) && !empty($p->fecha_pedido)) {
                                         $p->created_at = Carbon::parse($p->fecha_pedido);
                                     }
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

    public function metricas()
    {
        $totalVentas = HistorialVenta::sum('monto_total');

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

        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $chart = new LaravelChart([
                'chart_title' => 'Pedidos por mes',
                'chart_type' => 'bar',
                'report_type' => 'group_by_date',
                'model' => HistorialVenta::class,
                'group_by_field' => 'fecha_venta',
                'group_by_period' => 'month',
                'chart_color' => '239, 68, 68',
                'extra_options' => [
                    'maintainAspectRatio' => false,
                    'responsive' => true
                ]
            ]);
            $chartSvg = null;
        } else {
            $chart = null;
            $chartSvg = $this->buildChartSvg($pedidosPorMes, 780, 320, 'Pedidos por mes');
        }

        $backUrl = route('admin.reports.index');
        return view('admin.reports.metricas', compact('totalVentas', 'pedidosPorMes', 'topProductos', 'chart', 'chartSvg', 'backUrl'));
    }

    public function mostrarGraficos(Request $request)
    {
        $tipo = $request->get('tipo', 'ventas');
        $chartType = $request->get('chart_type', 'bar');

        $validChartTypes = ['bar', 'line', 'pie', 'doughnut'];
        if (!in_array($chartType, $validChartTypes)) {
            $chartType = 'bar';
        }

        $chartOptions = [];
        $chartTitle = '';

        switch ($tipo) {
            case 'ventas':
                $chartTitle = 'Ventas Mensuales';
                $chartOptions = [
                    'chart_title' => $chartTitle,
                    'report_type' => 'group_by_date',
                    'model' => HistorialVenta::class,
                    'group_by_field' => 'fecha_venta',
                    'group_by_period' => 'month',
                    'chart_type' => $chartType,
                    'chart_color' => '239, 68, 68'
                ];
                break;
            case 'productos':
                $chartTitle = 'Productos por Categoría';
                $chartOptions = [
                    'chart_title' => $chartTitle,
                    'report_type' => 'group_by_string',
                    'model' => Producto::class,
                    'group_by_field' => 'category_id',
                    'chart_type' => $chartType,
                    'chart_color' => '59, 130, 246'
                ];
                break;
            case 'usuarios':
                $chartTitle = 'Usuarios Registrados';
                $chartOptions = [
                    'chart_title' => $chartTitle,
                    'report_type' => 'group_by_date',
                    'model' => User::class,
                    'group_by_field' => 'created_at',
                    'group_by_period' => 'month',
                    'chart_type' => $chartType,
                    'chart_color' => '34, 197, 94'
                ];
                break;
            case 'inventario':
                $chartTitle = 'Pedidos por Estado';
                $chartOptions = [
                    'chart_title' => $chartTitle,
                    'report_type' => 'group_by_string',
                    'model' => Pedido::class,
                    'group_by_field' => 'estado',
                    'chart_type' => $chartType,
                    'chart_color' => '147, 51, 234'
                ];
                break;
        }

        if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
            $chartOptions['extra_options'] = [
                'maintainAspectRatio' => false,
                'responsive' => true
            ];
            $chart = new LaravelChart($chartOptions);
        } else {
            $chart = $this->makeFallbackChart($chartTitle, null);
        }

        $backUrl = route('admin.reports.index');
        return view('admin.reports.graficos', compact('chart', 'backUrl', 'tipo', 'chartType'));
    }

    public function filtrarFechas(Request $request)
    {
        // Validar y obtener fechas
        $fechaInicio = $request->input('fecha_inicio') 
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $fechaFin = $request->input('fecha_fin') 
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : now()->endOfDay();

        // Validaciones
        if ($fechaInicio->greaterThan($fechaFin)) {
            return back()->with('error', 'La fecha de inicio no puede ser mayor que la fecha de fin');
        }

        $diasDiferencia = $fechaInicio->diffInDays($fechaFin);
        if ($diasDiferencia > 365) {
            return back()->with('error', 'El rango máximo permitido es de 365 días (1 año)');
        }

        // CONSULTA REAL - Filtrar ventas por fecha
        $ventas = HistorialVenta::with('pedido.usuario')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        // CONSULTA REAL - Filtrar pedidos por fecha
        $pedidos = Pedido::with('usuario')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas REALES y MÉTRICAS AVANZADAS
        $totalVentas = $ventas->sum('monto_total');
        $totalPedidos = $pedidos->count();
        $promedioVenta = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;
        
        // Métricas adicionales para pedidos
        $pedidosPagados = $pedidos->where('payment_status', 'succeeded')->count();
        $pedidosPendientes = $pedidos->where('payment_status', 'pending')->count();
        $tasaConversion = $totalPedidos > 0 ? ($pedidosPagados / $totalPedidos) * 100 : 0;
        
        // Agrupar pedidos por estado
        $pedidosPorEstado = $pedidos->groupBy('estado')->map(fn($group) => $group->count());
        
        // Top clientes en este período
        $topClientes = $pedidos->groupBy('id_usuario')->map(function($pedidosUsuario) {
            $usuario = $pedidosUsuario->first()->usuario;
            $totalGastado = $ventas->whereIn('id_pedido', $pedidosUsuario->pluck('id'))->sum('monto_total');
            return [
                'nombre' => $usuario ? $usuario->first_name . ' ' . $usuario->last_name : 'Usuario Desconocido',
                'email' => $usuario->email ?? 'N/A',
                'pedidos' => $pedidosUsuario->count(),
                'total' => $totalGastado
            ];
        })->sortByDesc('total')->take(5);
        
        // Ventas por día (para el gráfico)
        $ventasPorDia = $ventas->groupBy(function($venta) {
            return \Carbon\Carbon::parse($venta->fecha_venta)->format('Y-m-d');
        })->map(function($ventasDia) {
            return [
                'total' => $ventasDia->sum('monto_total'),
                'cantidad' => $ventasDia->count()
            ];
        })->sortKeys();

        // Crear gráfico filtrado con datos REALES
        $chartFiltrado = null;
        if ($ventas->count() > 0) {
            if (class_exists(\LaravelDaily\LaravelCharts\Classes\LaravelChart::class)) {
                // Determinar el período según el rango de días
                $groupPeriod = 'day';
                if ($diasDiferencia > 90) {
                    $groupPeriod = 'month';
                } elseif ($diasDiferencia > 30) {
                    $groupPeriod = 'week';
                }

                $chartFiltrado = new LaravelChart([
                    'chart_title' => 'Ventas Filtradas por Período',
                    'chart_type' => 'line',
                    'report_type' => 'group_by_date',
                    'model' => HistorialVenta::class,
                    'group_by_field' => 'fecha_venta',
                    'group_by_period' => $groupPeriod,
                    'chart_color' => '239, 68, 68',
                    'filter_field' => 'fecha_venta',
                    'filter_days' => (int)$diasDiferencia,
                    'extra_options' => [
                        'maintainAspectRatio' => false,
                        'responsive' => true
                    ]
                ]);
            }
        }

        $backUrl = route('admin.reports.index');

        return view('admin.reports.filtros', compact(
            'fechaInicio', 
            'fechaFin', 
            'totalVentas',
            'totalPedidos',
            'promedioVenta',
            'diasDiferencia',
            'chartFiltrado',
            'backUrl',
            'ventas',
            'pedidos',
            'pedidosPagados',
            'pedidosPendientes',
            'tasaConversion',
            'pedidosPorEstado',
            'topClientes',
            'ventasPorDia'
        ));
    }

    public function exportarPdf(Request $request)
    {
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }

        $tipoReporte = $request->input('tipo', 'general');
        $fechaInicio = $request->input('fecha_inicio') 
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : now()->subMonth()->startOfDay();
        $fechaFin = $request->input('fecha_fin') 
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : now()->endOfDay();

        // Obtener datos REALES filtrados por fecha
        $datos = $this->obtenerDatosReporte($tipoReporte, $fechaInicio, $fechaFin);

        try {
            Log::info('Export PDF request', [
                'tipo' => $tipoReporte,
                'fechaInicio' => $fechaInicio?->toDateTimeString(),
                'fechaFin' => $fechaFin?->toDateTimeString(),
                'ventas' => isset($datos['ventas']) ? count($datos['ventas']) : null,
                'productos' => isset($datos['productos']) ? count($datos['productos']) : null,
                'pedidos' => isset($datos['pedidos']) ? count($datos['pedidos']) : null,
                'user_id' => optional(auth()->user())->id,
                'ip' => $request->ip(),
            ]);
            $pdf = Pdf::loadView('admin.reports.pdf', [
                'datos' => $datos,
                'tipo' => $tipoReporte,
                'fecha_generacion' => now(),
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
            ])->setPaper('A4', 'portrait');

            return $pdf->download('reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Export PDF failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function exportarExcel(Request $request)
    {
        $tipo = $request->get('tipo', 'ventas');
        $fechaInicio = $request->get('fecha_inicio') 
            ? Carbon::parse($request->get('fecha_inicio'))->startOfDay()
            : now()->subMonth()->startOfDay();
        $fechaFin = $request->get('fecha_fin') 
            ? Carbon::parse($request->get('fecha_fin'))->endOfDay()
            : now()->endOfDay();

        $filename = "reporte_{$tipo}_" . now()->format('Y-m-d') . ".xlsx";

        try {
            return Excel::download(
                new ReportesExport($tipo, $fechaInicio, $fechaFin), 
                $filename
            );
        } catch (\Throwable $e) {
            Log::error('Error exportando Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ], 500);
        }
    }

    public function create()
    {
        if (!session()->has('_token')) {
            session()->regenerateToken();
        }

        return view('admin.reports.create');
    }

    public function estilizarVistaReportes()
    {
        $tiposReporte = [];

        try {
            if (HistorialVenta::count() > 0) {
                $tiposReporte['ventas'] = [
                    'nombre' => 'Reportes de Ventas',
                    'descripcion' => 'Análisis de ingresos y transacciones',
                    'icono' => 'dollar',
                    'color' => 'red',
                    'total' => HistorialVenta::count()
                ];
            }
        } catch (\Exception $e) {}

        try {
            if (Producto::count() > 0) {
                $tiposReporte['productos'] = [
                    'nombre' => 'Reportes de Productos',
                    'descripcion' => 'Inventario y rendimiento de productos',
                    'icono' => 'box',
                    'color' => 'blue',
                    'total' => Producto::count()
                ];
            }
        } catch (\Exception $e) {}

        try {
            if (User::count() > 0) {
                $tiposReporte['usuarios'] = [
                    'nombre' => 'Reportes de Usuarios',
                    'descripcion' => 'Estadísticas de usuarios registrados',
                    'icono' => 'users',
                    'color' => 'green',
                    'total' => User::count()
                ];
            }
        } catch (\Exception $e) {}

        try {
            if (Pedido::count() > 0) {
                $tiposReporte['pedidos'] = [
                    'nombre' => 'Reportes de Pedidos',
                    'descripcion' => 'Seguimiento de pedidos y estados',
                    'icono' => 'chart',
                    'color' => 'purple',
                    'total' => Pedido::count()
                ];
            }
        } catch (\Exception $e) {}

        return view('admin.reports.navegacion', compact('tiposReporte'));
    }

    private function obtenerDatosReporte($tipo, $fechaInicio, $fechaFin)
    {
        $fechaInicio = $fechaInicio instanceof Carbon ? $fechaInicio : Carbon::parse($fechaInicio);
        $fechaFin = $fechaFin instanceof Carbon ? $fechaFin : Carbon::parse($fechaFin);

        switch ($tipo) {
            case 'ventas':
                $ventas = HistorialVenta::with('pedido.usuario')
                    ->whereBetween('fecha_venta', [
                        $fechaInicio->startOfDay(), 
                        $fechaFin->endOfDay()
                    ])
                    ->orderBy('fecha_venta', 'desc')
                    ->get();

                return [
                    'titulo' => 'Reporte de Ventas',
                    'ventas' => $ventas,
                    'total' => $ventas->sum('monto_total'),
                    'cantidad' => $ventas->count()
                ];

            case 'productos':
                return [
                    'titulo' => 'Reporte de Productos',
                    'productos' => Producto::with(['category', 'theme'])->get(),
                    'total_productos' => Producto::count()
                ];

            case 'pedidos':
                $pedidos = Pedido::with('usuario')
                    ->whereBetween('created_at', [
                        $fechaInicio->startOfDay(), 
                        $fechaFin->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();

                return [
                    'titulo' => 'Reporte de Pedidos', 
                    'pedidos' => $pedidos,
                    'total_pedidos' => $pedidos->count()
                ];

            case 'usuarios':
                // Obtener usuarios con sus estadísticas de pedidos y ventas en el período
                $usuarios = User::with(['pedidos' => function($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()]);
                }])->get()->map(function($usuario) use ($fechaInicio, $fechaFin) {
                    $pedidos = $usuario->pedidos()
                        ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
                        ->get();
                    
                    $totalGastado = HistorialVenta::whereIn('id_pedido', $pedidos->pluck('id'))
                        ->sum('monto_total');
                    
                    $usuario->total_pedidos = $pedidos->count();
                    $usuario->total_gastado = $totalGastado;
                    return $usuario;
                })->filter(function($usuario) {
                    // Solo mostrar usuarios con al menos 1 pedido en el período
                    return $usuario->total_pedidos > 0;
                });

                return [
                    'titulo' => 'Reporte de Usuarios',
                    'usuarios' => $usuarios,
                    'total_usuarios' => $usuarios->count(),
                    'total_usuarios_sistema' => User::count()
                ];

            default:
                return [
                    'titulo' => 'Reporte General',
                    'resumen' => [
                        'total_ventas' => HistorialVenta::whereBetween('fecha_venta', [
                            $fechaInicio->startOfDay(), 
                            $fechaFin->endOfDay()
                        ])->sum('monto_total'),
                        'total_pedidos' => Pedido::whereBetween('created_at', [
                            $fechaInicio->startOfDay(), 
                            $fechaFin->endOfDay()
                        ])->count(),
                        'total_productos' => Producto::count(),
                        'usuarios_registrados' => User::count(),
                        'periodo' => $fechaInicio->format('d/m/Y') . ' - ' . $fechaFin->format('d/m/Y')
                    ]
                ];
        }
    }

    private function crearDatosPrueba($fechaInicio, $fechaFin)
    {
        // Asegurar que exista al menos un usuario para cumplir la FK de pedidos
        if (User::count() === 0) {
            User::create([
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo@example.com',
                'password' => 'demo1234', // se hashea por cast del modelo
                'id_rol' => 1,
            ]);
        }

        // Crear algunos pedidos si hay muy pocos (evita referenciar IDs inexistentes)
        if (Pedido::count() < 10) {
            $usuarios = User::pluck('id'); // Usar tabla de usuarios reales (ajustar si corresponde a 'usuarios')
            // Usar los estados normalizados del sistema para evitar violar el CHECK constraint
            $estadoPool = ['Procesando', 'Enviado', 'Entregado', 'Cancelado'];
            for ($i = Pedido::count(); $i < 10; $i++) {
                Pedido::create([
                    'id_usuario' => $usuarios->isNotEmpty() ? $usuarios->random() : User::value('id'),
                    'fecha_pedido' => now()->subDays(rand(1, 30)),
                    'estado' => $estadoPool[array_rand($estadoPool)],
                ]);
            }
        }

        // Obtener IDs de pedidos que aún NO tienen venta registrada (más eficiente que varios whereDoesntHave)
        $pedidoIdsSinVenta = Pedido::leftJoin('historial_ventas', 'historial_ventas.id_pedido', '=', 'pedidos.id')
            ->whereNull('historial_ventas.id_pedido')
            ->select('pedidos.id')
            ->pluck('id');

        foreach ($pedidoIdsSinVenta as $pid) {
            if (!HistorialVenta::where('id_pedido', $pid)->exists()) {
                HistorialVenta::create([
                    'id_pedido' => $pid,
                    'monto_total' => rand(50, 500) + (rand(0, 99) / 100),
                    'fecha_venta' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }

    private function buildChartSvg($data, $width = 520, $height = 200, $title = '')
    {
        $padLeft = 50;
        $padRight = 20;
        $padTop = 36;
        $padBottom = 56;
        $innerW = $width - $padLeft - $padRight;
        $innerH = $height - $padTop - $padBottom;

        $months = range(1, 12);
        $values = array_map(function ($m) use ($data) { return (int) $data->get($m, 0); }, $months);
        $max = max(1, max($values));

        $barW = intval($innerW / (count($months) * 1.6));
        $totalBarsW = $barW * count($months);
        $gap = intval(($innerW - $totalBarsW) / (count($months) - 1));

        $bars = '';
        $ticks = '';

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

            $bars .= "<rect x='" . ($x) . "' y='" . ($y) . "' width='{$barW}' height='{$barH}' fill='#ef4444' stroke='#b91c1c' stroke-width='1'/>";

            if ($val > 0) {
                $labelY = max($padTop + 12, $y - 8);
                $bars .= "<text x='" . ($x + intval($barW/2)) . "' y='" . ($labelY) . "' font-size='12' fill='#0f172a' font-weight='600' text-anchor='middle' font-family='Helvetica, Arial, sans-serif'>{$val}</text>";
            }

            $bars .= "<text x='" . ($x + intval($barW/2)) . "' y='" . ($padTop + $innerH + 20) . "' font-size='12' fill='#374151' text-anchor='middle' font-family='Helvetica, Arial, sans-serif'>{$monthLabel}</text>";
            $i++;
        }

        $titleText = $title ? "<text x='" . ($width/2) . "' y='18' font-size='16' fill='currentColor' text-anchor='middle' font-family='Helvetica, Arial, sans-serif' font-weight='600'>{$title}</text>" : '';

        $svg = "<svg width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' xmlns='http://www.w3.org/2000/svg'>" .
            "{$titleText}" .
            "<g>{$ticks}</g>" .
            "<g>{$bars}</g>" .
            "</svg>";

        return $svg;
    }

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
                return '';
            }

            public function renderChartJsLibrary()
            {
                return '';
            }
        };
    }

    private function buildSimpleTableHtml(string $title, array $headers, array $rows): string
    {
        $thead = implode('', array_map(fn($h)=>"<th class='px-3 py-2 text-left border-b'>".htmlspecialchars($h)."</th>", $headers));
        $tbody = '';
        foreach ($rows as $r) {
            $tbody .= '<tr>' . implode('', array_map(fn($c)=>"<td class='px-3 py-1 border-b'>".htmlspecialchars((string)$c)."</td>", $r)) . '</tr>';
        }
        return "<div class='w-full'><h4 class='text-sm font-semibold mb-2'>".htmlspecialchars($title)."</h4><div class='overflow-x-auto'><table class='min-w-full text-sm'><thead><tr>{$thead}</tr></thead><tbody>{$tbody}</tbody></table></div></div>";
    }

    // Métodos requeridos por Resource Controller
    public function store(Request $request)
    {
        return $this->exportarPdf($request);
    }

    public function show($id)
    {
        return redirect()->route('admin.reports.index');
    }

    public function edit($id)
    {
        return redirect()->route('admin.reports.index');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.reports.index');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.reports.index');
    }
}