# 🏗️ Arquitectura de API Centralizada

## 📊 Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────┐
│                     FRONTEND (Vue 3)                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Components   │  │    Stores    │  │ Composables  │     │
│  │              │  │              │  │              │     │
│  │ - Products   │  │ - cart.js    │  │ - useLogout  │     │
│  │ - Cart       │  │ - orders.js  │  │ - useAppInit │     │
│  │ - Auth       │  │ - addresses  │  │              │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
│         └──────────────────┼──────────────────┘              │
│                            ▼                                 │
│              ┌─────────────────────────┐                    │
│              │     src/http.js         │                    │
│              │  (Cliente HTTP Central) │                    │
│              │                         │                    │
│              │  ✓ JWT Interceptors     │                    │
│              │  ✓ Token Refresh        │                    │
│              │  ✓ Error Handling       │                    │
│              │  ✓ Base URL Config      │                    │
│              └───────────┬─────────────┘                    │
│                          │                                   │
│                          ▼                                   │
│              ┌─────────────────────────┐                    │
│              │   import.meta.env       │                    │
│              │ VITE_API_BASE_URL       │                    │
│              └───────────┬─────────────┘                    │
│                          │                                   │
└──────────────────────────┼───────────────────────────────────┘
                           │
                           │ HTTP Request
                           ▼
         ┌─────────────────────────────────┐
         │      ENTORNO / PROXY             │
         ├─────────────────────────────────┤
         │                                  │
         │  Docker (.env):                  │
         │  └─> http://localhost/api        │
         │      ↓                            │
         │  [NGINX PROXY:80]                │
         │      ↓                            │
         │  [Laravel Backend:8000]          │
         │                                  │
         │  Local (.env.local):             │
         │  └─> http://localhost:8000/api   │
         │      ↓                            │
         │  [Laravel Backend:8000]          │
         │                                  │
         │  Production (.env.production):   │
         │  └─> https://dominio.com/api     │
         │      ↓                            │
         │  [Production Server]             │
         │                                  │
         └─────────────────────────────────┘
                           │
                           ▼
         ┌─────────────────────────────────┐
         │    BACKEND (Laravel + API)       │
         ├─────────────────────────────────┤
         │                                  │
         │  Routes (/api):                  │
         │  ├─ /productos                   │
         │  ├─ /carrito                     │
         │  ├─ /pedidos                     │
         │  ├─ /direcciones                 │
         │  ├─ /register                    │
         │  ├─ /login                       │
         │  └─ /user                        │
         │                                  │
         │  Database: PostgreSQL            │
         │                                  │
         └──────────────────────────────────┘
```

---

## 🔄 Flujo de Request

### 1. Usuario hace una acción
```
Usuario → Click en "Ver Productos"
```

### 2. Componente Vue hace llamada
```javascript
// En FeaturedProducts.vue
import http from '@/http'
const response = await http.get('/productos')
```

### 3. Cliente HTTP procesa
```javascript
// src/http.js
1. Lee: import.meta.env.VITE_API_BASE_URL
2. Agrega token: Authorization: Bearer <token>
3. Construye URL completa: http://localhost/api/productos
4. Envía request
```

### 4. Según entorno, se redirige
```
Docker:
  http://localhost/api/productos
  ↓
  Nginx (puerto 80)
  ↓
  Laravel Backend (puerto 8000)

Local:
  http://localhost:8000/api/productos
  ↓
  Laravel Backend (puerto 8000)
```

### 5. Backend procesa y responde
```
Laravel recibe /api/productos
↓
Valida token JWT
↓
Consulta base de datos
↓
Retorna JSON con productos
```

### 6. Frontend recibe respuesta
```javascript
// FeaturedProducts.vue
products.value = response.data
// UI se actualiza automáticamente
```

---

## 🔐 Flujo de Autenticación

```
┌─────────────────────────────────────────────┐
│           Login del Usuario                  │
└─────────────────┬───────────────────────────┘
                  │
                  ▼
      ┌───────────────────────┐
      │ POST /login           │
      │ { email, password }   │
      └───────────┬───────────┘
                  │
                  ▼
      ┌───────────────────────┐
      │ Backend valida         │
      │ credenciales           │
      └───────────┬───────────┘
                  │
                  ▼
      ┌───────────────────────┐
      │ Retorna:               │
      │ - access_token         │
      │ - refresh_token        │
      │ - user data            │
      └───────────┬───────────┘
                  │
                  ▼
      ┌───────────────────────┐
      │ Frontend guarda en     │
      │ localStorage           │
      └───────────┬───────────┘
                  │
                  ▼
      ┌───────────────────────────────────┐
      │ http.js AUTOMÁTICAMENTE:          │
      │                                   │
      │ 1. Lee token de localStorage      │
      │ 2. Agrega a TODOS los requests:   │
      │    Authorization: Bearer <token>  │
      │                                   │
      │ 3. Si recibe 401:                 │
      │    - Intenta refresh              │
      │    - Si falla → logout            │
      └───────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

