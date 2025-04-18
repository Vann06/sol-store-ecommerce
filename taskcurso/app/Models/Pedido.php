<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Indica que la tabla se llama "pedidos"
    protected $table = 'pedidos';

    // Asignación masiva
    protected $fillable = [
        'id_usuario',
        'fecha_pedido',
        'estado',
        'created_by',
        'updated_by',
    ];

    // Casteos
    protected $casts = [
        'fecha_pedido' => 'datetime',
    ];

    /**
     * El usuario que realizó el pedido
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Usuario que creó este registro de pedido
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    /**
     * Usuario que actualizó este registro
     */
    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }

    /**
     * Detalles asociados a este pedido
     */
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }

    /**
     * Pagos realizados para este pedido
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_pedido');
    }

    /**
     * Envío asociado a este pedido (uno a uno)
     */
    public function envio()
    {
        return $this->hasOne(Envio::class, 'id_pedido');
    }
}
