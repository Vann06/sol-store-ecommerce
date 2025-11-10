# 📊 Guía de Uso - Sistema de Reportes

## 🎯 Descripción

Sistema completo de reportes de ventas con generación de archivos PDF y Excel. Permite analizar y exportar datos de ventas, productos y pedidos en diferentes formatos.

---

## 🚀 Acceso

### URL del Sistema
```
http://localhost/admin/reportes
```

O si usas puerto específico:
```
http://localhost:5173/admin/reportes
```

### Requisitos
- ✅ Usuario autenticado con JWT
- ✅ Backend Laravel corriendo (puerto 8000)
- ✅ Frontend Vue corriendo (puerto 5173 o 80 vía Nginx)

---

## 📋 Funcionalidades

### 1. **Dashboard de Estadísticas**

El dashboard muestra 4 métricas principales:

| Métrica | Descripción |
|---------|-------------|
| 💰 **Total Ventas** | Suma total de ventas en el período seleccionado |
| 🛍️ **Total Pedidos** | Cantidad de pedidos realizados |
| 📊 **Promedio Venta** | Promedio de venta por pedido |
| 📦 **Total Productos** | Cantidad total de productos en inventario |

### 2. **Filtros de Fecha**

```
📅 Fecha Inicio: [Selector de fecha]
📅 Fecha Fin: [Selector de fecha]
🔍 [Botón Filtrar]
```

**Por defecto**: Últimos 30 días

**Cómo usar**:
1. Selecciona fecha de inicio
2. Selecciona fecha de fin
3. Click en "🔍 Filtrar"
4. Los datos se actualizarán automáticamente

### 3. **Botones de Exportación**

#### 📄 **Ventas PDF** (Rojo)
```http
POST /api/reportes/pdf
Body: { 
  tipo: "ventas",
  fecha_inicio: "2024-01-01",
  fecha_fin: "2024-12-31"
}
```
**Contenido**:
- Lista detallada de ventas
- Fecha, monto, pedido asociado
- Total general del período

#### 📗 **Ventas Excel** (Verde)
```http
POST /api/reportes/excel
Body: { 
  tipo: "ventas",
  fecha_inicio: "2024-01-01",
  fecha_fin: "2024-12-31"
}
```
**Contenido**:
- Hoja de cálculo con ventas
- Columnas: ID, Fecha, Monto Total, ID Pedido
- Formato `.xlsx` editable

#### 📄 **Productos PDF** (Azul)
```http
POST /api/reportes/pdf
Body: { 
  tipo: "productos",
  fecha_inicio: "2024-01-01",
  fecha_fin: "2024-12-31"
}
```
**Contenido**:
- Lista completa de productos
- Nombre, categoría, stock, precio
- Estado (activo/inactivo)

#### 📗 **Pedidos Excel** (Morado)
```http
POST /api/reportes/excel
Body: { 
  tipo: "pedidos",
  fecha_inicio: "2024-01-01",
  fecha_fin: "2024-12-31"
}
```
**Contenido**:
- Hoja de cálculo con pedidos
- Columnas: ID, Usuario, Fecha, Estado, Total
- Formato `.xlsx` editable

---

## 📈 Gráficos Visuales

### 1. **Ventas por Mes**
- Gráfico de barras interactivo
- Muestra el total de ventas por mes
- Colores: Azul
- Hover muestra el valor exacto

### 2. **Pedidos por Estado**
- Barras horizontales con porcentajes
- Estados:
  - 🟡 **Procesando** (Amarillo)
  - 🔵 **Enviado** (Azul)
  - 🟢 **Entregado** (Verde)
  - 🔴 **Cancelado** (Rojo)

### 3. **Top 10 Productos**
- Tabla ordenada por ventas
- Columnas:
  - # (Posición)
  - Producto (Nombre)
  - Ventas (Cantidad vendida)
  - Stock (Inventario actual)

---

## 🔧 Integración con tu Menú

### Opción 1: Agregar al Menú de Administración

```vue
<!-- En tu componente de navegación -->
<template>
  <nav class="admin-sidebar">
    <router-link to="/admin/dashboard">
      🏠 Dashboard
    </router-link>
    <router-link to="/admin/productos">
      📦 Productos
    </router-link>
    <router-link to="/admin/pedidos">
      🛍️ Pedidos
    </router-link>
    <router-link to="/admin/reportes">
      📊 Reportes
    </router-link>
  </nav>
</template>
```

### Opción 2: Acceso Directo desde Código

```javascript
// Navegar programáticamente
import { useRouter } from 'vue-router'

const router = useRouter()

function irAReportes() {
  router.push('/admin/reportes')
}
```

---

## 🛠️ Solución de Problemas

### ❌ Error: "Unauthorized" o "401"

**Causa**: No estás autenticado con JWT

