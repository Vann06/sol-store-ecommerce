<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    // Tabla explícita (plural en español)
    protected $table = 'direcciones';

    protected $fillable = [
        'id_usuario',
        'direccion',
        'id_municipio',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * El usuario al que pertenece esta dirección
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * El municipio al que pertenece esta dirección
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
}
