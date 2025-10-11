# ================================================
# Script de Prueba Local para CI/CD
# ================================================
# Este script simula el entorno de CI/CD localmente
# Para probar antes de hacer push
# ================================================

Write-Host "================================" -ForegroundColor Cyan
Write-Host "🧪 Simulador de CI/CD Local" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "front-vue")) {
    Write-Host "❌ Error: Ejecuta este script desde la raíz del proyecto" -ForegroundColor Red
    exit 1
}

Write-Host "📁 Directorio correcto verificado" -ForegroundColor Green

# ============================================
# PASO 1: Pruebas Unitarias
# ============================================
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "🧪 PASO 1: Pruebas Unitarias" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow

try {
    docker exec vue_frontend npm run test:unit -- --run
    Write-Host "✅ Pruebas unitarias: PASARON" -ForegroundColor Green
    $unitTests = $true
} catch {
    Write-Host "❌ Pruebas unitarias: FALLARON" -ForegroundColor Red
    $unitTests = $false
}

# ============================================
# PASO 2: Pruebas de Integración
# ============================================
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "🔄 PASO 2: Pruebas de Integración" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow

try {
    docker exec vue_frontend npm run test:automation -- --run
    Write-Host "✅ Pruebas de integración: PASARON" -ForegroundColor Green
    $integrationTests = $true
} catch {
    Write-Host "❌ Pruebas de integración: FALLARON" -ForegroundColor Red
    $integrationTests = $false
}

# ============================================
# PASO 3: Verificar servidor
# ============================================
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "🌐 PASO 3: Verificar Servidor" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:5173" -UseBasicParsing -TimeoutSec 5
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Servidor frontend: DISPONIBLE" -ForegroundColor Green
        $serverReady = $true
    }
} catch {
    Write-Host "❌ Servidor frontend: NO DISPONIBLE" -ForegroundColor Red
    Write-Host "   Asegúrate de ejecutar: docker-compose up -d" -ForegroundColor Yellow
    $serverReady = $false
}

# ============================================
# PASO 4: Pruebas E2E (Local)
# ============================================
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "🎭 PASO 4: Pruebas E2E (Opcional)" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow

Write-Host ""
Write-Host "⚠️  Las pruebas E2E deben ejecutarse localmente:" -ForegroundColor Yellow
Write-Host "   cd front-vue" -ForegroundColor Cyan
Write-Host "   npm run test:e2e:simple" -ForegroundColor Cyan
Write-Host ""

$runE2E = Read-Host "¿Quieres ejecutar las pruebas E2E ahora? (s/n)"

if ($runE2E -eq "s" -or $runE2E -eq "S") {
    try {
        Set-Location front-vue
        npm run test:e2e:simple
        Write-Host "✅ Pruebas E2E: PASARON" -ForegroundColor Green
        $e2eTests = $true
        Set-Location ..
    } catch {
        Write-Host "❌ Pruebas E2E: FALLARON" -ForegroundColor Red
        Write-Host "   Verifica que Playwright esté instalado: npm run test:install" -ForegroundColor Yellow
        $e2eTests = $false
        Set-Location ..
    }
} else {
    Write-Host "⏭️  Pruebas E2E omitidas" -ForegroundColor Yellow
    $e2eTests = $null
}

# ============================================
# RESUMEN FINAL
# ============================================
Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "📊 RESUMEN FINAL" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

$allPassed = $true

# Unitarias
if ($unitTests) {
    Write-Host "✅ Pruebas Unitarias: PASARON" -ForegroundColor Green
} else {
    Write-Host "❌ Pruebas Unitarias: FALLARON" -ForegroundColor Red
    $allPassed = $false
}

# Integración
if ($integrationTests) {
    Write-Host "✅ Pruebas Integración: PASARON" -ForegroundColor Green
} else {
    Write-Host "❌ Pruebas Integración: FALLARON" -ForegroundColor Red
    $allPassed = $false
}

# Servidor
if ($serverReady) {
    Write-Host "✅ Servidor: DISPONIBLE" -ForegroundColor Green
} else {
    Write-Host "❌ Servidor: NO DISPONIBLE" -ForegroundColor Red
    $allPassed = $false
}

# E2E
if ($null -eq $e2eTests) {
    Write-Host "⏭️  Pruebas E2E: OMITIDAS" -ForegroundColor Yellow
} elseif ($e2eTests) {
    Write-Host "✅ Pruebas E2E: PASARON" -ForegroundColor Green
} else {
    Write-Host "❌ Pruebas E2E: FALLARON" -ForegroundColor Red
    $allPassed = $false
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan

if ($allPassed) {
    Write-Host "🎉 TODO LISTO PARA CI/CD!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos pasos:" -ForegroundColor Cyan
    Write-Host "1. git add ." -ForegroundColor White
    Write-Host "2. git commit -m 'Add CI/CD configuration'" -ForegroundColor White
    Write-Host "3. git push origin main" -ForegroundColor White
    Write-Host ""
    Write-Host "El pipeline se ejecutará automáticamente" -ForegroundColor Green
} else {
    Write-Host "⚠️  ALGUNAS PRUEBAS FALLARON" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Revisa los errores antes de hacer push" -ForegroundColor Yellow
    Write-Host "El pipeline de CI/CD probablemente fallará" -ForegroundColor Red
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
