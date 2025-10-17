# ⚡ RESPUESTA RÁPIDA: Levantar Contenedores e Importar Datos

## ❌ NO es automático

```bash
docker-compose up -d
# ❌ La base de datos queda VACÍA
# ❌ NO se importan datos automáticamente
```

---

## ✅ Proceso Correcto

### Opción 1: Automático (RECOMENDADO) 🚀

```bash
.\setup-database.ps1
```

**Hace todo por ti:**
- ✅ Levanta contenedores
- ✅ Espera a que BD esté lista
- ✅ Ejecuta migraciones
- ✅ Importa datos desde CSV
- ✅ Verifica que todo esté OK

---

### Opción 2: Manual

```bash
# 1. Levantar contenedores
docker-compose up -d

# 2. Esperar 30 segundos
Start-Sleep -Seconds 30

# 3. Crear tablas
docker-compose exec backend php artisan migrate

# 4. Importar datos
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder

# 5. Verificar
docker-compose exec backend php artisan import:verify
```

---

## 🔄 Flujo Visual

### ❌ Lo que NO pasa:

```
docker-compose up -d
         ↓
   🐳 Contenedores
         ↓
   🗄️ Base de datos
         ↓
    📦 Vacía ❌
```

### ✅ Lo que SÍ necesitas:

```
.\setup-database.ps1
         ↓
   🐳 Contenedores
         ↓
   🔧 Migraciones
         ↓
   📊 Import CSV
         ↓
   ✅ 20 productos
```

---

## 📋 Escenarios Comunes

### 1️⃣ Primera vez levantando el proyecto

```bash
.\setup-database.ps1
```

### 2️⃣ Modificaste los CSV

```bash
.\reset-database.ps1
```

### 3️⃣ Contenedores ya están corriendo

```bash
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder
```

### 4️⃣ Quieres limpiar todo

```bash
.\reset-database.ps1
```

---

## 🎯 Resumen

| Lo que haces | Lo que pasa |
|--------------|-------------|
| `docker-compose up -d` | ❌ BD vacía |
| `.\setup-database.ps1` | ✅ Todo listo con datos |
| `.\reset-database.ps1` | ✅ Limpia y re-importa |

---

## 💡 Scripts Creados

### 📄 `setup-database.ps1`
- **Úsalo:** Primera vez o cuando reinicies el proyecto
- **Hace:** Todo el setup automático
- **Resultado:** BD con 20 productos listos

### 📄 `reset-database.ps1`
- **Úsalo:** Cuando modificas los CSV
- **Hace:** Borra todo y re-importa
- **Resultado:** BD limpia con datos nuevos

---

## ✅ Comandos Rápidos

```bash
# Setup inicial
.\setup-database.ps1

# Ver si hay datos
docker-compose exec backend php artisan tinker
>>> DB::table('productos')->count()

# Re-importar
docker-compose exec backend php artisan db:seed --class=ExcelImportSeeder

# Verificar
docker-compose exec backend php artisan import:verify
```

---

**🚀 TL;DR: Usa `.\setup-database.ps1` y olvídate del resto!**
