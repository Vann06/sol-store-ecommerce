<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    // Si la tabla no se llama "municipios", especificar:
    // protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'codigo_postal',
        'id_departamento',
    ];

    /**
     * Departamento al que pertenece este municipio
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    /**
     * Direcciones dentro de este municipio
     */
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'id_municipio');
    }
}
