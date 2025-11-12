<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $datos['titulo'] ?? 'Reporte' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 20px;
        }
        .title {
            color: #dc2626;
            font-size: 24px;
            margin: 0;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin: 10px 0;
        }
        .chart-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .data-section {
            margin: 30px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $datos['titulo'] ?? 'Reporte' }}</h1>
        <p class="subtitle">Generado el: {{ $fecha_generacion->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($chartPngBase64) && $chartPngBase64)
        <div class="chart-container">
            <h3>Gráfico de Datos</h3>
            <img src="data:image/png;base64,{{ $chartPngBase64 }}" style="max-width: 100%; max-height: 400px;" alt="Gráfico">
        </div>
    @elseif(isset($chartSvg) && $chartSvg)
        <div class="chart-container">
            <h3>Gráfico de Datos</h3>
            {!! $chartSvg !!}
        </div>
    @elseif(isset($chartImageUrl) && $chartImageUrl)
        <div class="chart-container">
            <h3>Gráfico de Datos</h3>
            <img src="{{ $chartImageUrl }}" style="max-width: 100%; max-height: 400px;" alt="Gráfico">
        </div>
    @endif

    <div class="data-section">
        @if($tipo === 'ventas' && isset($datos['ventas']))
            <h3>Datos de Ventas</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Monto Total</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['ventas'] as $venta)
                        <tr>
                            <td>{{ $venta->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                            <td>Q{{ number_format($venta->monto_total, 2) }}</td>
                            <td>
                                @if(isset($venta->pedido) && $venta->pedido && $venta->pedido->usuario)
                                    {{ $venta->pedido->usuario->first_name }} {{ $venta->pedido->usuario->last_name }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Total de Ventas: Q{{ number_format($datos['total'] ?? 0, 2) }}</strong>
            </div>
        @elseif($tipo === 'productos' && isset($datos['productos']))
            <h3>Datos de Productos</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['productos'] as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>Q{{ number_format($producto->precio_base ?? 0, 2) }}</td>
                            <td>{{ $producto->stock ?? 0 }}</td>
                            <td>{{ $producto->category->name ?? 'Sin categoría' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Total de Productos: {{ $datos['total_productos'] ?? 0 }}</strong>
            </div>
        @elseif($tipo === 'pedidos' && isset($datos['pedidos']))
            <h3>Datos de Pedidos</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th>Pago</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['pedidos'] as $pedido)
                        <tr>
                            <td>{{ $pedido->id }}</td>
                            <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($pedido->estado) }}</td>
                            <td>
                                @if($pedido->usuario)
                                    {{ $pedido->usuario->first_name }} {{ $pedido->usuario->last_name }}
                                @else
                                    Usuario #{{ $pedido->id_usuario }}
                                @endif
                            </td>
                            <td>
                                @if($pedido->payment_status === 'succeeded')
                                    <span style="color: #16a34a;">✓ Pagado</span>
                                @elseif($pedido->payment_status === 'pending')
                                    <span style="color: #eab308;">⏳ Pendiente</span>
                                @else
                                    <span style="color: #6b7280;">- N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($pedido->payment_amount)
                                    Q{{ number_format($pedido->payment_amount, 2) }}
                                @else
                                    Q{{ number_format($pedido->getTotal(), 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Total de Pedidos: {{ $datos['total_pedidos'] ?? 0 }}</strong><br>
                <strong>Total Pagados: {{ $datos['pedidos']->where('payment_status', 'succeeded')->count() }}</strong><br>
                <strong>Monto Total: Q{{ number_format($datos['pedidos']->sum('payment_amount'), 2) }}</strong>
            </div>
        @elseif($tipo === 'usuarios' && isset($datos['usuarios']))
            <h3>Datos de Usuarios</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Pedidos</th>
                        <th>Total Gastado</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['usuarios'] as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->first_name }} {{ $usuario->last_name }}</td>
                            <td style="font-size: 10px;">{{ $usuario->email }}</td>
                            <td style="text-align: center;">{{ $usuario->total_pedidos ?? 0 }}</td>
                            <td>Q{{ number_format($usuario->total_gastado ?? 0, 2) }}</td>
                            <td>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Usuarios con Pedidos en el Período: {{ $datos['total_usuarios'] ?? 0 }}</strong><br>
                <strong>Total Usuarios en el Sistema: {{ $datos['total_usuarios_sistema'] ?? 0 }}</strong><br>
                <strong>Gasto Total del Período: Q{{ number_format($datos['usuarios']->sum('total_gastado'), 2) }}</strong>
            </div>
        @elseif($tipo === 'general' && isset($datos['resumen']))
            <h3>Resumen General</h3>
            <div class="summary">
                @foreach($datos['resumen'] as $key => $value)
                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                @endforeach
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema</p>
    </div>
</body>
</html>