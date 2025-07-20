<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'envios';

    // Asignación masiva
    protected $fillable = [
        'id_pedido',
        'id_direccion',
        'fecha_envio',
    ];

    // Casteo de fecha
    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    /**
     * Pedido asociado a este envío (uno a uno)
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    /**
     * Dirección de entrega asociada
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }
}
