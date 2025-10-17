# 📊 Ejemplos de Excel para Base de Datos Sol Store

Este documento explica la estructura exacta de cada tabla y cómo preparar los archivos Excel/CSV para importarlos.

---

## 🎯 Orden de Importación (IMPORTANTE)

**DEBES importar en este orden debido a las dependencias entre tablas:**

```
1. Categories (categorías)        ← Primero
2. Themes (temáticas)            ← Segundo
3. Materiales                     ← Tercero
4. Usuarios (opcional)            ← Cuarto
5. Productos                      ← Quinto (depende de categories y themes)
```

---

## 📋 Estructura de Tablas y Ejemplos

### 1️⃣ Categories (Categorías)

**Tabla:** `categories`  
**Archivo:** `1_categories.csv`

#### Campos:
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
|-------|------|-------------|-------------|---------|
| name | string | ✅ Sí | Nombre único de la categoría | `Ropa` |
| imagen | string | ❌ No | URL de la imagen | `https://example.com/ropa.jpg` |

#### Excel/CSV de ejemplo:
```csv
name,imagen
Ropa,https://example.com/images/ropa.jpg
Accesorios,https://example.com/images/accesorios.jpg
Juguetes,https://example.com/images/juguetes.jpg
Hogar y Decoración,https://example.com/images/hogar.jpg
Tecnología,https://example.com/images/tecnologia.jpg
Papelería,https://example.com/images/papeleria.jpg
Coleccionables,https://example.com/images/coleccionables.jpg
```

#### ⚠️ Validaciones:
- `name` debe ser único (no puede repetirse)
- Si no tienes imagen, deja la columna vacía pero mantén la coma: `Ropa,`

---

### 2️⃣ Themes (Temáticas)

**Tabla:** `themes`  
**Archivo:** `2_themes.csv`

#### Campos:
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
|-------|------|-------------|-------------|---------|
| name | string | ✅ Sí | Nombre único de la temática | `Videojuegos` |
| imagen | string | ❌ No | URL de la imagen | `https://example.com/videojuegos.jpg` |

#### Excel/CSV de ejemplo:
```csv
name,imagen
Videojuegos,https://example.com/images/videojuegos.jpg
Anime,https://example.com/images/anime.jpg
Películas,https://example.com/images/peliculas.jpg
Series de TV,https://example.com/images/series.jpg
Cómics,https://example.com/images/comics.jpg
Música,https://example.com/images/musica.jpg
Deportes,https://example.com/images/deportes.jpg
Fantasía,https://example.com/images/fantasia.jpg
```

#### ⚠️ Validaciones:
- `name` debe ser único
- Similar a categories

---

### 3️⃣ Materiales

**Tabla:** `materiales`  
**Archivo:** `3_materiales.csv`

#### Campos:
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
|-------|------|-------------|-------------|---------|
| nombre | string | ✅ Sí | Nombre único del material | `Algodón` |

#### Excel/CSV de ejemplo:
```csv
nombre
Algodón
Poliéster
Cerámica
Plástico
Metal
Vinilo
Tela
Acrílico
Madera
Silicona
```

#### ⚠️ Validaciones:
- `nombre` debe ser único
- Tabla muy simple, solo un campo

---

### 4️⃣ Usuarios

**Tabla:** `usuarios`  
**Archivo:** `4_usuarios.csv`

#### Campos:
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
|-------|------|-------------|-------------|---------|
| first_name | string(100) | ✅ Sí | Nombre | `Juan` |
| last_name | string(100) | ✅ Sí | Apellido | `Pérez` |
| email | string(255) | ✅ Sí | Email único | `juan@example.com` |
| password | string(255) | ✅ Sí | Contraseña hasheada | `$2y$12$...` |

#### Excel/CSV de ejemplo:
```csv
first_name,last_name,email,password
Juan,Pérez,juan.perez@example.com,$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5HS9h.tZx.Gla
María,García,maria.garcia@example.com,$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5HS9h.tZx.Gla
Carlos,López,carlos.lopez@example.com,$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5HS9h.tZx.Gla
```

#### ⚠️ Validaciones y Notas:
- `email` debe ser único y válido
- El password mostrado es el hash de `password123`
- **Para generar el hash de una contraseña:**
  ```bash
  php artisan tinker
  >>> bcrypt('tuContraseña')
  ```
- O puedes usar este hash que equivale a `password123`:
  ```
  $2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5HS9h.tZx.Gla
  ```

---

### 5️⃣ Productos ⭐ (Tabla Principal)

**Tabla:** `productos`  
**Archivo:** `5_productos.csv`

