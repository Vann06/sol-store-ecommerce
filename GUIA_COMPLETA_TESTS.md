# 🧪 Guía Completa de Ejecución de Tests

## 📋 Tabla de Contenidos
- [Resumen Rápido](#resumen-rápido)
- [Pruebas en Docker](#pruebas-en-docker)
- [Pruebas en tu PC Local](#pruebas-en-tu-pc-local)
- [Todas las Pruebas de una Vez](#todas-las-pruebas-de-una-vez)
- [Ver Reportes](#ver-reportes)
- [Solución de Problemas](#solución-de-problemas)

---

## ⚡ Resumen Rápido

### Ejecutar TODO (Recomendado)
```powershell
# 1. Pruebas en Docker (Unitarias + Integración)
docker exec vue_frontend npm run test -- --run

# 2. Pruebas E2E en tu PC
cd front-vue
npx playwright test --config=playwright.config.simple.js
```

---

## 🐳 Pruebas en Docker

### Requisito Previo
```powershell
# Asegúrate de que los contenedores estén corriendo
docker-compose up -d

# Verifica que estén activos
docker ps
```

### 1️⃣ Pruebas Unitarias (9 tests)
**¿Qué prueban?** Componentes individuales (Header, Footer, LoginForm)

```powershell
# Ejecutar pruebas unitarias
docker exec vue_frontend npm run test:unit -- --run
```

**Salida esperada:**
```
✓ src/test/components/Footer.spec.js (3 tests) ✅
✓ src/test/components/LoginForm.spec.js (3 tests) ✅
✓ src/test/components/Header.spec.js (3 tests) ✅

Test Files  3 passed (3)
Tests  9 passed (9) ✅
```

---

### 2️⃣ Pruebas de Integración/Automatizadas (6 tests)
**¿Qué prueban?** Flujos completos (Búsqueda, Navegación, Login, Carrito)

```powershell
# Ejecutar pruebas de integración
docker exec vue_frontend npm run test:automation -- --run
```

**Salida esperada:**
```
✓ src/test/automation/SearchFlow.spec.js (2 tests) ✅
✓ src/test/automation/LoginFlow.spec.js (1 test) ✅
✓ src/test/automation/NavigationFlow.spec.js (3 tests) ✅

Test Files  3 passed (3)
Tests  6 passed (6) ✅
```

---

### 3️⃣ Todas las Pruebas de Vitest (Unitarias + Integración)
```powershell
# Ejecutar todas las pruebas de Vitest
docker exec vue_frontend npm run test -- --run
```

**Salida esperada:**
```
Test Files  6 passed (6)
Tests  15 passed (15) ✅
Duration  ~5s
```

---

### 4️⃣ Pruebas con Cobertura de Código
```powershell
# Ejecutar con reporte de cobertura
docker exec vue_frontend npm run test:coverage -- --run
```

**Resultado:** Se genera un reporte en `front-vue/coverage/`

---

## 💻 Pruebas en tu PC Local

### Requisitos Previos
```powershell
# 1. Navegar al directorio del frontend
cd "C:\Users\richi\Desktop\2025_S1\Ingenieria de Software\sol-store-ecommerce\front-vue"

# 2. Instalar dependencias (solo primera vez)
npm install

# 3. Instalar navegadores de Playwright (solo primera vez)
npx playwright install
```

---

### 5️⃣ Pruebas E2E con Playwright (4 tests)
**¿Qué prueban?** Navegación real en navegador (Carga, Navegación, Responsive, Rendimiento)

#### Opción A: Configuración Simple (Solo Chromium - Recomendado)
```powershell
# Desde front-vue/
npx playwright test --config=playwright.config.simple.js
```

#### Opción B: Todos los Navegadores (Chromium, Firefox, Webkit)
```powershell
# Desde front-vue/
npx playwright test
```

#### Opción C: Con Interfaz Visual (UI Mode)
```powershell
# Desde front-vue/
npx playwright test --config=playwright.config.simple.js --ui
```

#### Opción D: Modo Debug (Paso a Paso)
```powershell
# Desde front-vue/
npx playwright test --config=playwright.config.simple.js --debug
```

**Salida esperada:**
```
✓ La aplicación carga correctamente (4.7s) ✅
✓ Navegación básica funciona (5.6s) ✅
✓ Responsive móvil (6.0s) ✅
✓ Rendimiento de carga (2.9s) ✅

4 passed (20.9s) ✅
```

---

## 🎯 Todas las Pruebas de una Vez

### Script PowerShell Completo
```powershell
# ================================================
# EJECUTAR TODOS LOS TESTS DEL PROYECTO
# ================================================

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "🧪 EJECUTANDO TODOS LOS TESTS" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# 1. Pruebas Unitarias en Docker
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "1️⃣  PRUEBAS UNITARIAS (Docker)" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
docker exec vue_frontend npm run test:unit -- --run

# 2. Pruebas de Integración en Docker
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "2️⃣  PRUEBAS DE INTEGRACIÓN (Docker)" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
docker exec vue_frontend npm run test:automation -- --run

# 3. Pruebas E2E en Local
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "3️⃣  PRUEBAS E2E PLAYWRIGHT (Local)" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Set-Location "C:\Users\richi\Desktop\2025_S1\Ingenieria de Software\sol-store-ecommerce\front-vue"
npx playwright test --config=playwright.config.simple.js
Set-Location ..

# Resumen Final
Write-Host ""
Write-Host "=====================================" -ForegroundColor Green
Write-Host "✅ TODOS LOS TESTS COMPLETADOS" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
```

**Guardar como:** `run-all-tests.ps1`

**Ejecutar:**
```powershell
.\run-all-tests.ps1
```

---

## 📊 Ver Reportes

### Reporte de Playwright (HTML Interactivo)
```powershell
# Desde front-vue/
npx playwright show-report
```
**Se abre en:** `http://localhost:9323`

**Incluye:**
- 📊 Dashboard con resultados
- 🎬 Videos de ejecución
- 📸 Screenshots
- 📝 Logs detallados
- 🔍 Timeline de eventos

---

### Reporte de Cobertura (Vitest)
```powershell
# Generar reporte
docker exec vue_frontend npm run test:coverage -- --run

# Ver el reporte (abre el HTML)
start front-vue/coverage/index.html
```

---

## 📁 Ubicación de Reportes

```
front-vue/
├── coverage/                    # Cobertura de código (Vitest)
│   └── index.html              # Reporte HTML
│
├── playwright-report/           # Reportes de Playwright
│   └── index.html              # Reporte interactivo
│
└── test-results/               # Resultados detallados
    ├── results.json            # Datos JSON
    └── [carpetas con screenshots y videos]
```

---

## 🎨 Comandos por Tipo

### Pruebas Unitarias
```powershell
# En Docker
docker exec vue_frontend npm run test:unit -- --run

# En modo watch (desarrollo)
docker exec vue_frontend npm run test:unit -- --watch
```

### Pruebas de Integración
```powershell
# En Docker
docker exec vue_frontend npm run test:automation -- --run

# En modo watch
docker exec vue_frontend npm run test:automation -- --watch
```

### Pruebas E2E (Playwright)
```powershell
# Simple (solo Chromium)
cd front-vue
npx playwright test --config=playwright.config.simple.js

# Todos los navegadores
npx playwright test

# Con UI
npx playwright test --ui

# Debug mode
npx playwright test --debug

# Solo una prueba específica
npx playwright test navigation.spec.js

# Solo tests que coincidan con un nombre
npx playwright test -g "responsive"
```

---

## 🔧 Solución de Problemas

### Problema: "docker exec" no funciona
```powershell
# Solución: Verificar que los contenedores estén corriendo
docker-compose up -d
docker ps
```

### Problema: "playwright not found"
```powershell
# Solución: Instalar dependencias
cd front-vue
npm install
npx playwright install
```

### Problema: "Connection refused" en Playwright
```powershell
# Solución: Verificar que el servidor esté disponible
curl http://localhost:5173

# Si no está disponible:
docker-compose up -d
```

### Problema: Las pruebas tardan mucho
```powershell
# Solución: Usar configuración simple (solo Chromium)
npx playwright test --config=playwright.config.simple.js

# O ejecutar en paralelo con más workers
npx playwright test --workers=4
```

### Problema: No puedo ver los reportes
```powershell
# Playwright:
cd front-vue
npx playwright show-report

# Cobertura:
start front-vue/coverage/index.html
```

---

## 📊 Resumen de Comandos Esenciales

### Desarrollo Diario (Más Usados)
```powershell
# 1. Pruebas unitarias rápidas
docker exec vue_frontend npm run test:unit -- --run

# 2. Pruebas de integración
docker exec vue_frontend npm run test:automation -- --run

# 3. Pruebas E2E con UI (para ver qué pasa)
cd front-vue
npx playwright test --config=playwright.config.simple.js --ui
```

### Antes de Hacer Commit
```powershell
# Ejecutar todo para asegurar que nada se rompió
docker exec vue_frontend npm run test -- --run
cd front-vue
npx playwright test --config=playwright.config.simple.js
cd ..
```

### Para CI/CD
```powershell
# Los workflows ya están configurados en:
# - .github/workflows/tests.yml (GitHub Actions)
# - .gitlab-ci.yml (GitLab CI)
```

---

## 📈 Estadísticas del Proyecto

```
╔═══════════════════════════════════════════════════╗
║  🎯 COBERTURA COMPLETA DE TESTING                ║
╠═══════════════════════════════════════════════════╣
║  📦 Pruebas Unitarias          9 tests  ✅       ║
║  🔄 Pruebas de Integración     6 tests  ✅       ║
║  🎭 Pruebas E2E (Playwright)   4 tests  ✅       ║
║  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  ║
║  📊 TOTAL:                    19 tests  ✅       ║
║  🎉 TASA DE ÉXITO:            100%      ✅       ║
╚═══════════════════════════════════════════════════╝
```

---

## 🎓 Tips Adicionales

### 1. Ejecutar solo tests modificados
```powershell
# Vitest detecta cambios automáticamente en modo watch
docker exec vue_frontend npm run test -- --watch
```

### 2. Filtrar tests por nombre
```powershell
# Solo tests con "Header" en el nombre
docker exec vue_frontend npm run test -- --run -t "Header"

# Playwright con filtro
npx playwright test -g "responsive"
```

### 3. Generar código de prueba automáticamente (Playwright)
```powershell
# Abre el navegador y graba tus acciones
npx playwright codegen http://localhost:5173
```

### 4. Ver trazas detalladas (Playwright)
```powershell
# Ejecutar con tracing activado
npx playwright test --trace on

# Ver las trazas
npx playwright show-trace test-results/.../trace.zip
```

---

## ✅ Checklist de Testing

Antes de hacer commit:
- [ ] Ejecutar pruebas unitarias: `docker exec vue_frontend npm run test:unit -- --run`
- [ ] Ejecutar pruebas de integración: `docker exec vue_frontend npm run test:automation -- --run`
- [ ] Ejecutar pruebas E2E: `npx playwright test --config=playwright.config.simple.js`
- [ ] Revisar que todas pasen (19/19)
- [ ] Verificar cobertura si es necesario

---

## 🎉 ¡Listo!

Ahora tienes todo lo necesario para ejecutar y verificar las pruebas del proyecto.

**Resumen de 3 comandos:**
1. `docker exec vue_frontend npm run test -- --run` (Unitarias + Integración)
2. `cd front-vue && npx playwright test --config=playwright.config.simple.js` (E2E)
3. `npx playwright show-report` (Ver reporte)

¡Happy Testing! 🚀
