<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Usuario;
use App\Models\Category;
use App\Models\Theme;
use App\Models\DetalleProducto;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    // Si tu tabla se llama 'productos' (no el plural inglés), explícitalo:
    protected $table = 'productos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'id_categoria',
        'id_tematica',
        'descripcion',
        'precio_base',
        'stock',
        'imagen',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Casts para tipos de datos
    protected $casts = [
        'precio_base'   => 'decimal:2',
        'stock'         => 'integer',
        'fecha_registro'=> 'datetime',
    ];

    /**
     * Relación con categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_categoria');
    }

    /**
     * Relación con  (theme)
     */
    public function theme()
    {
        return $this->belongsTo(Theme::class, 'id_tematica');
    }

    /**
     * Usuario que creó este producto
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    /**
     * Usuario que actualizó este producto
     */
    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }

    /**
     * Usuario que eliminó este producto (soft delete)
     */
    public function eliminador()
    {
        return $this->belongsTo(Usuario::class, 'deleted_by');
    }

    /**
     * Detalles de inventario / stock histórico (si los tienes)
     */
    public function detalleProducto()
    {
        return $this->hasMany(DetalleProducto::class, 'id_producto');
    }

    /**
     * Accessor para obtener la URL de la imagen
     * Si no hay imagen, devuelve una imagen por defecto
     */
    public function getImagenUrlAttribute()
    {
        // Si tiene imagen y no está vacía
        if (!empty($this->imagen)) {
            // Si es una URL completa, la devuelve tal cual
            if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
                return $this->imagen;
            }
            // Si es una ruta relativa, la convierte a URL completa
            return asset('storage/' . $this->imagen);
        }
        
        // Imagen por defecto
        return asset('images/no-image.svg');
    }

    /**
     * Accessor alternativo para compatibilidad
     */
    public function getImagenAttribute($value)
    {
        // Retorna el valor original de la DB
        return $value;
    }

    /**
     * Verifica si el producto tiene imagen
     */
    public function hasImage()
    {
        return !empty($this->imagen) && $this->imagen !== null;
    }
}

