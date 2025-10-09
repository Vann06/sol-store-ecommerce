# 🚀 Guía Rápida - Cambio de API Base URL

## ¿Qué problema resolvemos?

### ❌ ANTES (Problema)

Cada archivo tenía URLs hardcoded diferentes:

```javascript
// Archivo 1
axios.get('http://localhost:8000/api/productos')

// Archivo 2  
axios.post('http://localhost:8000/api/register', data)

// Archivo 3
const API_BASE_URL = 'http://localhost:8000/api'
axios.get(`${API_BASE_URL}/carrito`)
```

**Problemas:**
- 😩 Difícil cambiar URL (había que buscar en muchos archivos)
- 🐛 URLs inconsistentes
- 🔧 Configuración manual de tokens en cada archivo
- ⚠️ Errores al olvidar actualizar alguna URL

---

## ✅ DESPUÉS (Solución)

Todo centralizado en un solo lugar:

### 1. Configuración Centralizada

**Archivo: `/front-vue/.env`**
```bash
VITE_API_BASE_URL=http://localhost/api
```

### 2. Cliente HTTP Único

**Archivo: `/front-vue/src/http.js`**
```javascript
const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

const http = axios.create({
  baseURL: BASE_URL,
  // ... configuración automática de tokens
})
```

### 3. Uso Consistente

**En TODOS los archivos:**
```javascript
import http from '@/http'

// ✅ Así se usa ahora
const response = await http.get('/productos')
const response = await http.post('/register', data)
const response = await http.get('/carrito')
```

---

## 🎯 Cambio de Entorno en 3 Pasos

### Opción 1: Script Automático (Recomendado)

```bash
# Paso 1: Elegir entorno
./switch-env.sh docker   # o local, o production

# Paso 2: Reiniciar servidor
npm run dev

# ✅ ¡Listo!
```

### Opción 2: Manual

```bash
# Paso 1: Editar .env
nano .env

# Paso 2: Cambiar la URL
VITE_API_BASE_URL=http://tu-nueva-url/api

# Paso 3: Reiniciar
npm run dev
```

---

## 📋 Casos de Uso Comunes

### Caso 1: Desarrollo con Docker
```bash
./switch-env.sh docker
npm run dev
```
→ API: `http://localhost/api` (Nginx proxy)

### Caso 2: Desarrollo Local (sin Docker)
```bash
./switch-env.sh local
npm run dev
```
→ API: `http://localhost:8000/api` (Laravel directo)

### Caso 3: Producción
```bash
./switch-env.sh production
npm run build
```
→ API: `https://tu-dominio.com/api`

### Caso 4: Servidor de Pruebas
```bash
# Editar .env manualmente
echo "VITE_API_BASE_URL=http://192.168.1.100:8000/api" > .env
npm run dev
```

---

## 🔍 Verificación

### Ver configuración actual:
```bash
cat .env | grep VITE_API_BASE_URL
```

### Ver en el navegador (consola):
```javascript
// Pega esto en la consola del navegador
console.log(import.meta.env.VITE_API_BASE_URL)
```

---

## 💡 Ventajas

| Antes | Después |
|-------|---------|
| 😩 Buscar en 15+ archivos | ✅ Cambiar 1 archivo |
| 🐛 URLs inconsistentes | ✅ Una sola fuente de verdad |
| 🔧 Configurar tokens manualmente | ✅ Automático en cada request |
| ⚠️ Olvidar actualizar algún archivo | ✅ Imposible olvidar |
| 🕐 5-10 minutos de cambio | ✅ 10 segundos |

---

## 🆘 Troubleshooting

### Problema: Los cambios no se aplican
```bash
# Solución: Reiniciar el servidor
Ctrl + C
npm run dev
```

### Problema: Error 404 Not Found
```bash
# Verificar que el backend esté corriendo
curl http://localhost:8000/api/productos

# Si no responde, iniciar backend:
docker-compose up backend
```

### Problema: Error CORS
```bash
# Verificar configuración en backend Laravel
# Archivo: taskcurso/config/cors.php
```

### Problema: No encuentra el módulo @/http
```bash
# Verificar que el archivo existe
ls -la src/http.js

# Si no existe, revisar MIGRATION_SUMMARY.md
```

---

## 📖 Más Información

- 📄 [API_CONFIG.md](./API_CONFIG.md) - Documentación completa
- 📋 [MIGRATION_SUMMARY.md](./MIGRATION_SUMMARY.md) - Resumen de cambios
- 📚 [README.md](./README.md) - README del proyecto

---

## 🎉 Ejemplo Completo

### Antes (15 archivos diferentes):
```javascript
// stores/cart.js
const API_BASE_URL = 'http://localhost:8000/api'
axios.get(`${API_BASE_URL}/carrito`)

// components/SignUpForm.vue
axios.post('http://localhost:8000/api/register', data)

// components/Home/FeaturedProducts.vue
axios.get('/api/productos/recientes')

// ... y 12 archivos más con URLs diferentes
```

### Después (todos usan lo mismo):
```javascript
// ✅ Todos los archivos usan esto:
import http from '@/http'
await http.get('/carrito')
await http.post('/register', data)
await http.get('/productos/recientes')

// 🎯 Para cambiar URL, solo editar .env:
VITE_API_BASE_URL=http://nueva-url/api
```

---

**¿Preguntas?** Ver documentación completa en [API_CONFIG.md](./API_CONFIG.md)
