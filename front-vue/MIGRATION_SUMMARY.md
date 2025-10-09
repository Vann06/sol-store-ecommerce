# Centralización de API Base URL - Resumen de Cambios

## 🎯 Objetivo
Centralizar la configuración de la URL base de la API para facilitar cambios entre entornos (desarrollo, producción, local).

## ✅ Cambios Realizados

### 1. Configuración Base (`/front-vue/src/http.js`)
- ✅ Actualizado para usar `import.meta.env.VITE_API_BASE_URL`
- ✅ Exporta `BASE_URL` para uso directo si es necesario
- ✅ Mantiene interceptores JWT y manejo de errores

### 2. Variables de Entorno

#### `.env` (Docker con Nginx)
```bash
VITE_API_BASE_URL=http://localhost/api
```

#### `.env.local` (Desarrollo local - NUEVO)
```bash
VITE_API_BASE_URL=http://localhost:8000/api
```

#### `.env.example` (Plantilla)
```bash
VITE_API_BASE_URL=http://localhost/api
```

#### `.env.production` (Producción)
```bash
VITE_API_BASE_URL=https://tu-dominio.com/api
```

### 3. Stores Actualizados

#### ✅ `/stores/cart.js`
- Removido `const API_BASE_URL = 'http://localhost:8000/api'`
- Cambiado `import axios from 'axios'` → `import http from '@/http'`
- Todas las llamadas usan `http` en lugar de `axios`
- Rutas sin `/api` prefix (ej: `/carrito` en lugar de `/api/carrito`)

#### ✅ `/stores/orders.js`
- Cambiado `import axios from 'axios'` → `import http from '@/http'`
- Rutas actualizadas: `/pedidos`, `/orders/checkout`, etc.

#### ✅ `/stores/addresses.js`
- Cambiado `import axios from 'axios'` → `import http from '@/http'`
- Rutas actualizadas: `/direcciones`, etc.

### 4. Componentes Actualizados

#### ✅ `/components/SignUpForm.vue`
- Cambiado de `axios.post('http://localhost:8000/api/register')` → `http.post('/register')`

#### ✅ `/components/Home/FeaturedProducts.vue`
- Cambiado `axios.get('/api/productos/recientes')` → `http.get('/productos/recientes')`

#### ✅ `/components/Home/FeaturedCategories.vue`
- Cambiado `axios.get('/api/categorias')` → `http.get('/categorias')`

#### ✅ `/components/Home/FeaturedThemes.vue`
- Cambiado `axios.get('/api/tematicas')` → `http.get('/tematicas')`

### 5. Composables Actualizados

#### ✅ `/composables/useLogout.js`
- Cambiado `axios.post('http://localhost:8000/api/logout')` → `http.post('/logout')`
- Removido limpieza manual de headers (lo hace http.js)

#### ✅ `/composables/useAppInit.js`
- Cambiado `axios.get('http://localhost:8000/api/user')` → `http.get('/user')`
- Removida configuración manual de axios headers

### 6. Archivos Principales

#### ✅ `/src/main.js`
- Removida configuración manual de axios:
  ```javascript
  // ANTES:
  axios.defaults.baseURL = 'http://localhost:8000'
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  
  // AHORA:
  // El cliente http.js maneja todo automáticamente
  ```

### 7. Views Actualizadas

#### ✅ `/views/FaqView.vue`
- Cambiado `axios.get('/api/faqs')` → `http.get('/faqs')`

### 8. Utils Actualizados

#### ✅ `/utils/http.js`
- Actualizado para usar `import.meta.env.VITE_API_BASE_URL`
- Nota agregada indicando que es duplicado de `/src/http.js`

### 9. Documentación

#### ✅ `API_CONFIG.md` (NUEVO)
- Documentación completa de cómo usar la configuración
- Ejemplos de uso en componentes y stores
- Instrucciones para cambiar entre entornos
- Beneficios y mejores prácticas

## 🔄 Patrón de Migración

### Antes:
```javascript
import axios from 'axios'

// Opción 1: URL hardcoded
const response = await axios.get('http://localhost:8000/api/productos')

// Opción 2: URL relativa con axios configurado globalmente
const response = await axios.get('/api/productos')
```

### Después:
```javascript
import http from '@/http'

// Siempre usar http con rutas relativas SIN /api
const response = await http.get('/productos')
```

## 📝 Archivos NO Modificados (Legacy/Debug)

- `/components/CartDebug.vue` - Componente de debug
- `/components/basura/PingTest.vue` - Archivo de prueba
- `/stores/taskStore.js` - Store legacy
- `/test/automation/CartFlow.spec.js` - Tests (pueden necesitar actualización)

## 🚀 Próximos Pasos Sugeridos

1. **Ejecutar el proyecto** para verificar que todo funciona:
   ```bash
   cd front-vue
   npm run dev
   ```

2. **Revisar rutas del backend** en `docker/nginx/default.conf` para asegurar que el proxy está configurado correctamente

3. **Probar diferentes entornos**:
   - Con Docker: usar `.env`
   - Sin Docker: usar `.env.local`
   - Producción: actualizar `.env.production` con dominio real

4. **Actualizar tests** si es necesario para usar el nuevo patrón

5. **Eliminar archivos legacy** si ya no se usan:
   - `/utils/http.js` (si no es necesario)
   - Componentes en `/components/basura/`

## ⚠️ Notas Importantes

1. **Reiniciar servidor**: Después de cambiar archivos `.env`, reinicia el servidor de desarrollo
   ```bash
   npm run dev
   ```

2. **Docker**: Si usas Docker, reconstruye el contenedor si cambias configuración
   ```bash
   docker-compose down
   docker-compose up --build
   ```

3. **Rutas**: Las rutas NO deben incluir `/api` ya que está en la `baseURL`
   - ✅ Correcto: `http.get('/productos')`
   - ❌ Incorrecto: `http.get('/api/productos')`

4. **Tokens**: El cliente HTTP maneja automáticamente los tokens JWT, no necesitas configurarlos manualmente

## 📊 Estadísticas

- **Archivos modificados**: ~15
- **Stores actualizados**: 3
- **Componentes actualizados**: 4
- **Composables actualizados**: 2
- **Archivos de configuración nuevos**: 1 (`.env.local`)
- **Documentación creada**: 2 (`API_CONFIG.md`, este archivo)
