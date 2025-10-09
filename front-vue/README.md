# Sol Store E-Commerce - Frontend Vue 3

Frontend de la tienda de impresión 3D Sol Store, construido con Vue 3 + Vite.

## 🚀 Inicio Rápido

### Instalación

```sh
npm install
```

### Desarrollo

#### Con Docker (Recomendado)
```sh
# El frontend ya está configurado en docker-compose.yml
# Solo necesitas:
docker-compose up frontend
```
Abre: http://localhost:5173

#### Sin Docker (Local)
```sh
# Cambiar a entorno local
./switch-env.sh local

# Iniciar servidor de desarrollo
npm run dev
```
Abre: http://localhost:5173

### Producción

```sh
npm run build
```

## 🔧 Configuración de API

Este proyecto usa una **URL base centralizada** para todas las llamadas a la API.

### Archivos de Entorno

- **`.env`** - Docker con Nginx (puerto 80)
- **`.env.local`** - Desarrollo local (puerto 8000)
- **`.env.production`** - Producción

### Cambiar entre Entornos

```sh
# Desarrollo con Docker
./switch-env.sh docker

# Desarrollo local (sin Docker)
./switch-env.sh local

# Producción
./switch-env.sh production
```

### Configuración Manual

Edita el archivo `.env` correspondiente:

```bash
VITE_API_BASE_URL=http://localhost/api
```

**Importante**: Después de cambiar el `.env`, reinicia el servidor:
```sh
npm run dev
```

📖 **Documentación completa**: Ver [API_CONFIG.md](./API_CONFIG.md)

## 📁 Estructura del Proyecto

```
src/
├── assets/          # Imágenes, CSS, recursos estáticos
├── components/      # Componentes Vue reutilizables
├── composables/     # Composables de Vue 3
├── plugins/         # Plugins (ej: Clarity)
├── router/          # Configuración de Vue Router
├── stores/          # Stores de Pinia (estado global)
├── utils/           # Funciones utilitarias
├── views/           # Vistas/páginas principales
├── http.js          # ⭐ Cliente HTTP centralizado
├── main.js          # Punto de entrada
└── App.vue          # Componente raíz
```

## 🔑 Cliente HTTP Centralizado

Todas las llamadas a la API deben usar el cliente HTTP centralizado:

```javascript
import http from '@/http'

// GET
const response = await http.get('/productos')

// POST
const response = await http.post('/register', userData)

// PUT
const response = await http.put('/productos/1', data)

// DELETE
const response = await http.delete('/productos/1')
```

**Beneficios:**
- ✅ URL base centralizada
- ✅ Autenticación JWT automática
- ✅ Manejo de errores global
- ✅ Refresh automático de tokens
- ✅ Fácil cambio entre entornos

## 🛠️ Tecnologías

- **Vue 3** - Framework JavaScript progresivo
- **Vite** - Build tool rápido
- **Pinia** - Estado global
- **Vue Router** - Enrutamiento
- **Axios** - Cliente HTTP
- **Microsoft Clarity** - Analytics

## 📚 IDE Recomendado

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar)

## 🔗 Enlaces Útiles

- [Documentación Vue 3](https://vuejs.org/)
- [Documentación Vite](https://vite.dev/)
- [Documentación Pinia](https://pinia.vuejs.org/)
- [API Config](./API_CONFIG.md) - Configuración de API
- [Migration Summary](./MIGRATION_SUMMARY.md) - Resumen de cambios

## 📝 Scripts Disponibles

```sh
npm run dev          # Servidor de desarrollo
npm run build        # Build de producción
npm run preview      # Preview del build
./switch-env.sh      # Cambiar entre entornos
```

## 🐛 Debugging

Si tienes problemas de conexión con la API:

1. Verifica el archivo `.env` activo
2. Revisa que la URL base sea correcta
3. Asegúrate que el backend esté corriendo
4. Revisa la consola del navegador para errores

```sh
# Ver configuración actual
cat .env | grep VITE_API_BASE_URL
```
