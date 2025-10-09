# Configuración de API Base URL

Este proyecto usa una configuración centralizada para la URL base de la API del backend.

## 📁 Archivos de Configuración

### `/front-vue/src/http.js`
Cliente HTTP centralizado que maneja:
- URL base de la API (configurable via `.env`)
- Headers comunes (Content-Type, Accept)
- Autenticación JWT automática
- Refresh de tokens
- Manejo de errores 401

### Variables de Entorno

El proyecto usa diferentes archivos `.env` según el entorno:

#### `.env` - Desarrollo con Docker
```bash
VITE_API_BASE_URL=http://localhost/api
```
Usa Nginx como proxy (puerto 80)

#### `.env.local` - Desarrollo local sin Docker
```bash
VITE_API_BASE_URL=http://localhost:8000/api
```
Conexión directa al backend Laravel (puerto 8000)

#### `.env.production` - Producción
```bash
VITE_API_BASE_URL=https://tu-dominio.com/api
```
Tu dominio de producción

## 🔧 Uso

### En componentes Vue:
```javascript
import http from '@/http'

// GET request
const response = await http.get('/productos')

// POST request
const response = await http.post('/register', userData)

// PUT request
const response = await http.put('/productos/1', updatedData)

// DELETE request
const response = await http.delete('/productos/1')
```

### En Stores (Pinia):
```javascript
import http from '@/http'

export const useProductStore = defineStore('products', {
  actions: {
    async fetchProducts() {
      const response = await http.get('/productos')
      this.products = response.data
    }
  }
})
```

## ⚙️ Cambiar la URL del Backend

### Para desarrollo local (sin Docker):
1. Renombra `.env.local` o crea uno nuevo
2. Configura: `VITE_API_BASE_URL=http://localhost:8000/api`
3. Reinicia el servidor de desarrollo: `npm run dev`

### Para desarrollo con Docker:
1. Usa el archivo `.env` existente
2. Configurado en: `VITE_API_BASE_URL=http://localhost/api`

### Para producción:
1. Edita `.env.production`
2. Cambia: `VITE_API_BASE_URL=https://tu-dominio.com/api`
3. Construye: `npm run build`

## 🔑 Autenticación

El cliente HTTP automáticamente:
- Agrega el token JWT a cada request (header `Authorization: Bearer <token>`)
- Intenta refrescar el token si recibe un 401
- Redirige al login si el refresh falla
- Lee el token de `localStorage.getItem('access_token')`

## ✅ Beneficios

1. **Centralización**: Cambia la URL en un solo lugar (`.env`)
2. **Múltiples entornos**: Desarrollo, local, producción
3. **Manejo automático de tokens**: No necesitas agregar el token manualmente
4. **Manejo de errores**: Interceptores globales para 401, etc.
5. **Tipo seguro**: Usa el mismo cliente en toda la app

## 🚫 NO hacer:

```javascript
// ❌ NO uses axios directamente
import axios from 'axios'
const response = await axios.get('http://localhost:8000/api/productos')

// ✅ SÍ usa el cliente http
import http from '@/http'
const response = await http.get('/productos')
```

## 📝 Notas

- Las rutas en las llamadas NO deben incluir `/api`, ya está en la baseURL
- El archivo `http.js` también maneja `withCredentials` para CORS
- Usa `import.meta.env.VITE_API_BASE_URL` para acceder a la variable de entorno
