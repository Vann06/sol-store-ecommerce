# 🔄 Script para Resetear y Re-Importar Base de Datos

Write-Host "⚠️  ADVERTENCIA: Este script eliminará TODOS los datos de la base de datos" -ForegroundColor Red
Write-Host ""
$confirm = Read-Host "¿Estás seguro? (escribe 'SI' para continuar)"

if ($confirm -ne "SI") {
    Write-Host "❌ Operación cancelada" -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "🔄 Reseteando base de datos..." -ForegroundColor Cyan
Write-Host ""

# 1. Drop todas las tablas y re-crear
Write-Host "🗑️  Eliminando tablas existentes..." -ForegroundColor Yellow
docker-compose exec -T backend php artisan migrate:fresh --force

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Base de datos limpia" -ForegroundColor Green
} else {
    Write-Host "❌ Error al limpiar base de datos" -ForegroundColor Red
    exit 1
}

Write-Host ""

# 2. Re-importar datos
Write-Host "📊 Re-importando datos desde CSV..." -ForegroundColor Cyan
docker-compose exec -T backend php artisan db:seed --class=ExcelImportSeeder

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Datos importados correctamente" -ForegroundColor Green
} else {
    Write-Host "❌ Error en importación" -ForegroundColor Red
    exit 1
}

Write-Host ""

# 3. Verificar
Write-Host "🔍 Verificando datos..." -ForegroundColor Cyan
docker-compose exec -T backend php artisan import:verify

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
Write-Host "✅ ¡BASE DE DATOS RESETEADA!" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
Write-Host ""
