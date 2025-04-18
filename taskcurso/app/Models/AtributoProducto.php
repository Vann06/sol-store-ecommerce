<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoProducto extends Model
{
    use HasFactory;

    // Si tu tabla no siguiera la convención plural de Eloquent, descomenta:
    // protected $table = 'atributos_producto';

    protected $fillable = [
        'talla',
        'id_producto',
        'id_material',
    ];

    /**
     * El producto al que pertenece este atributo
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    /**
     * El material asociado a este atributo
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }
}
