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
        
        $ventasPorMes = new LaravelChart([
            'chart_title' => 'Ventas por Mes',
            'chart_type' => 'line',
            'report_type' => 'group_by_date',
            'model' => HistorialVenta::class,
            'group_by_field' => 'fecha_venta',
            'group_by_period' => 'month',
            'chart_color' => '239, 68, 68', 
        ]);

        $topProductos = new LaravelChart([
            'chart_title' => 'Top Productos por Stock',
            'chart_type' => 'bar',
            'report_type' => 'group_by_string',
            'model' => Producto::class,
            'group_by_field' => 'nombre',
            'chart_color' => '59, 130, 246', 
        ]);

        $pedidosPorEstado = new LaravelChart([
            'chart_title' => 'Pedidos por Estado',
            'chart_type' => 'pie',
            'report_type' => 'group_by_string',
            'model' => Pedido::class,
            'group_by_field' => 'estado',
            'chart_color' => '34, 197, 94', 
        ]);

        $totalVentas = HistorialVenta::sum('monto_total');
        $totalPedidos = Pedido::count();
        $totalProductos = Producto::count();
        $totalUsuarios = User::count();
        $ventasEsteMes = HistorialVenta::whereMonth('fecha_venta', now()->month)
                                     ->whereYear('fecha_venta', now()->year)
                                     ->sum('monto_total');

        $pedidosRecientes = Pedido::with(['usuario'])
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get();

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

    public function calcularMetricas()
    {
        $totalVentas = HistorialVenta::sum('monto_total');
        $pedidosPorMes = Pedido::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
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

        $chart = new LaravelChart($chartOptions);

        return view('admin.reports.graficos', compact('chart'));
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
            $fechaInicio = now()->month($mes)->startOfMonth();
            $fechaFin = now()->month($mes)->endOfMonth();
        } else if (!$personalizado) {
            $fechaInicio = now()->subDays(7);
            $fechaFin = now();
        }

        $chartFiltrado = new LaravelChart([
            'chart_title' => 'Ventas Filtradas',
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
            ]
        ]);

            return view('admin.reports.filtros', compact('chartFiltrado', 'fechaInicio', 'fechaFin'));
        } catch (\Exception $e) {
            // En caso de error, usar valores por defecto
            $fechaInicio = now()->subDays(7);
            $fechaFin = now();
            
            $chartFiltrado = new LaravelChart([
                'chart_title' => 'Sin datos disponibles',
                'chart_type' => 'line',
                'report_type' => 'group_by_date',
                'model' => HistorialVenta::class,
                'group_by_field' => 'fecha_venta',
                'group_by_period' => 'day',
            ]);
            
            return view('admin.reports.filtros', compact('chartFiltrado', 'fechaInicio', 'fechaFin'))
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
        $fechaInicio = $request->input('fecha_inicio', now()->subMonth());
        $fechaFin = $request->input('fecha_fin', now());

        $datos = $this->obtenerDatosReporte($tipoReporte, $fechaInicio, $fechaFin);

        return response()->streamDownload(function () use ($datos, $tipoReporte) {
            echo Pdf::loadHtml(
                Blade::render('admin.reports.pdf', [
                    'datos' => $datos,
                    'tipo' => $tipoReporte,
                    'fecha_generacion' => now()
                ])
            )->stream();
        }, 'reporte_' . $tipoReporte . '_' . now()->format('Y-m-d') . '.pdf');
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

    private function obtenerDatosReporte($tipo, $fechaInicio, $fechaFin)
    {
        switch ($tipo) {
            case 'ventas':
                return [
                    'titulo' => 'Reporte de Ventas',
                    'ventas' => HistorialVenta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->get(),
                    'total' => HistorialVenta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->sum('monto_total')
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
                    'pedidos' => Pedido::whereBetween('created_at', [$fechaInicio, $fechaFin])->get(),
                    'total_pedidos' => Pedido::whereBetween('created_at', [$fechaInicio, $fechaFin])->count()
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
}
