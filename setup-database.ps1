# 🚀 Script de Setup Completo - Base de Datos

Write-Host "🚀 Iniciando setup de base de datos..." -ForegroundColor Green
Write-Host ""

# 1. Verificar que los contenedores estén corriendo
Write-Host "📦 Verificando contenedores..." -ForegroundColor Cyan
$containers = docker-compose ps -q
if (-not $containers) {
    Write-Host "⚠️  Los contenedores no están corriendo. Levantándolos..." -ForegroundColor Yellow
    docker-compose up -d
    Write-Host "⏳ Esperando 30 segundos para que la BD inicie..." -ForegroundColor Yellow
    Start-Sleep -Seconds 30
} else {
    Write-Host "✅ Contenedores corriendo" -ForegroundColor Green
}

Write-Host ""

# 2. Ejecutar migraciones
Write-Host "🔧 Ejecutando migraciones (crear tablas)..." -ForegroundColor Cyan
docker-compose exec -T backend php artisan migrate --force

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migraciones completadas" -ForegroundColor Green
} else {
    Write-Host "❌ Error en migraciones" -ForegroundColor Red
    exit 1
}

Write-Host ""

# 3. Importar datos desde CSV
Write-Host "📊 Importando datos desde CSV..." -ForegroundColor Cyan
Write-Host "   - Categorías (7)" -ForegroundColor Gray
Write-Host "   - Temáticas (8)" -ForegroundColor Gray
Write-Host "   - Materiales (10)" -ForegroundColor Gray
Write-Host "   - Usuarios (5)" -ForegroundColor Gray
Write-Host "   - Productos (20)" -ForegroundColor Gray
Write-Host ""

docker-compose exec -T backend php artisan db:seed --class=ExcelImportSeeder

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Datos importados correctamente" -ForegroundColor Green
} else {
    Write-Host "❌ Error en importación" -ForegroundColor Red
    exit 1
}

Write-Host ""

# 4. Verificar datos
Write-Host "🔍 Verificando datos importados..." -ForegroundColor Cyan
docker-compose exec -T backend php artisan import:verify

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
Write-Host "✅ ¡SETUP COMPLETO!" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
Write-Host ""
Write-Host "📊 Tu base de datos ahora tiene:" -ForegroundColor Cyan
Write-Host "   ✅ 7 categorías" -ForegroundColor White
Write-Host "   ✅ 8 temáticas" -ForegroundColor White
Write-Host "   ✅ 10 materiales" -ForegroundColor White
Write-Host "   ✅ 5 usuarios" -ForegroundColor White
Write-Host "   ✅ 20 productos" -ForegroundColor White
Write-Host ""
Write-Host "🌐 Puedes acceder a:" -ForegroundColor Cyan
Write-Host "   Backend API: http://localhost:8000" -ForegroundColor White
Write-Host "   Frontend: http://localhost:5173" -ForegroundColor White
Write-Host ""
