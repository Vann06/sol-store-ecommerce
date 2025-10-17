# 📚 Índice de Documentación - Importación desde Excel

## 🚀 Inicio Rápido

👉 **[README_IMPORTACION.md](README_IMPORTACION.md)** ⭐ **EMPIEZA AQUÍ**
- Guía de 3 pasos para importar
- Comandos esenciales
- Troubleshooting rápido

---

## 📖 Documentación por Nivel

### 🟢 Nivel Principiante

1. **[README_IMPORTACION.md](README_IMPORTACION.md)**
   - Quick start en 3 pasos
   - Comandos básicos
   - Errores comunes

2. **[IMPORTAR_EXCEL_QUICKSTART.md](IMPORTAR_EXCEL_QUICKSTART.md)**
   - 3 formas de importar
   - Ejemplos paso a paso
   - Tips rápidos

### 🟡 Nivel Intermedio

3. **[ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md)**
   - Estructura detallada de cada tabla
   - Campos obligatorios y opcionales
   - Validaciones y restricciones
   - Orden de importación

4. **[PLANTILLAS_EXCEL.md](PLANTILLAS_EXCEL.md)**
   - Tablas listas para copiar/pegar
   - Formato visual para Excel
   - Plantillas en blanco

### 🔴 Nivel Avanzado

5. **[IMPORTAR_EXCEL.md](IMPORTAR_EXCEL.md)**
   - Configuración avanzada
   - Transformaciones de datos
   - Relaciones automáticas
   - Procesamiento por lotes
   - Debugging y logs

6. **[VISTA_DATOS_EJEMPLO.md](VISTA_DATOS_EJEMPLO.md)**
   - Vista de todos los datos de ejemplo
   - Análisis estadístico
   - Distribución de productos
   - Valor del inventario

---

## 🎯 Por Tarea

### "Quiero importar datos AHORA"
→ **[README_IMPORTACION.md](README_IMPORTACION.md)**

### "¿Cómo debe ser mi Excel?"
→ **[ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md)**

### "Dame plantillas para copiar"
→ **[PLANTILLAS_EXCEL.md](PLANTILLAS_EXCEL.md)**

### "Quiero ver los datos de ejemplo"
→ **[VISTA_DATOS_EJEMPLO.md](VISTA_DATOS_EJEMPLO.md)**

### "Necesito configuración avanzada"
→ **[IMPORTAR_EXCEL.md](IMPORTAR_EXCEL.md)**

### "¿Cuáles son las formas de importar?"
→ **[IMPORTAR_EXCEL_QUICKSTART.md](IMPORTAR_EXCEL_QUICKSTART.md)**

---

## 📂 Archivos del Sistema

### Scripts PHP
- **`import-excel.php`** - Script independiente para importar
- **`database/seeders/ExcelImportSeeder.php`** - Seeder de Laravel
- **`app/Console/Commands/ImportFromCsv.php`** - Comando Artisan
- **`app/Console/Commands/VerifyImport.php`** - Verificación de datos

### Archivos CSV de Ejemplo
```
storage/app/imports/
├── 1_categories.csv        (7 categorías)
├── 2_themes.csv           (8 temáticas)
├── 3_materiales.csv       (10 materiales)
├── 4_usuarios.csv         (5 usuarios)
└── 5_productos.csv        (20 productos completos)
```

---

## 🎓 Tutoriales Paso a Paso

### Tutorial 1: Primera Importación (5 minutos)
```bash
# 1. Ir al proyecto
cd taskcurso

# 2. Importar datos de ejemplo
php artisan db:seed --class=ExcelImportSeeder

# 3. Verificar
php artisan import:verify
```

### Tutorial 2: Importar Tus Propios Datos (15 minutos)
1. Abrir **[PLANTILLAS_EXCEL.md](PLANTILLAS_EXCEL.md)**
2. Copiar las tablas a Excel
3. Llenar con tus datos
4. Guardar como CSV UTF-8
5. Colocar en `storage/app/imports/`
6. Ejecutar: `php artisan db:seed --class=ExcelImportSeeder`

### Tutorial 3: Importación Avanzada (30 minutos)
1. Leer **[ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md)**
2. Configurar transformaciones en `ExcelImportSeeder.php`
3. Agregar validaciones personalizadas
4. Ver **[IMPORTAR_EXCEL.md](IMPORTAR_EXCEL.md)** para ejemplos

---

## 🔧 Comandos Principales

### Importación
```bash
# Importar todo (usando seeder)
php artisan db:seed --class=ExcelImportSeeder

# Importar un archivo específico (usando comando)
php artisan import:csv storage/app/imports/5_productos.csv productos

# Preview sin importar
php artisan import:csv storage/app/imports/5_productos.csv productos --dry-run
```

### Verificación
```bash
# Verificar datos importados
php artisan import:verify

# Ver datos en la consola
php artisan tinker
>>> DB::table('productos')->count()
>>> DB::table('productos')->get()
```

