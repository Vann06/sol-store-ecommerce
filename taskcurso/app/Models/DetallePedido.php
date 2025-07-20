<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'detalle_pedidos';

    // Asignación masiva
    protected $fillable = [
        'id_pedido',
        'id_detalle_producto',
        'cantidad',
        'precio_unitario',
    ];

    /**
     * Pedido al que pertenece este detalle
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    /**
     * Detalle de producto asociado a este pedido
     */
    public function detalleProducto()
    {
        return $this->belongsTo(DetalleProducto::class, 'id_detalle_producto');
    }
}
