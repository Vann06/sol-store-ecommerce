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
        'stripe_payment_intent_id',
        'payment_status',
        'payment_amount',
        'payment_currency',
        'payment_method',
        'paid_at',
    ];

    // Casteos
    protected $casts = [
        'fecha_pedido' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * El usuario que realizó el pedido
     */
    public function usuario()
    {
        // Alias legacy; apunta al modelo User real
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Usuario que creó este registro de pedido
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó este registro
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    /**
     * El usuario que realizó el pedido (usando modelo User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Acceso directo a la dirección (a través de envio)
     */
    public function direccion()
    {
        return $this->hasOneThrough(
            Direccion::class,
            Envio::class,
            'id_pedido',      // Foreign key on Envio
            'id',             // Foreign key on Direccion (local key referenced by Envio)
            'id',             // Local key on Pedido
            'id_direccion'    // Local key on Envio referencing Direccion
        );
    }

    /**
     * Relación con el historial de ventas (para reportes)
     */
    public function historialVenta()
    {
        return $this->hasOne(HistorialVenta::class, 'id_pedido');
    }

    /**
     * 🔐 Verificar si el pedido ya está pagado
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'succeeded' 
            || $this->payment_status === 'paid';
    }

    /**
     * 🔐 Marcar el pedido como pagado
     */
    public function markAsPaid(string $paymentIntentId, string $paymentMethod = 'card'): bool
    {
        $this->stripe_payment_intent_id = $paymentIntentId;
        $this->payment_status = 'succeeded';
        $this->payment_method = $paymentMethod;
        $this->paid_at = now();
        $this->estado = 'Procesando'; // Changed from 'pagado' to match DB constraint
        return $this->save();
    }

    /**
     * 🔐 Obtener el total del pedido sumando los detalles
     */
    public function getTotal(): float
    {
        return $this->detalles()
            ->selectRaw('SUM(cantidad * precio_unitario) as total')
            ->value('total') ?? 0.0;
    }
}
