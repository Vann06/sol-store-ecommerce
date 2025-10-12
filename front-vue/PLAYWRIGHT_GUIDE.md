# 🎭 Guía de Pruebas E2E con Playwright

## 📋 Tabla de Contenidos
- [Estado Actual](#estado-actual)
- [Problema Identificado](#problema-identificado)
- [Soluciones](#soluciones)
- [Cómo Ejecutar las Pruebas](#cómo-ejecutar-las-pruebas)
- [Estructura de Pruebas](#estructura-de-pruebas)

---

## ✅ Estado Actual

### Pruebas Implementadas
El proyecto cuenta con pruebas E2E en:
- **Archivo:** `src/test/e2e/navigation.spec.js`
- **Pruebas:** 3 tests × 5 navegadores = 15 tests totales
  - ✓ Navegación desktop completa
  - ✓ Responsividad móvil
  - ✓ Rendimiento y carga de página

### Navegadores Configurados
- Chromium (Desktop Chrome)
- Firefox (Desktop Firefox)
- Webkit (Desktop Safari)
- Mobile Chrome (Pixel 5)
- Mobile Safari (iPhone 12)

---

## ⚠️ Problema Identificado

### Error Principal
```
Error: spawn /root/.cache/ms-playwright/chromium_headless_shell-1187/chrome-linux/headless_shell ENOENT
Error: env: can't execute 'bash': No such file or directory (webkit)
```

### Causa
El contenedor Docker usa **Alpine Linux** (node:18-alpine) que:
- No tiene las dependencias de sistema requeridas por Playwright
- No incluye `bash` (necesario para webkit)
- Falta `libc`, `libstdc++`, y otras librerías de sistema

---

## 🔧 Soluciones

### **Opción 1: Ejecutar Playwright FUERA de Docker (Recomendado para Desarrollo)**

#### Ventajas
✅ Configuración más simple
✅ Acceso a interfaz gráfica con `--ui`
✅ Mejor rendimiento
✅ Fácil debugging

#### Pasos
1. **Instalar dependencias localmente:**
   ```bash
   cd front-vue
   npm install
   ```

2. **Instalar navegadores de Playwright:**
   ```bash
   npx playwright install
   ```

3. **Asegurarte que el servidor esté corriendo:**
   ```bash
   # En Docker
   docker-compose up -d
   
   # O localmente
   npm run dev
   ```

4. **Ejecutar las pruebas:**
   ```bash
   # Modo headless (sin interfaz)
   npm run test:e2e
   
   # Modo UI (con interfaz visual)
   npm run test:e2e:ui
   
   # Solo Chromium (más rápido)
   npx playwright test --project=chromium
   ```

---

### **Opción 2: Contenedor Docker con Dependencias (Para CI/CD)**

#### Actualizar Dockerfile del Frontend
Cambiar de Alpine a Debian/Ubuntu:

```dockerfile
# Usar imagen base con más dependencias
FROM mcr.microsoft.com/playwright:v1.54.2-jammy

WORKDIR /app

# Copiar package files
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Instalar navegadores de Playwright
RUN npx playwright install --with-deps

# Copiar código
COPY . .

# Exponer puerto
EXPOSE 5173

# Comando
CMD ["npm", "run", "dev"]
```

**⚠️ Nota:** Esta imagen es MUY PESADA (~2GB) y solo se recomienda para CI/CD.

---

### **Opción 3: Configuración Simplificada (Solo Chromium)**

Para pruebas rápidas en Docker, puedes modificar `playwright.config.js`:

```javascript
// Configuración de proyectos/browsers - SOLO CHROMIUM
projects: [
  {
    name: 'chromium',
    use: { 
      ...devices['Desktop Chrome'],
      launchOptions: {
        executablePath: '/usr/bin/chromium-browser' // Si está instalado
      }
    },
  },
],
```

---

## 🚀 Cómo Ejecutar las Pruebas

### En tu Máquina Local (Recomendado)

```bash
# 1. Navegar al directorio
cd front-vue

# 2. Instalar dependencias (si no lo has hecho)
npm install

# 3. Instalar navegadores de Playwright
npx playwright install

# 4. Asegurar que el servidor esté corriendo
# Verifica que http://localhost:5173 esté accesible

# 5. Ejecutar pruebas E2E
npm run test:e2e

# 6. Ver resultados con interfaz
npm run test:e2e:ui

# 7. Solo un navegador específico
npx playwright test --project=chromium
npx playwright test --project=firefox
npx playwright test --project=webkit

# 8. Ver el reporte HTML
npx playwright show-report
```

### Desde Docker (Limitado)

```bash
# Las pruebas E2E NO funcionarán directamente en el contenedor actual
# debido a las dependencias faltantes

# Para pruebas unitarias y de integración (que SÍ funcionan):
docker exec vue_frontend npm run test:unit
docker exec vue_frontend npm run test:automation
```

---

## 📁 Estructura de Pruebas

```
front-vue/
├── src/
│   └── test/
│       ├── components/          # Pruebas Unitarias ✅
│       │   ├── Footer.spec.js
│       │   ├── Header.spec.js
│       │   └── LoginForm.spec.js
│       │
│       ├── automation/          # Pruebas de Integración ✅
│       │   ├── CartFlow.spec.js
│       │   ├── LoginFlow.spec.js
│       │   ├── NavigationFlow.spec.js
│       │   └── SearchFlow.spec.js
│       │
│       └── e2e/                 # Pruebas E2E (Playwright) ⚠️
│           └── navigation.spec.js
│
├── playwright.config.js         # Configuración de Playwright
├── playwright-report/           # Reportes HTML generados
└── test-results/                # Resultados JSON
```

---

## 📊 Comparación de Herramientas

| Herramienta | Ubicación | Estado | Ejecutar en Docker |
|-------------|-----------|--------|-------------------|
| **Vitest** (Unit) | `src/test/components/` | ✅ Funciona | ✅ Sí |
| **Vitest** (Integration) | `src/test/automation/` | ✅ Funciona | ✅ Sí |
| **Playwright** (E2E) | `src/test/e2e/` | ⚠️ Solo local | ❌ No (Alpine) |

---

## 🎯 Recomendaciones

### Para Desarrollo Local
✅ Usa Playwright en tu máquina local  
✅ Ejecuta pruebas unitarias/integración en Docker  
✅ Usa `npm run test:e2e:ui` para debugging visual

### Para CI/CD (GitHub Actions, GitLab CI, etc.)
✅ Usa la imagen oficial de Playwright: `mcr.microsoft.com/playwright`  
✅ Ejecuta pruebas en paralelo con múltiples navegadores  
✅ Genera reportes HTML como artefactos

### Para Entorno de Producción
✅ Mantén el contenedor Alpine (ligero) para desarrollo  
✅ Ejecuta Playwright en pipelines de CI/CD separados  
✅ No incluyas navegadores en imagen de producción

---

## 🐛 Debugging

### Ver las pruebas en modo debug
```bash
npx playwright test --debug
```

### Generar trazas
```bash
npx playwright test --trace on
```

### Inspeccionar selectores
```bash
npx playwright codegen http://localhost:5173
```

---

## 📝 Ejemplo de Prueba E2E

```javascript
import { test, expect } from '@playwright/test';

test('Usuario puede navegar a la página de productos', async ({ page }) => {
  // Ir a la página principal
  await page.goto('/');
  
  // Verificar que el header esté visible
  await expect(page.locator('header')).toBeVisible();
  
  // Click en enlace de productos
  await page.click('text=Productos');
  
  // Verificar navegación exitosa
  await expect(page).toHaveURL('/catalogo');
  
  // Verificar que se muestren productos
  const productos = page.locator('.producto-card');
  await expect(productos).toHaveCountGreaterThan(0);
});
```

---

## 🔗 Referencias

- [Playwright Documentation](https://playwright.dev)
- [Playwright en Docker](https://playwright.dev/docs/docker)
- [Best Practices](https://playwright.dev/docs/best-practices)
- [Debugging Tools](https://playwright.dev/docs/debug)

---

## ✅ Resumen Ejecutivo

| Aspecto | Estado |
|---------|--------|
| Pruebas Unitarias | ✅ 9/9 pasando |
| Pruebas de Integración | ✅ 6/6 pasando |
| Pruebas E2E (Playwright) | ⚠️ Requiere ejecución local |
| **Total Funcional** | **15/15 pruebas pasando** |

**Recomendación:** Ejecuta Playwright en tu máquina local para desarrollo. Para CI/CD, usa la imagen oficial de Playwright en tu pipeline.
