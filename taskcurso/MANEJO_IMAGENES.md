# 🖼️ Guía de Manejo de Imágenes de Productos

## 🎯 Sistema de Imagen por Defecto

Tu aplicación ahora maneja automáticamente productos sin imagen usando un placeholder "Imagen no disponible".

---

## ✅ ¿Cómo Funciona?

### 1. En el CSV
Simplemente **deja vacío** el campo `imagen`:

```csv
nombre,categoria,tematica,descripcion,precio_base,stock,imagen,status
Camiseta Gamer,Ropa,Videojuegos,Descripción...,29990,50,,activo
Taza Star Wars,Hogar,Películas,Descripción...,15990,100,,activo
```

### 2. En el Código
El modelo `Producto` detecta automáticamente si hay imagen o no:

```php
// En tu blade/vue
<img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">

// Devuelve automáticamente:
// - La imagen real si existe
// - /images/no-image.svg si no hay imagen
```

---

## 🎨 Tres Formas de Usar Imágenes

### Opción 1: Sin Imagen (Placeholder Automático) ✅ RECOMENDADO

**En el CSV:**
```csv
nombre,imagen,status
Producto sin foto,,activo
```

**Resultado:** Se muestra `/images/no-image.svg`

---

### Opción 2: Con URL Externa

**En el CSV:**
```csv
nombre,imagen,status
Producto con URL,https://micdn.com/producto.jpg,activo
```

**Resultado:** Se muestra la imagen de la URL

---

### Opción 3: Con Ruta Local (para subir después)

**En el CSV:**
```csv
nombre,imagen,status
Producto local,productos/camiseta-gamer.jpg,activo
```

**Resultado:** Se busca en `/storage/productos/camiseta-gamer.jpg`

---

## 📊 En tu Frontend (Vue/Blade)

### Ejemplo en Vue:
```vue
<template>
  <div class="producto-card">
    <img 
      :src="producto.imagen_url" 
      :alt="producto.nombre"
      @error="handleImageError"
      class="producto-imagen"
    >
  </div>
</template>

<script>
export default {
  methods: {
    handleImageError(event) {
      // Fallback adicional si la imagen por defecto también falla
      event.target.src = '/images/no-image.svg';
    }
  }
}
</script>
```

### Ejemplo en Blade:
```blade
<div class="producto-card">
    <img 
        src="{{ $producto->imagen_url }}" 
        alt="{{ $producto->nombre }}"
        onerror="this.src='/images/no-image.svg'"
        class="producto-imagen"
    >
    
    @if($producto->hasImage())
        <span class="badge">Con imagen</span>
    @else
        <span class="badge badge-warning">Pendiente imagen</span>
    @endif
</div>
```

---

## 🔧 API JSON Response

Cuando devuelvas productos en tu API:

```php
// ProductoController.php
public function index()
{
    $productos = Producto::with(['category', 'theme'])->get();
    
    return response()->json([
        'productos' => $productos->map(function($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_base,
                'imagen_url' => $producto->imagen_url, // 👈 Usa este
                'tiene_imagen' => $producto->hasImage(),
            ];
        })
    ]);
}
```

**Respuesta JSON:**
```json
{
  "productos": [
    {
      "id": 1,
      "nombre": "Camiseta Gamer",
      "precio": "29990.00",
      "imagen_url": "http://localhost/images/no-image.svg",
      "tiene_imagen": false
    },
    {
      "id": 2,
      "nombre": "Taza Star Wars",
      "precio": "15990.00",
      "imagen_url": "https://cdn.mitienda.com/taza.jpg",
      "tiene_imagen": true
    }
  ]
}
```

---

## 📤 Subir Imágenes Después

### Opción 1: Actualizar en la Base de Datos

```php
// Subir archivo
$path = $request->file('imagen')->store('productos', 'public');

// Actualizar producto
$producto = Producto::find($id);
$producto->imagen = $path;
$producto->save();

// Ahora $producto->imagen_url devolverá la nueva imagen
```

### Opción 2: Actualizar vía API

```javascript
// Frontend
const formData = new FormData();
formData.append('imagen', file);

await axios.post(`/api/productos/${id}/imagen`, formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
});
```

