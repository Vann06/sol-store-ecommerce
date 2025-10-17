# 🎯 GUÍA RÁPIDA: Importar Excel a Base de Datos

## ⚡ Quick Start (3 pasos)

```bash
# 1. Ir a la carpeta del proyecto
cd taskcurso

# 2. Importar todos los datos
php artisan db:seed --class=ExcelImportSeeder

# 3. Verificar que todo se importó bien
php artisan import:verify
```

¡Eso es todo! Los archivos de ejemplo ya están listos en `storage/app/imports/`

---

## 📂 Archivos Incluidos

Ya tienes listos estos archivos de ejemplo:

```
storage/app/imports/
├── 1_categories.csv        (7 categorías)
├── 2_themes.csv           (8 temáticas)
├── 3_materiales.csv       (10 materiales)
├── 4_usuarios.csv         (5 usuarios)
└── 5_productos.csv        (20 productos completos)
```

Todos los usuarios tienen la contraseña: `password123`

---

## 🛠️ Si Quieres Usar Tus Propios Datos

### Paso 1: Preparar Excel

Usa las plantillas en **`PLANTILLAS_EXCEL.md`** o crea tu propio Excel con esta estructura:

#### **IMPORTANTE: Orden de las tablas**
```
1. Categories (primero)
2. Themes (segundo)
3. Materiales (tercero)
4. Usuarios (cuarto - opcional)
5. Productos (último - depende de los anteriores)
```

### Paso 2: Estructura de cada tabla

#### 🏷️ Categories
```csv
name,imagen
Ropa,https://...
Accesorios,https://...
```

#### 🎨 Themes
```csv
name,imagen
Videojuegos,https://...
Anime,https://...
```

#### 🧱 Materiales
```csv
nombre
Algodón
Poliéster
```

#### 👤 Usuarios (opcional)
```csv
first_name,last_name,email,password
Juan,Pérez,juan@email.com,$2y$12$LQv3c1yq...
```

#### 📦 Productos (el más importante)
```csv
nombre,categoria,tematica,descripcion,precio_base,stock,imagen,status
Camiseta Gamer,Ropa,Videojuegos,Descripción...,29990,50,https://...,activo
```

**⚠️ CRÍTICO:**
- `categoria` debe coincidir EXACTAMENTE con un `name` de la tabla categories
- `tematica` debe coincidir EXACTAMENTE con un `name` de la tabla themes
- `precio_base` es un número sin símbolos: `29990` (no `$29.990`)
- `status` solo puede ser: `activo` o `inactivo`

### Paso 3: Exportar a CSV

En Excel:
1. **Archivo → Guardar como**
2. Formato: **CSV UTF-8 (delimitado por comas)**
3. Nombre: `1_categories.csv`, `2_themes.csv`, etc.

### Paso 4: Colocar archivos

```bash
# Copiar a la carpeta de importación
cp 1_categories.csv taskcurso/storage/app/imports/
cp 2_themes.csv taskcurso/storage/app/imports/
cp 3_materiales.csv taskcurso/storage/app/imports/
cp 4_usuarios.csv taskcurso/storage/app/imports/
cp 5_productos.csv taskcurso/storage/app/imports/
```

### Paso 5: Importar

```bash
cd taskcurso
php artisan db:seed --class=ExcelImportSeeder
```

### Paso 6: Verificar

```bash
php artisan import:verify
```

---

## 🔥 Comandos Útiles

### Ver datos importados
```bash
php artisan tinker

# Contar registros
>>> DB::table('productos')->count()

# Ver primeros 5 productos
>>> DB::table('productos')->limit(5)->get()

# Ver todas las categorías
>>> DB::table('categories')->pluck('name')

# Buscar un producto específico
>>> DB::table('productos')->where('nombre', 'like', '%Gamer%')->get()
```

### Limpiar y volver a importar
```bash
php artisan tinker

# CUIDADO: Esto borra TODOS los datos
>>> DB::table('productos')->truncate();
>>> DB::table('categories')->truncate();
>>> DB::table('themes')->truncate();
>>> DB::table('materiales')->truncate();
>>> exit

# Importar de nuevo
php artisan db:seed --class=ExcelImportSeeder
```

### Importar solo una tabla específica

Edita `database/seeders/ExcelImportSeeder.php`:

```php
// Línea ~75
$toImport = [
    // 'categories',
    // 'themes',
    // 'materiales',
    // 'usuarios',
    'productos',  // Solo importar productos
];
```

