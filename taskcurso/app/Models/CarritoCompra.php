<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoCompra extends Model
{
    use HasFactory;

    protected $table = 'carrito_compras';

    protected $fillable = [
        'id_usuario',
        'session_id',
    ];

    /**
     * Usuario que posee el carrito (puede ser null para invitados)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Detalles (productos) en el carrito
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'id_carrito');
    }

    /**
     * Obtener carrito por usuario o sesión
     */
    public static function obtenerCarrito($usuario_id = null, $session_id = null)
    {
        if ($usuario_id) {
            // Usuario autenticado
            return static::where('id_usuario', $usuario_id)->first();
        } elseif ($session_id) {
            // Usuario invitado
            return static::where('session_id', $session_id)
                ->whereNull('id_usuario')
                ->first();
        }
        
        return null;
    }

    /**
     * Crear o obtener carrito
     */
    public static function crearOObtenerCarrito($usuario_id = null, $session_id = null)
    {
        if ($usuario_id) {
            // Usuario autenticado
            return static::firstOrCreate(['id_usuario' => $usuario_id]);
        } elseif ($session_id) {
            // Usuario invitado
            return static::firstOrCreate([
                'session_id' => $session_id,
                'id_usuario' => null
            ]);
        }
        
        throw new \Exception('Se requiere usuario_id o session_id');
    }

    /**
     * Transferir carrito de invitado a usuario autenticado
     */
    public static function transferirCarritoInvitado($session_id, $usuario_id)
    {
        $carritoInvitado = static::where('session_id', $session_id)
            ->whereNull('id_usuario')
            ->first();
            
        if (!$carritoInvitado) {
            return null;
        }

        // Verificar si el usuario ya tiene un carrito
        $carritoUsuario = static::where('id_usuario', $usuario_id)->first();
        
        if ($carritoUsuario) {
            // Mover items del carrito invitado al carrito del usuario
            $carritoInvitado->detalles()->update(['id_carrito' => $carritoUsuario->id]);
            $carritoInvitado->delete();
            return $carritoUsuario;
        } else {
            // Convertir carrito invitado en carrito de usuario
            $carritoInvitado->update([
                'id_usuario' => $usuario_id,
                'session_id' => null
            ]);
            return $carritoInvitado;
        }
    }
}