```php
// Backend
public function updateImagen(Request $request, $id)
{
    $request->validate([
        'imagen' => 'required|image|max:2048'
    ]);
    
    $producto = Producto::findOrFail($id);
    
    // Eliminar imagen anterior si existe
    if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
        Storage::disk('public')->delete($producto->imagen);
    }
    
    // Guardar nueva imagen
    $path = $request->file('imagen')->store('productos', 'public');
    $producto->imagen = $path;
    $producto->save();
    
    return response()->json([
        'message' => 'Imagen actualizada',
        'imagen_url' => $producto->imagen_url
    ]);
}
```

---

## 🎨 Personalizar la Imagen por Defecto

### Cambiar el SVG por una imagen PNG/JPG

1. **Coloca tu imagen en:** `public/images/no-image.png`

2. **Modifica el modelo** `Producto.php`:
```php
public function getImagenUrlAttribute()
{
    if (!empty($this->imagen)) {
        if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
            return $this->imagen;
        }
        return asset('storage/' . $this->imagen);
    }
    
    // Cambiar a PNG
    return asset('images/no-image.png');
}
```

### Usar Placeholder de Internet (Temporal)

```php
public function getImagenUrlAttribute()
{
    if (!empty($this->imagen)) {
        // ... código existente
    }
    
    // Placeholder de https://placehold.co/
    return 'https://placehold.co/400x400/EFEFEF/999999?text=Sin+Imagen';
}
```

---

## ✅ Ventajas de Este Sistema

### ✅ Sin Errores de Imagen Rota
- Siempre muestra algo, nunca un cuadro roto

### ✅ Importación Más Rápida
- No necesitas buscar imágenes antes de importar
- Importa productos ahora, agrega imágenes después

### ✅ Experiencia de Usuario
- El usuario ve un placeholder profesional
- Sabe que el producto existe pero falta la imagen

### ✅ SEO Friendly
- Evita errores 404 en imágenes
- Mejor para rastreo de motores de búsqueda

---

## 📋 Checklist para Importar sin Imágenes

- [ ] CSV tiene columna `imagen` pero vacía
- [ ] Archivo `public/images/no-image.svg` existe
- [ ] Modelo `Producto.php` tiene accessor `getImagenUrlAttribute()`
- [ ] Frontend usa `$producto->imagen_url` o `producto.imagen_url`
- [ ] Manejo de errores con `onerror` o `@error`

---

## 🚀 Proceso Completo

### 1. Importar productos sin imagen
```bash
cd taskcurso
php artisan db:seed --class=ExcelImportSeeder
```

### 2. Ver en frontend
```
✅ Todos los productos muestran placeholder "Imagen no disponible"
```

### 3. Subir imágenes después (una por una o batch)
```php
// Actualizar imagen de un producto
$producto = Producto::find(1);
$producto->imagen = 'productos/camiseta-gamer.jpg';
$producto->save();
```

### 4. Resultado final
```
✅ Productos con imagen → muestran su imagen
✅ Productos sin imagen → muestran placeholder
```

---

## 💡 Tips Pro

### 1. Agregar indicador visual
```vue
<div class="producto-card" :class="{ 'sin-imagen': !producto.tiene_imagen }">
  <img :src="producto.imagen_url" :alt="producto.nombre">
  <span v-if="!producto.tiene_imagen" class="badge">Pendiente imagen</span>
</div>
```

### 2. Filtrar productos sin imagen
```php
// Obtener productos que necesitan imagen
$productosSinImagen = Producto::whereNull('imagen')
    ->orWhere('imagen', '')
    ->get();
```

### 3. Dashboard de estadísticas
```php
$stats = [
    'total' => Producto::count(),
    'con_imagen' => Producto::whereNotNull('imagen')
        ->where('imagen', '!=', '')
        ->count(),
    'sin_imagen' => Producto::whereNull('imagen')
        ->orWhere('imagen', '')
        ->count(),
];

// Resultado: 
// Total: 20
// Con imagen: 5
// Sin imagen: 15 (75% pendientes)
```

---

## 🎯 Resumen

**Para importar sin imágenes:**

1. **Deja el campo `imagen` vacío en el CSV:**
   ```csv
   nombre,imagen,status
   Mi Producto,,activo
   ```

2. **El sistema automáticamente:**
   - Detecta que no hay imagen
   - Muestra `/images/no-image.svg`
   - Funciona en API, frontend, y vistas

3. **Agrega imágenes cuando quieras:**
   - Actualiza el campo `imagen` en la BD
   - Sube archivos vía UI
   - Actualiza vía API

¡Listo! 🎉
