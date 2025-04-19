<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles'; 

    protected $fillable = [
        'nombre',
        'is_superadmin',
    ];

    /**
     * Relación con los usuarios que tienen este rol
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