#### Campos:
| Campo | Tipo | Obligatorio | Descripción | Ejemplo | Notas |
|-------|------|-------------|-------------|---------|-------|
| nombre | string(2000) | ✅ Sí | Nombre del producto | `Camiseta Gamer` | |
| categoria | string | ✅ Sí | Nombre de la categoría | `Ropa` | Busca en tabla `categories` |
| tematica | string | ✅ Sí | Nombre de la temática | `Videojuegos` | Busca en tabla `themes` |
| descripcion | text | ❌ No | Descripción detallada | `Camiseta 100% algodón...` | |
| precio_base | decimal(10,2) | ✅ Sí | Precio en pesos | `29990` o `29990.00` | Sin símbolos |
| stock | integer | ✅ Sí | Cantidad disponible | `50` | Número entero |
| imagen | string(2000) | ❌ No | URL de la imagen | `https://...jpg` | |
| status | enum | ✅ Sí | Estado del producto | `activo` o `inactivo` | Solo estos valores |

#### Excel/CSV de ejemplo (completo):
```csv
nombre,categoria,tematica,descripcion,precio_base,stock,imagen,status
Camiseta Gamer Level Up,Ropa,Videojuegos,"Camiseta de algodón 100% con diseño de control de videojuegos y texto 'Level Up'. Disponible en color negro con estampado blanco. Corte unisex, ideal para gamers.",29990,50,https://example.com/images/camiseta-gamer.jpg,activo
Taza Star Wars Darth Vader,Hogar y Decoración,Películas,"Taza de cerámica de 350ml con diseño de Darth Vader. Apta para microondas y lavavajillas. Acabado mate en color negro con detalles en rojo.",15990,100,https://example.com/images/taza-darth-vader.jpg,activo
Figura Naruto Uzumaki,Juguetes,Anime,"Figura coleccionable de Naruto Uzumaki de 15cm de altura. Material PVC de alta calidad con detalles pintados a mano. Incluye base de soporte y viene en caja decorativa.",39990,30,https://example.com/images/figura-naruto.jpg,activo
Poster Marvel Avengers,Hogar y Decoración,Películas,"Poster de alta calidad 50x70cm con diseño de los Avengers. Impresión en papel couché de 300gr. Colores vibrantes y resistentes a la luz.",9990,75,https://example.com/images/poster-avengers.jpg,activo
Gorra Pokémon Pikachu,Accesorios,Videojuegos,"Gorra ajustable de algodón con bordado de Pikachu en la parte frontal. Cierre regulable en la parte trasera. Color amarillo y negro.",19990,40,https://example.com/images/gorra-pokemon.jpg,activo
```

#### ⚠️ Validaciones Importantes:
- **categoria**: Debe existir EXACTAMENTE en la tabla `categories` (campo `name`)
- **tematica**: Debe existir EXACTAMENTE en la tabla `themes` (campo `name`)
- **precio_base**: Solo números, puede tener decimales (29990 o 29990.50)
- **stock**: Solo números enteros positivos
- **status**: Solo puede ser `activo` o `inactivo`

#### 💡 Consejos para Productos:
1. **Categorías y Temáticas**: Deben coincidir EXACTAMENTE con los nombres que usaste al importar esas tablas
   - ✅ Correcto: `Ropa` (si así lo creaste en categories)
   - ❌ Incorrecto: `ropa`, `ROPA`, `Ropa ` (con espacio)

2. **Precios**: Usa el formato chileno sin puntos ni símbolos
   - ✅ Correcto: `29990`, `29990.50`
   - ❌ Incorrecto: `$29.990`, `29,990`

3. **Descripciones**: Si tienen comas, encierra todo entre comillas dobles
   ```csv
   nombre,descripcion
   Producto,"Esta es una descripción con comas, puntos, y más."
   ```

---

## 🚀 Proceso Completo de Importación

### Paso 1: Preparar los archivos

1. Crea los 5 archivos CSV en Excel:
   - `1_categories.csv`
   - `2_themes.csv`
   - `3_materiales.csv`
   - `4_usuarios.csv` (opcional)
   - `5_productos.csv`

2. Al guardar desde Excel:
   - **Archivo → Guardar como**
   - Formato: **CSV (delimitado por comas) (*.csv)**
   - Encoding: **UTF-8** (importante)

### Paso 2: Colocar archivos

```bash
# Crear carpeta si no existe
mkdir -p taskcurso/storage/app/imports

# Copiar todos los archivos
cp 1_categories.csv taskcurso/storage/app/imports/
cp 2_themes.csv taskcurso/storage/app/imports/
cp 3_materiales.csv taskcurso/storage/app/imports/
cp 4_usuarios.csv taskcurso/storage/app/imports/
cp 5_productos.csv taskcurso/storage/app/imports/
```

