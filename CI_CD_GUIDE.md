# 🚀 Guía de CI/CD para Pruebas de Playwright

## 📋 Tabla de Contenidos
- [Visión General](#visión-general)
- [GitHub Actions](#github-actions)
- [GitLab CI](#gitlab-ci)
- [Otras Plataformas](#otras-plataformas)
- [Configuración](#configuración)
- [Solución de Problemas](#solución-de-problemas)

---

## 🎯 Visión General

### El Problema
- ❌ Playwright NO funciona en Alpine Linux (imagen ligera)
- ❌ Requiere dependencias de sistema (bash, librerías)
- ❌ Navegadores necesitan ~2GB de espacio

### La Solución
- ✅ Usar imagen oficial de Microsoft: `mcr.microsoft.com/playwright:v1.54.2-jammy`
- ✅ Ejecutar pruebas E2E solo en CI/CD
- ✅ Mantener imagen Alpine para desarrollo

---

## 🐙 GitHub Actions

### Archivos Creados
```
.github/
└── workflows/
    └── tests.yml        ← Workflow principal
```

### ¿Qué hace?
1. **Job 1:** Pruebas Unitarias e Integración (Alpine - rápido)
2. **Job 2:** Pruebas E2E con Playwright (imagen especial)
3. **Job 3:** Resumen y reportes

### Características
- ✅ Ejecución en paralelo
- ✅ Reportes HTML descargables
- ✅ Cobertura de código
- ✅ Retry automático en fallos
- ✅ Caché de dependencias

### Cómo activarlo
1. **Subir a GitHub:**
   ```bash
   git add .github/workflows/tests.yml
   git commit -m "Add CI/CD pipeline with Playwright"
   git push origin main
   ```

2. **Ver resultados:**
   - Ve a: `https://github.com/Vann06/sol-store-ecommerce/actions`
   - Click en el último workflow
   - Descarga los artifacts (reportes)

### Variables de Entorno
Configura en GitHub Settings → Secrets:
```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_DATABASE=ecommerce_db
DB_USERNAME=admin
DB_PASSWORD=admin123
```

---

## 🦊 GitLab CI

### Archivos Creados
```
.gitlab-ci.yml           ← Pipeline principal
```

### ¿Qué hace?
1. **Stage 1:** Instalación de dependencias
2. **Stage 2:** Pruebas unitarias e integración
3. **Stage 3:** Pruebas E2E con Playwright
4. **Stage 4:** Reporte final

### Características
- ✅ Caché inteligente
- ✅ Reportes JUnit
- ✅ Artifacts con retención de 30 días
- ✅ Ejecución paralela

### Cómo activarlo
1. **Subir a GitLab:**
   ```bash
   git add .gitlab-ci.yml
   git commit -m "Add GitLab CI/CD with Playwright"
   git push origin main
   ```

2. **Ver resultados:**
   - Ve a: CI/CD → Pipelines
   - Click en el último pipeline
   - Descarga los artifacts

### Variables de Entorno
Configura en Settings → CI/CD → Variables:
```
DB_CONNECTION: pgsql
DB_HOST: postgres
DB_DATABASE: ecommerce_db
DB_USERNAME: admin
DB_PASSWORD: admin123
```

---

## 🔧 Otras Plataformas CI/CD

### Jenkins
```groovy
pipeline {
    agent {
        docker {
            image 'mcr.microsoft.com/playwright:v1.54.2-jammy'
        }
    }
    stages {
        stage('Install') {
            steps {
                sh 'cd front-vue && npm ci'
            }
        }
        stage('E2E Tests') {
            steps {
                sh 'cd front-vue && npx playwright test'
            }
        }
    }
    post {
        always {
            publishHTML([
                allowMissing: false,
                alwaysLinkToLastBuild: true,
                keepAll: true,
                reportDir: 'front-vue/playwright-report',
                reportFiles: 'index.html',
                reportName: 'Playwright Report'
            ])
        }
    }
}
```

### CircleCI
```yaml
version: 2.1

jobs:
  e2e-tests:
    docker:
      - image: mcr.microsoft.com/playwright:v1.54.2-jammy
    steps:
      - checkout
      - run:
          name: Install dependencies
          command: cd front-vue && npm ci
      - run:
          name: Run Playwright tests
          command: cd front-vue && npx playwright test
      - store_artifacts:
          path: front-vue/playwright-report
          destination: playwright-report

workflows:
  version: 2
  test:
    jobs:
      - e2e-tests
```

### Travis CI
```yaml
language: node_js
node_js:
  - 18

services:
  - docker

before_install:
  - docker pull mcr.microsoft.com/playwright:v1.54.2-jammy

script:
  - docker run -v $PWD:/work -w /work/front-vue 
    mcr.microsoft.com/playwright:v1.54.2-jammy 
    sh -c "npm ci && npx playwright test"

after_script:
  - ls -la front-vue/playwright-report
```

---

## 🛠️ Configuración Detallada

### 1. Imagen de Playwright

#### ¿Por qué esta imagen?
```dockerfile
FROM mcr.microsoft.com/playwright:v1.54.2-jammy
```

**Ventajas:**
- ✅ Incluye Chromium, Firefox y Webkit
- ✅ Todas las dependencias pre-instaladas
- ✅ Mantenida por Microsoft
- ✅ Compatible con Ubuntu 22.04 (Jammy)

**Desventajas:**
- ⚠️ Tamaño: ~2GB
- ⚠️ No apta para producción

### 2. Configuración de Playwright para CI

Actualiza `playwright.config.js`:

```javascript
export default defineConfig({
  // Configuración específica para CI
  use: {
    baseURL: process.env.CI 
      ? 'http://localhost:5173' 
      : 'http://localhost:5173',
    
    // Más tiempo en CI
    actionTimeout: process.env.CI ? 10000 : 5000,
    
    // Screenshots solo en CI
    screenshot: process.env.CI ? 'on' : 'only-on-failure',
    
    // Videos solo en fallos
    video: 'retain-on-failure',
    
    // Trazas en CI
    trace: process.env.CI ? 'on' : 'on-first-retry',
  },
  
  // Más retries en CI
  retries: process.env.CI ? 2 : 0,
  
  // Menos workers en CI
  workers: process.env.CI ? 2 : undefined,
  
  // Reporter para CI
  reporter: process.env.CI 
    ? [['html'], ['junit', { outputFile: 'test-results/results.xml' }]]
    : [['html'], ['list']],
});
```

### 3. Esperar a que los servicios estén listos

En CI/CD, necesitas esperar a que los servicios se inicien:

```yaml
# GitHub Actions
- name: Wait for services
  run: |
    timeout 60 bash -c 'until curl -f http://localhost:5173; do sleep 2; done'
    timeout 60 bash -c 'until curl -f http://localhost:8000/api/health; do sleep 2; done'
```

### 4. Variables de Entorno

```yaml
# Ejemplo para GitHub Actions
env:
  CI: true
  NODE_ENV: test
  VITE_API_URL: http://localhost:8000/api
```

---

## 🐛 Solución de Problemas

### Error: "Browser not found"
```yaml
# Asegúrate de instalar navegadores
- run: npx playwright install --with-deps
```

### Error: "Connection refused"
```yaml
# Espera a que los servicios estén listos
- run: |
    timeout 60 bash -c 'until curl -f http://localhost:5173; do sleep 2; done'
```

### Error: "Permission denied"
```yaml
# En GitLab CI, usa el usuario correcto
container:
  image: mcr.microsoft.com/playwright:v1.54.2-jammy
  options: --user 1001
```

### Timeout en pruebas
```javascript
// Aumenta los timeouts en playwright.config.js
export default defineConfig({
  timeout: 30000, // 30 segundos
  use: {
    actionTimeout: 10000, // 10 segundos por acción
  },
});
```

### Falta de memoria
```yaml
# En GitHub Actions, usa ubuntu-latest
runs-on: ubuntu-latest

# En GitLab CI, aumenta memoria
variables:
  DOCKER_DRIVER: overlay2
  PLAYWRIGHT_BROWSERS_PATH: 0
```

---

## 📊 Reportes y Artifacts

### GitHub Actions
Los reportes se guardan automáticamente como **Artifacts**:

1. Ve a: Actions → Tu workflow
2. Scroll down hasta "Artifacts"
3. Descarga:
   - `playwright-report` (HTML interactivo)
   - `playwright-results` (JSON con detalles)
   - `coverage-report` (Cobertura de código)

### GitLab CI
Los artifacts están en:

1. Ve a: CI/CD → Pipelines → Tu pipeline
2. Click en el botón de descarga
3. Descomprime los artifacts

### Ver Reportes Localmente
```bash
# Descargar artifacts
# Luego descomprimir y:
cd playwright-report
python -m http.server 8080

# O con npx:
npx playwright show-report
```

---

## 🎯 Checklist de Implementación

### Paso 1: Archivos creados ✅
- [x] `.github/workflows/tests.yml`
- [x] `.gitlab-ci.yml`
- [x] `docker-compose.ci.yml`
- [x] `docker/frontend/Dockerfile.ci`

### Paso 2: Configurar repositorio
- [ ] Subir archivos a Git
- [ ] Configurar secrets/variables
- [ ] Activar CI/CD en la plataforma

### Paso 3: Primera ejecución
- [ ] Push a main/develop
- [ ] Verificar que el pipeline se ejecuta
- [ ] Revisar logs si hay errores
- [ ] Descargar reportes

### Paso 4: Optimización
- [ ] Ajustar timeouts si es necesario
- [ ] Configurar notificaciones
- [ ] Habilitar caché
- [ ] Agregar badges al README

---

## 📈 Métricas y Badges

### GitHub Actions Badge
Agrega al README.md:
```markdown
[![Tests](https://github.com/Vann06/sol-store-ecommerce/workflows/Tests/badge.svg)](https://github.com/Vann06/sol-store-ecommerce/actions)
```

### GitLab CI Badge
```markdown
[![Pipeline](https://gitlab.com/your-user/sol-store-ecommerce/badges/main/pipeline.svg)](https://gitlab.com/your-user/sol-store-ecommerce/pipelines)
```

---

## 🎉 Resumen

### ✅ Lo que hicimos:
1. Creamos workflow para **GitHub Actions**
2. Creamos pipeline para **GitLab CI**
3. Dockerfile específico para CI/CD
4. Docker Compose para testing
5. Configuración optimizada

### 🚀 Próximos pasos:
1. **Subir archivos a Git**
2. **Configurar secrets**
3. **Hacer push y ver el pipeline**
4. **Descargar reportes**

### 💡 Recuerda:
- ✅ Pruebas unitarias: Alpine (rápido)
- ✅ Pruebas E2E: Imagen Playwright (pesada)
- ✅ Desarrollo local: Sin Playwright en Docker
- ✅ CI/CD: Playwright completo

---

## 📞 Soporte

¿Problemas? Revisa:
1. Logs del pipeline
2. Variables de entorno
3. Timeouts
4. Versión de la imagen de Playwright

¡Todo listo para CI/CD! 🎉
