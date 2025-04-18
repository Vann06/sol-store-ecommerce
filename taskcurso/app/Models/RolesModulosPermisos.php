<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesModulosPermiso extends Model
{
    use HasFactory;

    // Si tu tabla no fuera "roles_modulos_permisos", descomenta:
    // protected $table = 'roles_modulos_permisos';

    protected $fillable = [
        'id_rol',
        'id_modulo',
        'id_permiso',
    ];

    /**
     * El rol al que pertenece esta asignación
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    /**
     * El módulo al que pertenece esta asignación
     */
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }

    /**
     * El permiso al que pertenece esta asignación
     */
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }
}
