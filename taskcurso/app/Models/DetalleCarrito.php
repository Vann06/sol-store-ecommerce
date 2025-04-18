<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{
    use HasFactory;

    protected $table = 'detalle_carrito';

    protected $fillable = [
        'id_carrito',
        'id_detalle_producto',
        'cantidad',
    ];

    /**
     * Relación con el carrito al que pertenece este detalle.
     */
    public function carrito()
    {
        return $this->belongsTo(CarritoCompra::class, 'id_carrito');
    }

    /**
     * Relación con el detalle del producto que se ha agregado.
     */
    public function detalleProducto()
    {
        return $this->belongsTo(DetalleProducto::class, 'id_detalle_producto');
    }
}
