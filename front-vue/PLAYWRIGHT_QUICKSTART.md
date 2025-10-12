# 🚀 Inicio Rápido - Pruebas de Playwright

## ⚡ Ejecuta las pruebas AHORA mismo

### 1️⃣ Instalar Playwright (solo una vez)
```powershell
cd front-vue
npm run test:install
```

### 2️⃣ Verificar que el servidor esté corriendo
```powershell
# Verificar en el navegador:
# http://localhost:5173 debe estar accesible
```

### 3️⃣ Ejecutar las pruebas E2E

#### Opción A: Modo Simple (Solo Chromium - Recomendado)
```powershell
npm run test:e2e:simple
```

#### Opción B: Modo Simple con Interfaz Visual
```powershell
npm run test:e2e:simple:ui
```

#### Opción C: Solo Chromium (Configuración completa)
```powershell
npm run test:e2e:chromium
```

#### Opción D: Todos los navegadores (Tarda más)
```powershell
npm run test:e2e
```

#### Opción E: Modo Debug (paso a paso)
```powershell
npm run test:e2e:debug
```

### 4️⃣ Ver el reporte HTML
```powershell
npm run test:e2e:report
```

---

## 📊 Nuevos Comandos Disponibles

| Comando | Descripción |
|---------|-------------|
| `npm run test:e2e:simple` | Solo Chromium, configuración simple |
| `npm run test:e2e:simple:ui` | Con interfaz visual |
| `npm run test:e2e:chromium` | Solo Chromium, config completa |
| `npm run test:e2e:debug` | Modo debugging paso a paso |
| `npm run test:e2e:report` | Ver reporte HTML |
| `npm run test:e2e` | Todos los navegadores |
| `npm run test:e2e:ui` | Todos con interfaz |

---

## 🎯 Ejemplos de Uso

### Ejecutar una prueba específica
```powershell
npx playwright test navigation.spec.js
```

### Ejecutar solo una prueba por nombre
```powershell
npx playwright test -g "Navegación desktop"
```

### Ver en modo headed (con navegador visible)
```powershell
npx playwright test --headed
```

### Generar código de prueba
```powershell
npx playwright codegen http://localhost:5173
```

---

## ✅ Estado de Pruebas

### Funcionando en Docker
✅ Pruebas Unitarias: `docker exec vue_frontend npm run test:unit`
✅ Pruebas de Integración: `docker exec vue_frontend npm run test:automation`

### Funcionando Localmente
✅ Pruebas E2E: `npm run test:e2e:simple` (en tu máquina)

---

## 🐛 Solución de Problemas

### Error: "Cannot find module '@playwright/test'"
```powershell
npm install
```

### Error: "Browser was not installed"
```powershell
npm run test:install
```

### Error: "net::ERR_CONNECTION_REFUSED"
```powershell
# Verifica que el servidor esté corriendo:
docker ps
# O inicia con:
docker-compose up -d
```

### Las pruebas fallan por timeout
```powershell
# Usa la configuración simple (timeout más largo):
npm run test:e2e:simple
```

---

## 📝 Resumen de 3 pasos

1. **Instala:** `npm run test:install`
2. **Verifica:** `http://localhost:5173` esté accesible
3. **Ejecuta:** `npm run test:e2e:simple:ui`

¡Listo! 🎉
