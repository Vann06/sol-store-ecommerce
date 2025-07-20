<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    // Si tu tabla no sigue la convención plural de Eloquent, descomenta:
    // protected $table = 'materiales';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Atributos de producto que usan este material
     */
    public function atributosProducto()
    {
        return $this->hasMany(AtributoProducto::class, 'id_material');
    }
}