**Solución**:
```javascript
// Verifica que tengas el token
const token = localStorage.getItem('access_token')
console.log('Token:', token)

// Si no hay token, inicia sesión
if (!token) {
  router.push('/account/login')
}
```

### ❌ Error al Descargar PDF/Excel

**Causa**: El backend no puede generar el archivo

**Solución**:
1. Verifica que el contenedor backend esté corriendo:
   ```bash
   docker compose ps
   ```

2. Revisa los logs del backend:
   ```bash
   docker compose logs backend
   ```

3. Verifica que las dependencias estén instaladas:
   ```bash
   docker compose exec backend php artisan tinker
   >>> class_exists('Barryvdh\DomPDF\Facade\Pdf')
   >>> class_exists('Maatwebsite\Excel\Facades\Excel')
   ```

### ❌ Gráficos no se Muestran

**Causa**: No hay datos en el período seleccionado

**Solución**:
1. Verifica que haya ventas en la base de datos:
   ```bash
   docker compose exec backend php artisan tinker
   >>> DB::table('historial_ventas')->count()
   ```

2. Ajusta el rango de fechas para incluir datos

### ❌ Error: "Network Error"

**Causa**: El backend no está accesible

**Solución**:
1. Verifica la URL base en `front-vue/src/http.js`:
   ```javascript
   const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'
   ```

2. Asegúrate de que Nginx esté redirigiendo correctamente:
   ```nginx
   location /api/ {
       proxy_pass http://backend:80;
   }
   ```

---

## 📊 Estructura de Datos

### Endpoint: GET /api/reportes/datos

**Respuesta**:
```json
{
  "resumen": {
    "total_ventas": 15430.50,
    "total_pedidos": 42,
    "promedio_venta": 367.39,
    "total_productos": 120,
    "total_usuarios": 35
  },
  "ventas_por_mes": [
    { "mes": "Jan", "total": 2500.00, "cantidad": 8 },
    { "mes": "Feb", "total": 3200.50, "cantidad": 12 }
  ],
  "top_productos": [
    { "nombre": "Producto A", "ventas": 45, "stock": 30 },
    { "nombre": "Producto B", "ventas": 38, "stock": 15 }
  ],
  "pedidos_por_estado": [
    { "estado": "Procesando", "cantidad": 12 },
    { "estado": "Enviado", "cantidad": 18 },
    { "estado": "Entregado", "cantidad": 10 },
    { "estado": "Cancelado", "cantidad": 2 }
  ],
  "periodo": {
    "inicio": "2024-10-09",
    "fin": "2024-11-09"
  }
}
```

---

## 🎨 Personalización

### Cambiar Colores de los Botones

```vue
<!-- En Reports.vue -->
<button class="bg-red-600 hover:bg-red-700">
  <!-- Cambia red-600 por el color que prefieras -->
</button>
```

### Agregar Más Tipos de Reportes

1. **Backend** - Agregar caso en `obtenerDatosReporte()`:
```php
case 'usuarios':
    return [
        'titulo' => 'Reporte de Usuarios',
        'usuarios' => User::whereBetween('created_at', [$fechaInicio, $fechaFin])->get(),
        'total_usuarios' => User::count()
    ];
```

2. **Frontend** - Agregar botón:
```vue
<button @click="descargarPDF('usuarios')">
  👥 Usuarios PDF
</button>
```

---

## 📦 Archivos Generados

### Nombres de Archivo

- PDF: `reporte_{tipo}_{fecha}.pdf`
  - Ejemplo: `reporte_ventas_2024-11-09.pdf`

- Excel: `reporte_{tipo}_{fecha}.xlsx`
  - Ejemplo: `reporte_ventas_2024-11-09.xlsx`

### Ubicación de Descarga

Los archivos se descargan automáticamente en la carpeta de descargas del navegador.

---

## 🔐 Seguridad

- ✅ Rutas protegidas con middleware JWT
- ✅ Validación de fechas en el backend
- ✅ Prevención de inyección SQL con Eloquent
- ✅ Límite de rango de fechas (máximo 365 días)

---

## 📞 Soporte

Si encuentras algún problema:

1. Revisa los logs del backend:
   ```bash
   docker compose logs -f backend
   ```

2. Revisa la consola del navegador (F12)

3. Verifica que todas las dependencias estén instaladas:
   ```bash
   docker compose exec backend composer show | grep -E "dompdf|excel"
   ```

---

## 🎉 ¡Listo!

Tu sistema de reportes está configurado y listo para usar. Genera informes profesionales de ventas, productos y pedidos con un solo click.

**Próximos pasos sugeridos**:
- Agregar filtros por categoría de producto
- Implementar reportes programados (cron jobs)
- Añadir gráficos más avanzados con Chart.js
- Exportar a otros formatos (CSV, JSON)
