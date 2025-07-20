<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'pagos';

    // Asignación masiva
    protected $fillable = [
        'id_pedido',
        'monto',
        'metodo_pago',
        'estado',
    ];

    /**
     * Pedido asociado a este pago (uno a uno)
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
