# Microsoft Clarity - Documentación de Implementación

## 📋 **Resumen**

Microsoft Clarity está integrado en el proyecto SOL Store siguiendo las mejores prácticas de desarrollo. La implementación incluye tracking automático y manual de eventos, debugging en desarrollo, y configuración flexible para diferentes entornos.

## 🚀 **Configuración**

### Variables de Entorno

```env
# Archivo: .env (desarrollo)
VITE_CLARITY_PROJECT_ID=t62uyfc02j
VITE_CLARITY_ENABLED=true

# Archivo: .env.production (producción)
VITE_CLARITY_PROJECT_ID=tu_project_id_produccion
VITE_CLARITY_ENABLED=true
```

### Obtener Project ID

1. Ir a [clarity.microsoft.com](https://clarity.microsoft.com)
2. Crear cuenta/iniciar sesión
3. Crear nuevo proyecto "SOL Store"
4. Copiar el Project ID generado
5. Reemplazar `t62uyfc02j` con tu Project ID real

## 📁 **Archivos Creados/Modificados**

```
front-vue/
├── .env                              # ✅ Actualizado con variables de Clarity
├── .env.production                   # 🆕 Creado para producción
├── src/
│   ├── App.vue                       # ✅ Integrado tracking de páginas + debug
│   ├── main.js                       # ✅ Agregado plugin de Clarity
│   ├── composables/
│   │   └── useClarity.js             # 🆕 Composable principal de Clarity
│   ├── plugins/
│   │   └── clarity.js                # 🆕 Plugin global de Vue
│   ├── components/
│   │   └── ClarityDebug.vue          # 🆕 Componente debug (solo desarrollo)
│   └── stores/
│       ├── user.js                   # ✅ Agregado tracking de login/logout
│       └── cart.js                   # ✅ Agregado tracking de carrito
```

## 🔧 **Características Implementadas**

### ✅ Tracking Automático
- **Page Views**: Cada cambio de ruta
- **User Identification**: Login/logout automático
- **Error Tracking**: Errores JS y promesas rechazadas
- **Button Clicks**: Botones importantes automáticamente
- **External Links**: Enlaces externos automáticamente

### ✅ Tracking Manual de E-commerce
```javascript
// Usar en componentes
import { useClarity } from '@/composables/useClarity'

const { trackEcommerce } = useClarity()

// Eventos disponibles:
trackEcommerce.viewProduct(productId, productName, category)
trackEcommerce.addToCart(productId, productName, quantity, price)
trackEcommerce.removeFromCart(productId, productName)
trackEcommerce.beginCheckout(cartValue, itemCount)
trackEcommerce.purchase(orderId, value, items)
trackEcommerce.search(query, resultsCount)
trackEcommerce.login(method)
trackEcommerce.register(method)
```

### ✅ Acceso Global
```javascript
// En cualquier componente Vue:
this.$trackEvent('custom_event', { data: 'value' })
this.$trackEcommerce.addToCart(1, 'Product', 1, 100)

// O con inject:
import { inject } from 'vue'
const clarity = inject('clarity')
```

## 🐛 **Debug en Desarrollo**

El componente `ClarityDebug` aparece solo en desarrollo (top-right corner):

- ✅ Estado de inicialización
- ✅ Test de eventos
- ✅ Historial de eventos recientes
- ✅ Información de configuración

## 📊 **Eventos Implementados**

### 🔐 Autenticación
- `user_login_success` - Login exitoso
- `user_logout` - Logout del usuario
- `user_register` - Registro de nuevo usuario

### 🛒 E-commerce
- `add_to_cart` - Producto agregado al carrito
- `remove_from_cart` - Producto eliminado del carrito
- `begin_checkout` - Inicio de checkout
- `purchase` - Compra completada
- `product_view` - Vista de producto

### 🗂️ Navegación
- `page_view` - Cambio de página
- `button_click` - Click en botones
- `external_link_click` - Click en enlaces externos
- `search` - Búsqueda realizada

### 🚨 Errores
- `javascript_error` - Error JS capturado
- `unhandled_promise_rejection` - Promesa rechazada

## 🚀 **Cómo Usar en Componentes**

### Ejemplo 1: Tracking en Producto
```vue
<script setup>
import { useClarity } from '@/composables/useClarity'

const { trackEcommerce } = useClarity()

const viewProduct = (product) => {
  // Track product view
  trackEcommerce.viewProduct(
    product.id, 
    product.nombre, 
    product.categoria
  )
}

const addToCart = (product) => {
  // Tu lógica existente...
  cartStore.addToCart(product.id, 1, product)
  
  // Tracking ya incluido en cartStore, pero puedes agregar más detalles:
  trackEcommerce.addToCart(
    product.id,
    product.nombre,
    1,
    product.precio
  )
}
</script>
```

### Ejemplo 2: Tracking en Búsqueda
```vue
<script setup>
import { useClarity } from '@/composables/useClarity'

const { trackEcommerce } = useClarity()

const search = async (query) => {
  const results = await searchProducts(query)
  
  // Track search
  trackEcommerce.search(query, results.length)
}
</script>
```

### Ejemplo 3: Evento Personalizado
```vue
<script setup>
import { useClarity } from '@/composables/useClarity'

const { trackEvent } = useClarity()

const shareProduct = (product) => {
  // Track custom event
  trackEvent('product_share', {
    product_id: product.id,
    product_name: product.nombre,
    share_method: 'social_media'
  })
}
</script>
```

## 🔄 **Cómo Funciona con Docker**

La implementación funciona perfectamente con tu setup actual:

```bash
# Tu comando habitual
docker compose up --build -d

# Clarity estará disponible en:
# - Frontend: http://localhost:5173
# - Debugging: Panel en esquina superior derecha
```

## 📈 **Dashboard de Clarity**

Una vez configurado, en [clarity.microsoft.com](https://clarity.microsoft.com) podrás ver:

1. **Heatmaps**: Dónde hacen clic los usuarios
2. **Session Recordings**: Grabaciones de navegación
3. **Custom Events**: Todos los eventos que enviamos
4. **User Flows**: Cómo navegan por tu e-commerce
5. **Performance**: Métricas de carga de la aplicación

## ⚡ **Performance**

- ✅ **Async Loading**: Script se carga de forma asíncrona
- ✅ **Error Handling**: No afecta la aplicación si Clarity falla
- ✅ **Environment Aware**: Solo activo cuando está habilitado
- ✅ **Lightweight**: Mínimo impacto en rendimiento

## 🛠️ **Próximos Pasos**

1. **Configurar Project ID real** en variables de entorno
2. **Testear eventos** usando el componente debug
3. **Desplegar a producción** con configuración de producción
4. **Monitorear métricas** en dashboard de Clarity
5. **Agregar más eventos personalizados** según necesidades

## 🔒 **Privacidad y Cumplimiento**

- ✅ **GDPR Compliant**: Clarity respeta configuraciones de privacidad
- ✅ **No PII**: No captura información personal identificable
- ✅ **Configurable**: Se puede deshabilitar fácilmente
- ✅ **Transparente**: Usuario puede ver en DevTools que está activo

## 🆘 **Troubleshooting**

### Clarity no se inicializa
1. Verificar Project ID en `.env`
2. Verificar `VITE_CLARITY_ENABLED=true`
3. Revisar consola del navegador
4. Usar el componente debug para diagnosticar

### Eventos no aparecen en dashboard
1. Esperar 30-60 minutos (delay de procesamiento)
2. Verificar que los eventos se envían (ver en debug)
3. Confirmar Project ID correcto

### Error en producción
1. Verificar `.env.production` configurado
2. Confirmar que el build incluye las variables de entorno
3. Revisar logs de servidor para errores de script

---

**📝 Implementado por:** GitHub Copilot  
**📅 Fecha:** $(date +%Y-%m-%d)  
**🏷️ Versión:** 1.0.0  
**🔗 Documentación oficial:** https://docs.microsoft.com/en-us/clarity/
