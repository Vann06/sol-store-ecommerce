<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Opcional si tu tabla se llama exactamente "categories"
    protected $table = 'categories';

    protected $fillable = ['name', 'imagen'];	

    // Relación con productos (si la usas)
    public function products()
    {
        return $this->hasMany(Product::class, 'id_categoria');
    }

    // Relación con productos usando el nombre correcto para los tests
    public function productos()
    {
        return $this->hasMany(\App\Models\Producto::class, 'id_categoria');
    }
}
