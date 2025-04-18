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
    ];

    /**
     * Usuario que posee el carrito
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Detalles (productos) en el carrito
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'id_carrito');
    }
}