### Paso 3: Ejecutar importación

```bash
cd taskcurso

# Importar todo de una vez
php artisan db:seed --class=ExcelImportSeeder
```

### Paso 4: Verificar

```bash
php artisan tinker

# Verificar categorías
>>> DB::table('categories')->count()
=> 7

# Verificar themes
>>> DB::table('themes')->count()
=> 8

# Verificar productos
>>> DB::table('productos')->count()
=> 20

# Ver primeros productos
>>> DB::table('productos')->limit(3)->get()
```

---

## 📝 Plantillas en Blanco para Excel

### Categorías:
```csv
name,imagen
,
,
,
```

### Temáticas:
```csv
name,imagen
,
,
,
```

### Materiales:
```csv
nombre
,
,
,
```

### Usuarios:
```csv
first_name,last_name,email,password
,,,
,,,
,,,
```

### Productos:
```csv
nombre,categoria,tematica,descripcion,precio_base,stock,imagen,status
,,,,,,,
,,,,,,,
,,,,,,,
```

---

## 🔥 Tips Pro

### 1. Validar antes de importar
```bash
# Ver preview sin importar
php artisan import:csv storage/app/imports/5_productos.csv productos --dry-run
```

### 2. Importar solo una tabla
```bash
# Modificar ExcelImportSeeder.php:
$toImport = [
    // 'categories',
    // 'themes',
    // 'materiales',
    // 'usuarios',
    'productos',  // Solo esta
];
```

### 3. Limpiar y volver a importar
```bash
php artisan tinker

# Limpiar todas las tablas (CUIDADO: borra todo)
>>> DB::table('productos')->truncate();
>>> DB::table('categories')->truncate();
>>> DB::table('themes')->truncate();
>>> DB::table('materiales')->truncate();

# Volver a importar
>>> exit
php artisan db:seed --class=ExcelImportSeeder
```

### 4. Usar valores por defecto
Si no quieres llenar algunos campos opcionales, el sistema usará:
- `created_at` y `updated_at`: Se llenan automáticamente con la fecha actual
- `imagen`: Si no la tienes, déjala vacía o pon una URL genérica
- `descripcion`: Puede estar vacía

---

## ⚠️ Errores Comunes

### Error: "Foreign key constraint fails"
**Causa:** Intentas crear un producto con una categoría o temática que no existe.

**Solución:**
1. Verifica que las categorías y temáticas estén importadas primero
2. Verifica que los nombres coincidan EXACTAMENTE (mayúsculas, espacios, etc.)

```bash
# Ver categorías disponibles
php artisan tinker
>>> DB::table('categories')->pluck('name')
=> ["Ropa", "Accesorios", "Juguetes", ...]

# Ver temáticas disponibles
>>> DB::table('themes')->pluck('name')
=> ["Videojuegos", "Anime", "Películas", ...]
```

### Error: "Duplicate entry"
**Causa:** Intentas insertar un valor que debe ser único (email, name de category, etc.)

**Solución:**
- Revisa tu CSV y elimina duplicados
- Si ya existe en la BD, elimínalo primero:
```bash
php artisan tinker
>>> DB::table('categories')->where('name', 'Ropa')->delete();
```

### Error: Caracteres raros (ñ, tildes)
**Causa:** Encoding incorrecto del CSV.

**Solución:**
- Guarda el CSV con encoding UTF-8
- En Excel: Guardar como → CSV UTF-8

---

## 📞 Checklist Pre-Importación

Antes de importar, verifica:

- [ ] Los archivos CSV tienen la extensión `.csv`
- [ ] Los encabezados coinciden EXACTAMENTE con los ejemplos
- [ ] No hay espacios extra en los nombres de columnas
- [ ] Las categorías y temáticas existen antes de importar productos
- [ ] Los precios son números sin símbolos ($, puntos, comas)
- [ ] El status es `activo` o `inactivo` (minúsculas)
- [ ] Los emails de usuarios son únicos
- [ ] El encoding es UTF-8

---

## 🎁 Archivos de Ejemplo Incluidos

En `storage/app/imports/` encontrarás:
- ✅ `1_categories.csv` - 7 categorías de ejemplo
- ✅ `2_themes.csv` - 8 temáticas de ejemplo
- ✅ `3_materiales.csv` - 10 materiales de ejemplo
- ✅ `4_usuarios.csv` - 5 usuarios de ejemplo (password: `password123`)
- ✅ `5_productos.csv` - 20 productos completos de ejemplo

**Puedes usarlos directamente:**
```bash
cd taskcurso
php artisan db:seed --class=ExcelImportSeeder
```

¡Listo para poblar tu base de datos! 🎉
