<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    // Opcional si tu tabla se llama exactamente "themes"
    // protected $table = 'themes';

    protected $fillable = ['name'];

    // Relación con productos (si la usas)
    public function products()
    {
        return $this->hasMany(Product::class, 'id_tematica');
    }
}
