<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';

    public $timestamps = false;

    protected $fillable = [
        'id_detalle_producto',
        'stock_actual',
        'cantidad_en_produccion',
        'fecha_actualizacion',
    ];

    /**
     * Relación con el detalle del producto.
     */
    public function detalleProducto()
    {
        return $this->belongsTo(DetalleProducto::class, 'id_detalle_producto');
    }
}
