<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    // Si la tabla no se llamara "modulos", descomenta la siguiente línea:
    // protected $table = 'modulos';

    // Campos que permiten asignación masiva
    protected $fillable = [
        'nombre',
    ];

    /**
     * Pivot roles ↔ módulos ↔ permisos
     */
    public function rolesModulosPermisos()
    {
        return $this->hasMany(RolesModulosPermiso::class, 'id_modulo');
    }

    /**
     * Los roles que incluyen este módulo (vía roles_modulos_permisos)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'roles_modulos_permisos',
            'id_modulo',
            'id_rol'
        )->withTimestamps();
    }

    /**
     * Los permisos asignados a este módulo (vía roles_modulos_permisos)
     */
    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'roles_modulos_permisos',
            'id_modulo',
            'id_permiso'
        )->withTimestamps();
    }
}
