# 🛍️ Sol_Store_Ecomerce

**Sol_Store_Ecomerce** es una tienda de impresiones 3D que estamos desarrollando como página web para ofrecer productos personalizados. Este repositorio contiene todo el entorno de desarrollo necesario usando contenedores Docker.

## New Changes on Development Testing

- Refix of Deploy Staging workflow to a minimal version for testing.

## 👥 Colaboradores

- Vianka  
- Ricardo  
- Paula  
- Roberto  

## 📁 Estructura del Proyecto

```
DOCKERIZANDO_2/
├── docker/
├── front-vue/
├── taskcurso/
├── .gitattributes
├── .gitignore
└── docker-compose.yml
```

## 🧰 Tecnologías Utilizadas

- **Backend:** PHP con Laravel  
- **Base de Datos:** PostgreSQL  
- **Frontend:** Vue.js  
- **Contenedores:** Docker y Docker Compose

## ℹ️ Configuración de Variables de Entorno

### Backend (Laravel)
Crea tu archivo `.env` en la carpeta `taskcurso/` a partir del `.env.example`

### Frontend (Vue)
El frontend ahora usa **configuración centralizada de API**:

```bash
cd front-vue

# Para desarrollo con Docker (usa Nginx en puerto 80)
cp .env.example .env

# O usa el script automático:
./switch-env.sh docker

# Para desarrollo local sin Docker (puerto 8000 directo)
./switch-env.sh local
```

**Variable clave:** `VITE_API_BASE_URL`
- Docker: `http://localhost/api`
- Local: `http://localhost:8000/api`
- Producción: `https://tu-dominio.com/api`

📖 **Documentación completa:** [`front-vue/INDEX.md`](front-vue/INDEX.md)


## 🚀 Instrucciones para levantar el entorno

Asegúrate de tener instalado Docker y Docker Compose antes de comenzar.

### 1. Baja los contenedores anteriores (por si ya estaban corriendo)

```bash
docker compose down --remove-orphans
```

### 2. Reconstruye e inicia los contenedores en segundo plano

```bash
docker compose up --build -d
```

### 3. Entra al contenedor del backend

```bash
docker compose exec backend bash
```

### 4. Corre las migraciones y seeders para preparar la base de datos

```bash
php artisan migrate:fresh --seed
chmod -R 777 storage bootstrap/cache
```

### 5. Sal del contenedor

```bash
exit
```

### 6. Abre la aplicación en el navegador

[http://localhost/admin/products](http://localhost/admin/products)

---

## 📊 **Microsoft Clarity Analytics**

El proyecto incluye integración con Microsoft Clarity para analítica web avanzada:

- ✅ **Mapas de calor** de interacciones de usuario
- ✅ **Grabaciones de sesión** completas  
- ✅ **Tracking de eventos** de e-commerce
- ✅ **Análisis de conversión** y abandono
- ✅ **Debug panel** en desarrollo

### Configuración de Clarity

1. **Variables de entorno** (ya configurado):
   ```env
   VITE_CLARITY_PROJECT_ID=t62uyfc02j
   VITE_CLARITY_ENABLED=true
   ```

2. **Panel de Debug** (solo desarrollo):
   - Aparece en esquina superior derecha
   - Muestra estado de inicialización
   - Permite probar eventos
   - Historial de eventos recientes

3. **Documentación completa**: Ver [`CLARITY_IMPLEMENTATION.md`](CLARITY_IMPLEMENTATION.md)

---

## � **Stripe Payment Integration**

El proyecto incluye integración completa con Stripe para procesar pagos de forma segura:

### Características de Pago
- ✅ **Checkout seguro** con elementos de tarjeta de Stripe
- ✅ **Creación de PaymentIntent** en el backend
- ✅ **Confirmación de pago** en el frontend
- ✅ **Verificación en backend** después del pago
- ✅ **Modal de éxito** con animaciones
- ✅ **Actualización automática** del estado del pedido

### Configuración de Stripe

1. **Obtener credenciales de Stripe**:
   - Ve a [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys)
   - Copia tu **Publishable Key** (comienza con `pk_test_`)
   - Copia tu **Secret Key** (comienza con `sk_test_`)

2. **Configurar variables de entorno**:
   ```bash
   # En taskcurso/.env
   STRIPE_KEY=pk_test_tu_clave_publica_aqui
   
   # Por seguridad, divide la clave secreta en dos partes
   STRIPE_SECRET_PART1=sk_test_primera_parte
   STRIPE_SECRET_PART2=segunda_parte
   
   STRIPE_WEBHOOK_SECRET=whsec_tu_webhook_secret
   ```

3. **Cómo dividir la clave secreta**:
   ```
   Clave original: sk_test_51ABC...XYZ900
   
   STRIPE_SECRET_PART1=sk_test_51ABC...mitad
   STRIPE_SECRET_PART2=...otraMitad...XYZ900
   ```

### Paquetes Instalados

#### Backend (Laravel)
```bash
composer require stripe/stripe-php
```

#### Frontend (Vue)
```bash
npm install @stripe/stripe-js
```

### Archivos Clave
- `taskcurso/app/Services/StripeService.php` - Servicio de Stripe
- `taskcurso/app/Http/Controllers/StripePaymentController.php` - Controlador de pagos
- `taskcurso/config/stripe.php` - Configuración de Stripe
- `front-vue/src/composables/useStripe.js` - Composable de Stripe
- `front-vue/src/components/StripePaymentForm.vue` - Formulario de pago
- `front-vue/src/views/CheckoutView.vue` - Vista de checkout

### Flujo de Pago
1. Usuario agrega productos al carrito
2. Usuario va al checkout (`/checkout?direccion_id=X`)
3. Se crea un pedido en estado "pendiente"
4. Frontend crea un PaymentIntent en el backend
5. Usuario ingresa datos de tarjeta (elementos de Stripe)
6. Frontend confirma el pago con Stripe
7. Backend verifica el pago y actualiza el pedido a "confirmado"
8. Se muestra modal de éxito y se redirige a pedidos

### Tarjetas de Prueba
Para probar en modo test, usa estas tarjetas:

| Número | Resultado |
|--------|-----------|
| 4242 4242 4242 4242 | Éxito |
| 4000 0000 0000 9995 | Declinada (fondos insuficientes) |
| 4000 0000 0000 0002 | Declinada (tarjeta rechazada) |

- **Fecha de expiración**: Cualquier fecha futura (ej. 12/25)
- **CVC**: Cualquier 3 dígitos (ej. 123)

---

## �🔧 **Configuración de API Centralizada** ⭐ NUEVO

El frontend ahora usa una **URL base centralizada** para todas las llamadas a la API, facilitando el cambio entre entornos.

### Cambio Rápido de Entorno

```bash
cd front-vue

# Desarrollo con Docker
./switch-env.sh docker

# Desarrollo local (sin Docker)
./switch-env.sh local

# Producción
./switch-env.sh production
```

### Beneficios
- ✅ Cambio de URL en un solo lugar
- ✅ Autenticación JWT automática
- ✅ Manejo de errores centralizado
- ✅ Múltiples entornos soportados

### Documentación Frontend
- 📖 [Índice completo](front-vue/INDEX.md)
- 🚀 [Guía rápida](front-vue/QUICK_GUIDE.md)
- ✅ [Checklist para developers](front-vue/DEVELOPER_CHECKLIST.md)

---

¡Y listo! Ya tienes todo funcionando localmente con analytics incluido 🚀📈
