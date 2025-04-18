<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleProducto extends Model
{
    use HasFactory;

    // Si tu tabla no siguiera la convención plural de Eloquent:
    protected $table = 'detalle_producto';

    // Campos que pueden asignarse masivamente
    protected $fillable = [
        'id_producto',
        'stock',
        'precio',
        'created_by',
    ];

    /**
     * El producto al que pertenece este detalle
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    /**
     * Usuario que creó este registro de detalle
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}
