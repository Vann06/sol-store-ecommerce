# 🚀 Guía Rápida: Levantar Proyecto con Datos

## ⚡ Quick Start (Método Automático)

### Primera vez (Setup completo):

```bash
# 1. Levantar contenedores + importar datos
.\setup-database.ps1
```

¡Eso es todo! El script hace:
- ✅ Levanta contenedores
- ✅ Ejecuta migraciones
- ✅ Importa todos los datos desde CSV
- ✅ Verifica que todo esté OK

---

## 🔄 Resetear Base de Datos

Si modificaste los CSV y quieres empezar de cero:

```bash
.\reset-database.ps1
```

Te pedirá confirmación antes de borrar todo.

---

## 🛠️ Método Manual (Paso a Paso)

Si prefieres hacerlo manualmente:

### 1. Levantar contenedores

```bash
docker-compose up -d
```

### 2. Esperar a que la BD inicie (~30 segundos)

```bash
# Verificar que el contenedor de BD esté "healthy"
docker-compose ps
```

### 3. Ejecutar migraciones (crear tablas)

```bash
docker-compose exec backend php artisan migrate
```

### 4. Importar datos desde CSV

```bash
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

### 5. Verificar

```bash
docker-compose exec backend php artisan import:verify
```

---

## 📊 ¿Qué se Importa?

Al ejecutar el seeder, se importan:

| Tabla | Registros | Archivo |
|-------|-----------|---------|
| Categories | 7 | `1_categories.csv` |
| Themes | 8 | `2_themes.csv` |
| Materiales | 10 | `3_materiales.csv` |
| Usuarios | 5 | `4_usuarios.csv` |
| Productos | 20 | `5_productos.csv` |

**Total:** 50 registros

---

## 🔧 Comandos Útiles

### Ver logs del backend
```bash
docker-compose logs -f backend
```

### Acceder al contenedor del backend
```bash
docker-compose exec backend bash
```

### Ver datos en la BD (Laravel Tinker)
```bash
docker-compose exec backend php artisan tinker

# Dentro de tinker:
>>> DB::table('productos')->count()
=> 20

>>> DB::table('productos')->first()
```

### Limpiar todo y empezar de cero
```bash
docker-compose exec backend php artisan migrate:fresh
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

---

## 📝 Flujo de Trabajo Completo

### Escenario 1: Primera vez levantando el proyecto

```bash
# 1. Clonar repo (ya lo tienes)
# 2. Configurar .env (ya debería estar)

# 3. Setup automático
.\setup-database.ps1

# ¡Listo! Ya tienes 20 productos en la BD
```

### Escenario 2: Ya tenías contenedores corriendo

```bash
# Solo importar/re-importar datos
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

### Escenario 3: Modificaste los CSV

```bash
# Opción A: Limpiar y re-importar
.\reset-database.ps1

# Opción B: Manual
docker-compose exec backend php artisan migrate:fresh
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

### Escenario 4: Solo quieres agregar más productos

```bash
# Edita taskcurso/storage/app/imports/5_productos.csv
# Agrega más filas al final

# Re-importa (agregará los nuevos)
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

---

## ❌ Problemas Comunes

### "Connection refused" al importar

**Problema:** La BD aún no está lista.

**Solución:**
```bash
# Espera 30 segundos y reintenta
Start-Sleep -Seconds 30
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

### "Table doesn't exist"

**Problema:** No se ejecutaron las migraciones.

**Solución:**
```bash
docker-compose exec backend php artisan migrate
```

### "Duplicate entry"

**Problema:** Los datos ya existen en la BD.

**Solución:** Resetear primero
```bash
.\reset-database.ps1
```

### "File not found: storage/app/imports/..."

**Problema:** Los archivos CSV no están en el contenedor.

**Solución:** Verificar que están mapeados en `docker-compose.yml`
```yaml
volumes:
  - ./taskcurso:/var/www/html
```

---

## 🎯 Respuesta a tu Pregunta Original

### ❌ NO automático:

```bash
docker-compose up -d
# ❌ Base de datos vacía
# ❌ Necesitas ejecutar el seeder manualmente
```

### ✅ Correcto:

```bash
# Opción 1: Script automático
.\setup-database.ps1

# Opción 2: Manual
docker-compose up -d
# Esperar 30 segundos
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

---

## 💡 Automatizar al 100%

Si quieres que se importe automáticamente al levantar contenedores, puedes modificar el `entrypoint.sh`:

```bash
# taskcurso/entrypoint.sh

#!/bin/bash

# Esperar a que la BD esté lista
sleep 30

# Ejecutar migraciones
php artisan migrate --force

# Importar datos si la tabla productos está vacía
COUNT=$(php artisan tinker --execute="echo DB::table('productos')->count();")
if [ "$COUNT" -eq "0" ]; then
    echo "📊 Importando datos iniciales..."
    php artisan db:seed --class=ExcelImportSeeder
fi

# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=8000
```

**Pero NO lo recomiendo** porque:
- ⚠️ Agrega 30 segundos al startup siempre
- ⚠️ Puede causar problemas si la BD no está lista
- ⚠️ Dificulta debugging

Es mejor usar el script `setup-database.ps1` cuando lo necesites.

---

## 📚 Resumen

| Acción | Comando |
|--------|---------|
| Setup completo | `.\setup-database.ps1` |
| Resetear DB | `.\reset-database.ps1` |
| Solo importar | `docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder` |
| Verificar datos | `docker-compose exec backend php artisan import:verify` |
| Ver logs | `docker-compose logs -f backend` |

---

**¡Usa `setup-database.ps1` para hacerlo todo automáticamente! 🚀**
