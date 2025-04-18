<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    // Si tu tabla no fuera "permisos", descomenta la siguiente línea:
    // protected $table = 'permisos';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Pivot roles ↔ módulos ↔ permisos
     */
    public function rolesModulosPermisos()
    {
        return $this->hasMany(RolesModulosPermiso::class, 'id_permiso');
    }

    /**
     * Los roles que tienen este permiso (vía roles_modulos_permisos)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'roles_modulos_permisos',
            'id_permiso',
            'id_rol'
        )->withTimestamps();
    }

    /**
     * Los módulos que incluyen este permiso (vía roles_modulos_permisos)
     */
    public function modulos()
    {
        return $this->belongsToMany(
            Modulo::class,
            'roles_modulos_permisos',
            'id_permiso',
            'id_modulo'
        )->withTimestamps();
    }
}
