# 🚀 Quick Start - Importar desde Excel

## Tres formas de importar datos desde Excel/CSV

### 1️⃣ Comando Artisan (Recomendado - Más fácil)

```bash
# Importar cualquier CSV a cualquier tabla
php artisan import:csv storage/app/productos.csv productos

# Con opciones adicionales
php artisan import:csv storage/app/productos.csv productos --batch=50 --dry-run

# Ver vista previa sin insertar (dry-run)
php artisan import:csv storage/app/ejemplo-productos.csv productos --dry-run
```

**Ventajas:**
- ✅ Más simple y directo
- ✅ Preview antes de importar
- ✅ Validación automática
- ✅ Progreso en tiempo real

### 2️⃣ Seeder de Laravel

```bash
# 1. Coloca tu CSV en storage/app/imports/
mkdir -p storage/app/imports
cp tu-archivo.csv storage/app/imports/productos.csv

# 2. Edita la configuración en database/seeders/ExcelImportSeeder.php

# 3. Ejecuta el seeder
php artisan db:seed --class=ExcelImportSeeder
```

**Ventajas:**
- ✅ Integrado con Laravel
- ✅ Transformación avanzada de datos
- ✅ Manejo automático de relaciones
- ✅ Importar múltiples tablas

### 3️⃣ Script PHP Independiente

```bash
# 1. Coloca tu CSV en storage/app/
cp tu-archivo.csv storage/app/productos.csv

# 2. Edita la configuración en import-excel.php

# 3. Ejecuta
php import-excel.php
```

**Ventajas:**
- ✅ No requiere Laravel
- ✅ Más control manual
- ✅ Fácil de personalizar

---

## 📋 Preparar tu Excel

### Paso 1: Formato correcto
```
| Nombre      | Descripción           | Precio | Stock | Categoría |
|-------------|-----------------------|--------|-------|-----------|
| Producto 1  | Descripción aquí      | 29.99  | 50    | Electro   |
| Producto 2  | Otra descripción      | 39.99  | 30    | Ropa      |
```

### Paso 2: Exportar a CSV
1. **Archivo → Guardar como**
2. Formato: **CSV (delimitado por comas) (*.csv)**
3. Codificación: **UTF-8** (importante para tildes y ñ)

### Paso 3: Colocar en la carpeta correcta
```bash
# Para comando artisan (cualquier ubicación)
cp mi-archivo.csv storage/app/productos.csv

# Para seeder
cp mi-archivo.csv storage/app/imports/productos.csv
```

---

## 🎯 Ejemplo Práctico Completo

### Importar productos desde Excel:

```bash
# 1. Crear carpeta si no existe
mkdir -p storage/app/imports

# 2. Copiar tu archivo Excel (ya exportado a CSV)
# Asegúrate de que tenga encabezados: Nombre, Descripción, Precio, Stock, etc.

# 3. Usar el comando más simple:
php artisan import:csv storage/app/ejemplo-productos.csv productos --dry-run

# 4. Si el preview se ve bien, importar de verdad:
php artisan import:csv storage/app/ejemplo-productos.csv productos

# ¡Listo! ✅
```

---

## ⚡ Comandos Útiles

```bash
# Ver qué se importó
php artisan tinker
>>> DB::table('productos')->count()
>>> DB::table('productos')->latest()->take(5)->get()

# Limpiar tabla antes de importar de nuevo
php artisan tinker
>>> DB::table('productos')->truncate()

# Ver logs si hay errores
tail -f storage/logs/laravel.log
```

---

## 🆘 Problemas Comunes

### ❌ "File not found"
```bash
# Verifica que el archivo existe
ls -la storage/app/productos.csv

# Usa ruta absoluta si es necesario
php artisan import:csv "$(pwd)/storage/app/productos.csv" productos
```

### ❌ Caracteres raros (ñ, tildes)
- Guarda el CSV con encoding **UTF-8**
- En Excel: Guardar como → Más opciones → UTF-8

### ❌ "Table does not exist"
```bash
# Verifica el nombre de la tabla
php artisan tinker
>>> DB::select('SHOW TABLES')

# Ejecuta migraciones si es necesario
php artisan migrate
```

### ❌ Errores de datos
- Revisa que las columnas del CSV coincidan con las de la BD
- Verifica que no haya celdas vacías obligatorias
- Usa `--dry-run` para ver preview antes de importar

---

## 📚 Documentación Completa

Para más detalles, transformaciones avanzadas y configuración:
👉 Ver **IMPORTAR_EXCEL.md**

---

## 🎁 Archivo de Ejemplo

Incluido: `storage/app/ejemplo-productos.csv`

Puedes usarlo para probar:
```bash
php artisan import:csv storage/app/ejemplo-productos.csv productos --dry-run
```

¡Eso es todo! 🎉
