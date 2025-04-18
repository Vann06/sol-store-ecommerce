<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioRole extends Model
{
    use HasFactory;

    // Si tu tabla no sigue convenciones de Eloquent, especifícala:
    protected $table = 'usuarios_roles';

    // Asignación masiva
    protected $fillable = [
        'id_usuario',
        'id_rol',
    ];

    /**
     * El usuario de este pivot
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * El rol de este pivot
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }
}