### Limpieza
```bash
# Limpiar todas las tablas
php artisan tinker
>>> DB::table('productos')->truncate();
>>> DB::table('categories')->truncate();
>>> DB::table('themes')->truncate();
```

---

## 📋 Estructura de Tablas

### Orden de Dependencias
```
1. categories        ← Sin dependencias
2. themes           ← Sin dependencias
3. materiales       ← Sin dependencias
4. usuarios         ← Sin dependencias (opcional)
5. productos        ← Depende de: categories, themes
```

### Campos por Tabla

#### Categories
- `name` (obligatorio, único)
- `imagen` (opcional)

#### Themes
- `name` (obligatorio, único)
- `imagen` (opcional)

#### Materiales
- `nombre` (obligatorio, único)

#### Usuarios
- `first_name` (obligatorio)
- `last_name` (obligatorio)
- `email` (obligatorio, único)
- `password` (obligatorio, hasheado)

#### Productos ⭐
- `nombre` (obligatorio)
- `categoria` (obligatorio, debe existir en categories)
- `tematica` (obligatorio, debe existir en themes)
- `descripcion` (opcional)
- `precio_base` (obligatorio, decimal)
- `stock` (obligatorio, entero)
- `imagen` (opcional)
- `status` (obligatorio: 'activo' o 'inactivo')

---

## ⚠️ Problemas Comunes y Soluciones

| Problema | Documento | Sección |
|----------|-----------|---------|
| Error de foreign key | [ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md) | Errores Comunes |
| Categoría no encontrada | [README_IMPORTACION.md](README_IMPORTACION.md) | Errores Comunes |
| Caracteres raros | [ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md) | Problemas Comunes |
| Duplicados | [README_IMPORTACION.md](README_IMPORTACION.md) | Errores Comunes |
| Encoding UTF-8 | [IMPORTAR_EXCEL.md](IMPORTAR_EXCEL.md) | Problemas Comunes |

---

## 💡 Tips y Mejores Prácticas

### ✅ Hacer
- Importar en orden: categories → themes → materiales → usuarios → productos
- Usar encoding UTF-8 siempre
- Verificar con `php artisan import:verify` después de importar
- Hacer preview con `--dry-run` antes de importar datos reales
- Mantener backup de la base de datos antes de importaciones grandes

### ❌ No Hacer
- No importar productos antes que categorías y temáticas
- No usar Excel (.xlsx) directamente, siempre convertir a CSV
- No usar símbolos ($, puntos, comas) en los precios
- No dejar espacios extra en los nombres
- No mezclar mayúsculas/minúsculas en las relaciones

---

## 🆘 Ayuda y Soporte

### Logs
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Ver errores específicos
grep "ERROR" storage/logs/laravel.log
```

### Debugging
```bash
# Inspeccionar base de datos
php artisan tinker

# Ver estructura de tabla
>>> DB::select('DESCRIBE productos')

# Contar registros
>>> DB::table('productos')->count()

# Buscar productos huérfanos
>>> DB::table('productos as p')
    ->leftJoin('categories as c', 'p.id_categoria', '=', 'c.id')
    ->whereNull('c.id')
    ->get()
```

---

## 🎁 Recursos Adicionales

### Archivos de Ejemplo Incluidos
- ✅ 20 productos completos con descripciones reales
- ✅ 7 categorías populares
- ✅ 8 temáticas variadas
- ✅ 10 materiales comunes
- ✅ 5 usuarios de prueba (password: `password123`)

### Valor del Inventario de Ejemplo
- **Total productos:** 20
- **Stock total:** 1.070 unidades
- **Rango de precios:** $5.990 - $79.990
- **Valor estimado:** ~$28.000.000

---

## 📊 Flujo de Trabajo Recomendado

```
1. Leer README_IMPORTACION.md
   ↓
2. Ejecutar importación de ejemplo
   ↓
3. Verificar con import:verify
   ↓
4. Si OK → Leer ESTRUCTURA_EXCEL_COMPLETA.md
   ↓
5. Preparar tus propios datos usando PLANTILLAS_EXCEL.md
   ↓
6. Limpiar base de datos (truncate)
   ↓
7. Importar tus datos
   ↓
8. Verificar de nuevo
   ↓
9. Para casos avanzados → Leer IMPORTAR_EXCEL.md
```

---

## 🚀 Quick Links

- **Empezar:** [README_IMPORTACION.md](README_IMPORTACION.md)
- **Estructura:** [ESTRUCTURA_EXCEL_COMPLETA.md](ESTRUCTURA_EXCEL_COMPLETA.md)
- **Plantillas:** [PLANTILLAS_EXCEL.md](PLANTILLAS_EXCEL.md)
- **Datos:** [VISTA_DATOS_EJEMPLO.md](VISTA_DATOS_EJEMPLO.md)
- **Avanzado:** [IMPORTAR_EXCEL.md](IMPORTAR_EXCEL.md)

---

**Última actualización:** Octubre 2025  
**Versión:** 1.0  
**Estado:** ✅ Completo y funcional
