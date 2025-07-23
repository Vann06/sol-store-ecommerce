<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialVenta extends Model
{
    use HasFactory;

    protected $table = 'historial_ventas';

    protected $fillable = [
        'id_pedido',
        'fecha_venta',
        'monto_total',
    ];

    /**
     * Relación con el pedido correspondiente a esta venta.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