Luego ejecuta:
```bash
php artisan db:seed --class=ExcelImportSeeder
```

### Ver preview antes de importar
```bash
php artisan import:csv storage/app/imports/5_productos.csv productos --dry-run
```

---

## ❌ Errores Comunes

### "Foreign key constraint fails"
**Problema:** Intentas importar productos antes que categorías/temáticas.

**Solución:**
```bash
# Importar en orden correcto
php artisan db:seed --class=ExcelImportSeeder
```

### "Duplicate entry for key 'name'"
**Problema:** Ya existe una categoría/temática con ese nombre.

**Solución:**
```bash
# Opción 1: Limpiar y volver a importar
php artisan tinker
>>> DB::table('categories')->truncate();

# Opción 2: Cambiar el nombre en tu CSV
```

### Caracteres raros (ñ, tildes)
**Problema:** El CSV no está en UTF-8.

**Solución:** Guardar como **CSV UTF-8** en Excel.

### Categoría no encontrada
**Problema:** El nombre en productos no coincide exactamente.

**Solución:**
```bash
# Ver categorías disponibles
php artisan tinker
>>> DB::table('categories')->pluck('name')

# Asegúrate que el nombre coincida EXACTAMENTE
# ✅ "Ropa"
# ❌ "ropa", "ROPA", "Ropa " (con espacio)
```

---

## 📚 Documentación Completa

- **`ESTRUCTURA_EXCEL_COMPLETA.md`** - Estructura detallada de cada tabla
- **`PLANTILLAS_EXCEL.md`** - Plantillas listas para copiar/pegar
- **`IMPORTAR_EXCEL_QUICKSTART.md`** - Guía de inicio rápido
- **`IMPORTAR_EXCEL.md`** - Documentación completa con ejemplos avanzados

---

## ✅ Checklist

Antes de importar, verifica:

- [ ] Los archivos están en `storage/app/imports/`
- [ ] Los nombres de archivo son correctos: `1_categories.csv`, etc.
- [ ] Los archivos son CSV UTF-8 (no Excel .xlsx)
- [ ] Los encabezados coinciden exactamente
- [ ] Las categorías y temáticas existen antes de importar productos
- [ ] Los precios son números sin símbolos
- [ ] El status es `activo` o `inactivo`

---

## 🎁 Datos de Ejemplo

Los archivos de ejemplo incluyen:

### Categorías (7)
- Ropa, Accesorios, Juguetes, Hogar y Decoración, Tecnología, Papelería, Coleccionables

### Temáticas (8)
- Videojuegos, Anime, Películas, Series de TV, Cómics, Música, Deportes, Fantasía

### Productos (20)
- Camiseta Gamer Level Up
- Taza Star Wars Darth Vader
- Figura Naruto Uzumaki
- Poster Marvel Avengers
- Gorra Pokémon Pikachu
- Llavero Dragon Ball
- Sudadera Zelda Triforce
- Libreta Harry Potter Hogwarts
- Mouse Pad Gamer RGB
- Peluche Pikachu Grande
- Funko Pop Batman
- Mochila Anime Naruto
- Vaso Térmico Star Wars
- Calendario Anime 2025
- Alfombrilla Yoga Gaming
- Auriculares Gamer RGB
- Teclado Mecánico RGB
- Polera Anime One Piece
- Lampara LED Pokeball
- Cuaderno Death Note

Todos con:
- ✅ Descripciones completas
- ✅ Precios realistas (5.990 - 79.990)
- ✅ Stock variado (20-200 unidades)
- ✅ URLs de imágenes
- ✅ Categorías y temáticas válidas

---

## 🚀 Empezar Ahora

```bash
cd taskcurso
php artisan db:seed --class=ExcelImportSeeder
php artisan import:verify
```

¡Listo! Tu base de datos está poblada. 🎉

---

## 📞 Ayuda

Si tienes problemas:

1. Revisa `storage/logs/laravel.log`
2. Ejecuta `php artisan import:verify`
3. Revisa que tus CSV tengan encoding UTF-8
4. Verifica que las relaciones (categorías/temáticas) existan primero

**Comandos de diagnóstico:**
```bash
# Ver estructura de la tabla
php artisan tinker
>>> DB::select('DESCRIBE productos')

# Ver si hay categorías
>>> DB::table('categories')->get()

# Ver último error
>>> tail -20 storage/logs/laravel.log
```
