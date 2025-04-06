<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'productos'; 

    protected $fillable = [
        'nombre',
        'id_categoria',
        'id_tematica',
        'descripcion',
        'precio_base',
        'imagen',
        'status',
        'stock',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_categoria');
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'id_tematica');
    }
}
