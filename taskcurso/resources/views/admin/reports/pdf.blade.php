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
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #374151;
            border-left: 4px solid #dc2626;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .total {
            background-color: #fef2f2;
            font-weight: bold;
        }
        .summary {
            background-color: #f0f9ff;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            margin: 20px 0;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .stats-number {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }
        .stats-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $datos['titulo'] ?? 'Reporte de Ventas' }}</h1>
        <p>Generado el: {{ $fecha_generacion->format('d/m/Y H:i:s') }}</p>
        <p>SOL Store - Sistema de Reportes</p>
    </div>

    @if($tipo === 'general' && isset($datos['resumen']))
        <div class="section">
            <h2>Resumen General</h2>
            <div class="stats-grid">
                <div class="stats-item">
                    <div class="stats-number">Q{{ number_format($datos['resumen']['total_ventas'] ?? 0, 2) }}</div>
                    <div class="stats-label">Total Ventas</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number">{{ $datos['resumen']['total_pedidos'] ?? 0 }}</div>
                    <div class="stats-label">Total Pedidos</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number">{{ $datos['resumen']['total_productos'] ?? 0 }}</div>
                    <div class="stats-label">Total Productos</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number">{{ $datos['resumen']['usuarios_registrados'] ?? 0 }}</div>
                    <div class="stats-label">Usuarios Registrados</div>
                </div>
            </div>
        </div>
    @endif

    @if($tipo === 'ventas' && isset($datos['ventas']))
        <div class="section">
            <h2>Detalle de Ventas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Venta</th>
                        <th>Monto Total</th>
                        <th>Pedido ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['ventas'] as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                        <td>Q{{ number_format($venta->monto_total, 2) }}</td>
                        <td>{{ $venta->id_pedido }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total">
                        <td colspan="2"><strong>Total General</strong></td>
                        <td><strong>Q{{ number_format($datos['total'] ?? 0, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    @if($tipo === 'productos' && isset($datos['productos']))
        <div class="section">
            <h2>Listado de Productos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Temática</th>
                        <th>Precio Base</th>
                        <th>Stock</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['productos'] as $producto)
                    <tr>
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->category->name ?? 'N/A' }}</td>
                        <td>{{ $producto->theme->name ?? 'N/A' }}</td>
                        <td>Q{{ number_format($producto->precio_base, 2) }}</td>
                        <td>{{ $producto->stock }}</td>
                        <td>{{ ucfirst($producto->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Total de productos: {{ $datos['total_productos'] ?? 0 }}</strong>
            </div>
        </div>
    @endif

    @if($tipo === 'pedidos' && isset($datos['pedidos']))
        <div class="section">
            <h2>Detalle de Pedidos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario ID</th>
                        <th>Fecha Pedido</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['pedidos'] as $pedido)
                    <tr>
                        <td>{{ $pedido->id }}</td>
                        <td>{{ $pedido->id_usuario }}</td>
                        <td>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ ucfirst($pedido->estado) }}</td>
                        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary">
                <strong>Total de pedidos: {{ $datos['total_pedidos'] ?? 0 }}</strong>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>SOL Store © {{ date('Y') }} - Reporte generado automáticamente</p>
        <p>Este documento contiene información confidencial de la empresa</p>
    </div>
</body>
</html>
