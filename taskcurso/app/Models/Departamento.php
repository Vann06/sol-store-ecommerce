<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    // Si la tabla no se llama "departamentos", especificar:
    // protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Municipios pertenecientes a este departamento
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_departamento');
    }
}
