# 📋 Checklist para Desarrolladores

## ✅ HACER (Buenas Prácticas)

### 1. Importar el Cliente HTTP Centralizado
```javascript
✅ import http from '@/http'
❌ import axios from 'axios'
```

### 2. Usar Rutas Relativas SIN /api
```javascript
✅ await http.get('/productos')
✅ await http.post('/register', data)
❌ await http.get('/api/productos')          // /api ya está en baseURL
❌ await http.get('http://localhost:8000/api/productos')  // URL completa
```

### 3. No Configurar Headers Manualmente
```javascript
✅ await http.get('/user')  // Token se agrega automáticamente
❌ await http.get('/user', {
     headers: { Authorization: `Bearer ${token}` }  // Innecesario
   })
```

### 4. Usar Variables de Entorno para Configuración
```javascript
✅ const apiUrl = import.meta.env.VITE_API_BASE_URL
❌ const apiUrl = 'http://localhost:8000/api'
```

### 5. Cambiar Entornos con el Script
```bash
✅ ./switch-env.sh docker
❌ Editar múltiples archivos manualmente
```

---

## ❌ NO HACER (Anti-patrones)

### 1. NO uses axios directamente
```javascript
❌ import axios from 'axios'
   const response = await axios.get('http://localhost:8000/api/productos')

✅ import http from '@/http'
   const response = await http.get('/productos')
```

### 2. NO hardcodees URLs
```javascript
❌ const API_URL = 'http://localhost:8000/api'
   axios.get(`${API_URL}/productos`)

✅ import http from '@/http'
   http.get('/productos')
```

### 3. NO configures axios.defaults
```javascript
❌ axios.defaults.baseURL = 'http://localhost:8000'
   axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

✅ // http.js ya lo hace automáticamente
```

### 4. NO dupliques configuración de cliente HTTP
```javascript
❌ // En cada archivo
   const api = axios.create({
     baseURL: 'http://localhost:8000/api',
     headers: { ... }
   })

✅ import http from '@/http'
```

### 5. NO olvides reiniciar después de cambiar .env
```bash
❌ # Cambiar .env y continuar trabajando
✅ # Cambiar .env y reiniciar:
   Ctrl+C
   npm run dev
```

---

## 🔍 Revisión de Pull Requests

Al revisar código, asegúrate que:

- [ ] Usa `import http from '@/http'` en lugar de axios
- [ ] Las rutas NO incluyen `/api` prefix
- [ ] NO hay URLs hardcoded
- [ ] NO configura headers de autenticación manualmente
- [ ] NO hay nuevas instancias de axios.create()

---

## 📝 Plantillas de Código

### Componente Vue con API Call
```vue
<script setup>
import { ref, onMounted } from 'vue'
import http from '@/http'

const data = ref([])
const loading = ref(false)
const error = ref(null)

const fetchData = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await http.get('/endpoint')
    data.value = response.data
  } catch (err) {
    error.value = err.message
    console.error('Error fetching data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>
```

### Store de Pinia con API Calls
```javascript
import { defineStore } from 'pinia'
import http from '@/http'

export const useMyStore = defineStore('myStore', {
  state: () => ({
    items: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchItems() {
      this.loading = true
      this.error = null
      try {
        const response = await http.get('/items')
        this.items = response.data
        return { success: true }
      } catch (error) {
        this.error = error.message
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async createItem(itemData) {
      try {
        const response = await http.post('/items', itemData)
        this.items.push(response.data)
        return { success: true, data: response.data }
      } catch (error) {
        return { success: false, error: error.message }
      }
    }
  }
})
```

### Composable con API Call
```javascript
import { ref } from 'vue'
import http from '@/http'

export function useDataFetch(endpoint) {
  const data = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetch = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await http.get(endpoint)
      data.value = response.data
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  return {
    data,
    loading,
    error,
    fetch
  }
}
```

---

## 🚨 Errores Comunes y Soluciones

### Error: "Network Error" o 404
```javascript
// Problema: Ruta incorrecta
❌ await http.get('/api/productos')  // /api/api/productos

// Solución: Remover /api
✅ await http.get('/productos')
```

### Error: "Token not found" / 401
```javascript
// Problema: Token no se está leyendo
❌ localStorage.getItem('token')

// Solución: Usar el key correcto
✅ localStorage.getItem('access_token')
```

### Error: Cambios en .env no se aplican
```bash
# Problema: No reiniciar el servidor
❌ Cambiar .env y continuar

# Solución: Reiniciar
✅ Ctrl+C → npm run dev
```

### Error: CORS issues
```javascript
// Problema: URL incorrecta o backend no corriendo
// Verificar:
1. Backend está corriendo
2. URL en .env es correcta
3. Backend tiene CORS configurado
4. Nginx proxy funciona (si usas Docker)
```

---

## 📚 Recursos

- 📖 [API_CONFIG.md](./API_CONFIG.md) - Documentación completa
- 🚀 [QUICK_GUIDE.md](./QUICK_GUIDE.md) - Guía rápida visual
- 📋 [MIGRATION_SUMMARY.md](./MIGRATION_SUMMARY.md) - Resumen de migración
- ⚠️ [PENDING_MIGRATION.md](./PENDING_MIGRATION.md) - Archivos pendientes

---

## 🎯 Regla de Oro

> **Si necesitas hacer una llamada HTTP a la API, SIEMPRE usa:**
> ```javascript
> import http from '@/http'
> ```

---

## ✅ Antes de Hacer Commit

- [ ] Importé `http` en lugar de `axios`
- [ ] Las rutas no incluyen `/api`
- [ ] No hay URLs hardcoded
- [ ] Probé en mi entorno local
- [ ] El código sigue las plantillas de arriba

---

**¿Dudas?** Consulta [API_CONFIG.md](./API_CONFIG.md) o pregunta al equipo.
