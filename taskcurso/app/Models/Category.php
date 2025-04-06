<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Opcional si tu tabla se llama exactamente "categories"
    protected $table = 'categories';

    protected $fillable = ['name'];

    // Relación con productos (si la usas)
    public function products()
    {
        return $this->hasMany(Product::class, 'id_categoria');
    }
}
