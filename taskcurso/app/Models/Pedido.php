<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'stripe_payment_intent_id',
        'payment_status',
        'payment_amount',
        'payment_currency',
        'payment_method',
        'paid_at',
        'created_by',
        'updated_by',
    ];

    // Casteos
    protected $casts = [
        'fecha_pedido' => 'datetime',
        'paid_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * El usuario que realizó el pedido (alias de user para compatibilidad)
     * @deprecated Usar user() en su lugar
     */
    public function usuario()
    {
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
     * Verificar si el pedido está pagado
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'succeeded' && !empty($this->stripe_payment_intent_id);
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPending(): bool
    {
        return in_array($this->payment_status, ['pending', 'processing']);
    }

    /**
     * Verificar si el pago falló
     */
    public function isPaymentFailed(): bool
    {
        return in_array($this->payment_status, ['failed', 'canceled']);
    }

    /**
     * Marcar el pago como exitoso
     * 
     * ⚠️ IMPORTANTE: Solo llamar este método después de verificar el pago en Stripe
     */
    public function markAsPaid(string $paymentIntentId, string $paymentMethod): void
    {
        // ✅ VALIDACIÓN: Verificar que no esté ya pagado
        if ($this->isPaid()) {
            throw new \Exception('Este pedido ya ha sido marcado como pagado anteriormente');
        }
        
        // ✅ VALIDACIÓN: Verificar que el paymentIntentId corresponda al de este pedido
        if ($this->stripe_payment_intent_id && $this->stripe_payment_intent_id !== $paymentIntentId) {
            throw new \Exception('El payment_intent_id no corresponde a este pedido');
        }
        
        $this->update([
            'stripe_payment_intent_id' => $paymentIntentId,
            'payment_status' => 'succeeded',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);
    }

    /**
     * Marcar el pago como fallido
     */
    public function markAsPaymentFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }
}