```
sol-store-ecommerce/
│
├── front-vue/
│   ├── .env                    ← Config Docker (puerto 80)
│   ├── .env.local              ← Config Local (puerto 8000)
│   ├── .env.production         ← Config Producción
│   ├── switch-env.sh           ← Script cambio de entorno
│   │
│   ├── src/
│   │   ├── http.js             ← ⭐ CLIENTE HTTP CENTRAL
│   │   ├── main.js             ← Inicialización
│   │   │
│   │   ├── components/         ← Componentes Vue
│   │   │   ├── SignUpForm.vue      (usa http)
│   │   │   └── Home/
│   │   │       ├── FeaturedProducts.vue    (usa http)
│   │   │       ├── FeaturedCategories.vue  (usa http)
│   │   │       └── FeaturedThemes.vue      (usa http)
│   │   │
│   │   ├── stores/             ← Stores Pinia
│   │   │   ├── cart.js             (usa http)
│   │   │   ├── orders.js           (usa http)
│   │   │   └── addresses.js        (usa http)
│   │   │
│   │   ├── composables/        ← Composables
│   │   │   ├── useLogout.js        (usa http)
│   │   │   └── useAppInit.js       (usa http)
│   │   │
│   │   └── views/              ← Views
│   │       └── FaqView.vue         (usa http)
│   │
│   └── DOCUMENTATION/
│       ├── INDEX.md                 ← Índice de docs
│       ├── QUICK_GUIDE.md           ← Guía rápida
│       ├── API_CONFIG.md            ← Doc técnica
│       ├── DEVELOPER_CHECKLIST.md   ← Checklist
│       ├── MIGRATION_SUMMARY.md     ← Resumen migración
│       └── IMPLEMENTATION_COMPLETE.md
│
├── taskcurso/                  ← Backend Laravel
│   └── routes/
│       └── api.php             ← Rutas API
│
├── docker/
│   └── nginx/
│       └── default.conf        ← Config proxy
│
└── docker-compose.yml          ← Orquestación
```

---

## ⚙️ Configuración por Entorno

### Desarrollo con Docker
```
Usuario → Frontend (Vue:5173)
            ↓
         .env lee: http://localhost/api
            ↓
         Nginx Proxy (puerto 80)
            ↓
         Laravel Backend (puerto 8000)
            ↓
         PostgreSQL (puerto 5432)
```

### Desarrollo Local (sin Docker)
```
Usuario → Frontend (Vue:5173)
            ↓
         .env.local lee: http://localhost:8000/api
            ↓
         Laravel Backend (puerto 8000)
            ↓
         PostgreSQL (puerto 5432)
```

### Producción
```
Usuario → CDN/Web Server
            ↓
         Frontend estático (build)
            ↓
         .env.production: https://dominio.com/api
            ↓
         API Server
            ↓
         Database
```

---

## 🔑 Variables de Entorno Clave

| Variable | Desarrollo Docker | Desarrollo Local | Producción |
|----------|-------------------|------------------|------------|
| `VITE_API_BASE_URL` | `http://localhost/api` | `http://localhost:8000/api` | `https://dominio.com/api` |
| Frontend Port | 5173 | 5173 | N/A (build) |
| Backend Port | 8000 (interno) | 8000 | 443 |
| Proxy | Nginx:80 | No | Load Balancer |

---

## 🚀 Puntos Clave de la Arquitectura

### ✅ Ventajas

1. **Separación de Concerns**
   - Frontend solo importa `http`
   - Configuración centralizada en un lugar

2. **Flexibilidad**
   - Cambio de entorno en segundos
   - Múltiples backends soportados

3. **Seguridad**
   - Tokens manejados automáticamente
   - Refresh automático
   - No hay tokens en el código

4. **Mantenibilidad**
   - Un solo archivo para actualizar
   - Código más limpio
   - Menos bugs

5. **Escalabilidad**
   - Fácil agregar nuevos entornos
   - Fácil agregar interceptores globales
   - Fácil cambiar de axios a otro cliente HTTP

### 🎯 Principios Aplicados

- **DRY** (Don't Repeat Yourself)
- **Single Source of Truth**
- **Configuration over Code**
- **Separation of Concerns**
- **Dependency Injection**

---

## 📊 Comparación Antes/Después

### Antes ❌
```
15 archivos → 15 URLs diferentes
         ↓
    Backend API
```
- Difícil de mantener
- Propenso a errores
- 5-10 minutos para cambiar

### Después ✅
```
1 archivo .env → 1 URL
         ↓
    http.js (interceptores)
         ↓
    Todos los archivos
         ↓
    Backend API
```
- Fácil de mantener
- A prueba de errores
- 10 segundos para cambiar

---

**Arquitectura implementada por:** Equipo Sol Store  
**Fecha:** Octubre 8, 2025  
**Versión:** 1.0
