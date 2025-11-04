<?php

namespace App\Exports;

use App\Models\HistorialVenta;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ReportesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $tipo;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($tipo, $fechaInicio, $fechaFin)
    {
        $this->tipo = $tipo;
        $this->fechaInicio = $fechaInicio instanceof Carbon ? $fechaInicio : Carbon::parse($fechaInicio);
        $this->fechaFin = $fechaFin instanceof Carbon ? $fechaFin : Carbon::parse($fechaFin);
    }

    /**
     * Obtener la colección de datos filtrados por fecha
     */
    public function collection()
    {
        switch ($this->tipo) {
            case 'ventas':
                return HistorialVenta::whereBetween('fecha_venta', [
                    $this->fechaInicio->startOfDay(), 
                    $this->fechaFin->endOfDay()
                ])
                ->orderBy('fecha_venta', 'desc')
                ->get();
                
            case 'productos':
                return Producto::with('category')->get();
                
            case 'pedidos':
                return Pedido::whereBetween('created_at', [
                    $this->fechaInicio->startOfDay(), 
                    $this->fechaFin->endOfDay()
                ])
                ->with('usuario')
                ->orderBy('created_at', 'desc')
                ->get();
                
            case 'usuarios':
                return User::whereBetween('created_at', [
                    $this->fechaInicio->startOfDay(), 
                    $this->fechaFin->endOfDay()
                ])
                ->orderBy('created_at', 'desc')
                ->get();
                
            default:
                return collect([
                    (object)[
                        'metrica' => 'Total Ventas',
                        'valor' => 'Q' . number_format(HistorialVenta::whereBetween('fecha_venta', [
                            $this->fechaInicio->startOfDay(), 
                            $this->fechaFin->endOfDay()
                        ])->sum('monto_total'), 2)
                    ],
                    (object)[
                        'metrica' => 'Total Pedidos',
                        'valor' => Pedido::whereBetween('created_at', [
                            $this->fechaInicio->startOfDay(), 
                            $this->fechaFin->endOfDay()
                        ])->count()
                    ],
                    (object)[
                        'metrica' => 'Total Productos',
                        'valor' => Producto::count()
                    ],
                    (object)[
                        'metrica' => 'Período',
                        'valor' => $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y')
                    ]
                ]);
        }
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        switch ($this->tipo) {
            case 'ventas':
                return [
                    'ID Venta',
                    'ID Pedido',
                    'Fecha de Venta',
                    'Monto Total (Q)',
                    'Fecha Registro',
                    'Estado'
                ];
                
            case 'productos':
                return [
                    'ID',
                    'Nombre',
                    'Precio (Q)',
                    'Stock',
                    'Categoría',
                    'Estado'
                ];
                
            case 'pedidos':
                return [
                    'ID Pedido',
                    'Usuario',
                    'Email Usuario',
                    'Fecha Pedido',
                    'Estado',
                    'Total (Q)'
                ];
                
            case 'usuarios':
                return [
                    'ID',
                    'Nombre',
                    'Apellido',
                    'Email',
                    'Rol',
                    'Fecha Registro'
                ];
                
            default:
                return ['Métrica', 'Valor'];
        }
    }

    /**
     * Mapear los datos a las columnas
     */
    public function map($item): array
    {
        switch ($this->tipo) {
            case 'ventas':
                return [
                    $item->id,
                    $item->id_pedido ?? 'N/A',
                    $item->fecha_venta instanceof Carbon 
                        ? $item->fecha_venta->format('d/m/Y H:i') 
                        : Carbon::parse($item->fecha_venta)->format('d/m/Y H:i'),
                    number_format($item->monto_total, 2),
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A',
                    $item->estado ?? 'Completada'
                ];
                
            case 'productos':
                return [
                    $item->id,
                    $item->nombre ?? $item->titulo ?? 'Sin nombre',
                    number_format($item->precio, 2),
                    $item->stock ?? 'N/A',
                    $item->category->nombre ?? 'Sin categoría',
                    $item->estado ?? 'Activo'
                ];
                
            case 'pedidos':
                $usuario = $item->usuario;
                return [
                    $item->id,
                    $usuario ? ($usuario->first_name . ' ' . $usuario->last_name) : 'Usuario #' . ($item->id_usuario ?? 'N/A'),
                    $usuario->email ?? 'N/A',
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A',
                    ucfirst($item->estado ?? 'Pendiente'),
                    number_format($item->total ?? 0, 2)
                ];
                
            case 'usuarios':
                return [
                    $item->id,
                    $item->first_name ?? 'N/A',
                    $item->last_name ?? 'N/A',
                    $item->email,
                    $item->role ?? 'Usuario',
                    $item->created_at ? $item->created_at->format('d/m/Y') : 'N/A'
                ];
                
            default:
                return [
                    $item->metrica,
                    $item->valor
                ];
        }
    }

    /**
     * Estilos para las celdas
     */
    public function styles(Worksheet $sheet)
    {
        // Título del reporte en la fila 1
        $sheet->insertNewRowBefore(1, 3);
        
        $titulo = 'REPORTE DE ' . strtoupper($this->tipo);
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:F1');
        
        $sheet->setCellValue('A2', 'Período: ' . $this->fechaInicio->format('d/m/Y') . ' al ' . $this->fechaFin->format('d/m/Y'));
        $sheet->mergeCells('A2:F2');
        
        $sheet->setCellValue('A3', 'Generado: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('A3:F3');
        
        return [
            // Estilo del título
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Estilo del período
            2 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Estilo de fecha de generación
            3 => [
                'font' => ['italic' => true, 'size' => 9],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Estilo de los encabezados (fila 4)
            4 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B5563']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Estilo para todas las celdas
            'A:Z' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB']
                    ]
                ]
            ]
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return ucfirst($this->tipo);
    }
}