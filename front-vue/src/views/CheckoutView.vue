<template>
  <div class="checkout-page">
    <div class="container mx-auto px-4">
      <!-- Header -->
  <div class="page-lead">
        <div class="lead-icon">
          <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
        </div>
        <div class="lead-copy">
          <h1>Finaliza tu compra</h1>
          <p>Revisa tu pedido y completa el pago de forma segura con Stripe.</p>
        </div>
        <div class="secure-pill">
          <i class="fa-solid fa-lock" aria-hidden="true"></i>
          <span>Conexión SSL segura</span>
        </div>
      </div>

      <!-- Contenedor principal -->
      <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-8">
        
        <!-- Columna izquierda: Resumen del pedido -->
        <div class="order-summary-card">
          <div class="card-header">
            <div class="card-icon">
              <i class="fa-solid fa-box-open" aria-hidden="true"></i>
            </div>
            <h2>Resumen del pedido</h2>
          </div>
          
          <!-- Items del pedido -->
          <div class="items-list">
            <div v-if="orderItems.length === 0" class="empty-cart">
              <p class="text-gray-500 text-center text-2xl mb-2">🛒</p>
              <p class="text-gray-500">No hay productos en el carrito</p>
            </div>
            
            <div v-for="item in orderItems" :key="item.id" class="order-item">
              <div class="item-image">
                <div class="image-placeholder">
                  <i class="fa-solid fa-cube" aria-hidden="true"></i>
                </div>
              </div>
              <div class="item-details">
                <p class="item-name">{{ item.name }}</p>
                <p class="item-quantity">Cantidad: {{ item.quantity }}</p>
                <p class="item-price">{{ formatCurrency(item.price) }} c/u</p>
              </div>
              <div class="item-total">
                {{ formatCurrency(item.price * item.quantity) }}
              </div>
            </div>
          </div>

          <!-- Totales -->
          <div class="totals-section">
            <div class="total-row">
              <span class="label">Subtotal</span>
              <span class="value">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="total-row">
              <span class="label">Envío</span>
              <span class="value shipping">{{ shippingCost === 0 ? 'Gratis' : formatCurrency(shippingCost) }}</span>
            </div>
            <div class="total-row">
              <span class="label">Impuestos (16%)</span>
              <span class="value">{{ formatCurrency(taxes) }}</span>
            </div>
            <div class="total-row grand-total">
              <span class="label">Total a Pagar</span>
              <span class="value">{{ formatCurrency(total) }}</span>
            </div>
          </div>

          <!-- Información adicional -->
          <div class="info-box">
            <div class="info-icon">
              <i class="fa-regular fa-envelope" aria-hidden="true"></i>
            </div>
            <div>
              <p class="info-title">Recibirás un correo de confirmación</p>
              <p class="info-subtitle">Te avisaremos en cuanto el pago sea aprobado.</p>
            </div>
          </div>
        </div>

        <!-- Columna derecha: Formulario de pago -->
        <div class="checkout-form-panel">
          <!-- Loader mientras se crea el pedido -->
          <div v-if="loading" class="text-center py-12">
            <div class="panel-icon">
              <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            </div>
            <p>Preparando tu pedido...</p>
          </div>

          <!-- Formulario de Stripe cuando el pedido esté listo -->
          <StripePaymentForm
            v-else-if="orderId"
            :order-id="orderId"
            :amount="total"
            :order-summary="{
              orderNumber: orderNumber,
              items: orderItems.length
            }"
            currency="GTQ"
            @success="handlePaymentSuccess"
            @error="handlePaymentError"
            @cancel="handlePaymentCancel"
          />

          <!-- Mensaje de error -->
          <div v-else class="text-center py-12">
            <p class="error-text">No pudimos preparar tu pedido</p>
            <button @click="router.push('/cart')" class="error-link">
              Volver al carrito
            </button>
          </div>
        </div>

      </div>

      <!-- Modal de éxito mejorado -->
      <transition name="modal-fade">
        <div v-if="showSuccessModal" class="success-modal-overlay" @click.self="goToOrders">
          <div class="success-modal-content">
            <div class="success-modal-header">
              <div class="success-icon-wrapper">
                <i class="fa-solid fa-circle-check success-icon" aria-hidden="true"></i>
              </div>
              <div class="success-confetti">
                <span class="confetti">🎉</span>
                <span class="confetti">✨</span>
                <span class="confetti">�</span>
              </div>
            </div>
            
            <div class="success-modal-body">
              <h2 class="success-title">¡Pago Exitoso!</h2>
              <p class="success-subtitle">
                Tu pedido <strong>#{{ orderNumber }}</strong> ha sido confirmado exitosamente.
              </p>
              <div class="success-info-box">
                <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                <span>Recibirás un email de confirmación en breve</span>
              </div>
            </div>
            
            <div class="success-modal-actions">
              <button @click="goToOrders" class="btn-success-primary">
                <i class="fa-solid fa-box" aria-hidden="true"></i>
                Ver mis pedidos
              </button>
              <button @click="goHome" class="btn-success-secondary">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                Volver al inicio
              </button>
            </div>
          </div>
        </div>
      </transition>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import StripePaymentForm from '@/components/StripePaymentForm.vue'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()

// Datos del pedido
const orderId = ref(null)
const orderNumber = ref('')
const orderItems = ref([])
const loading = ref(true)

// Cálculos del carrito
const subtotal = computed(() => {
  return orderItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const shippingCost = ref(0) // Gratis según tu UI
const taxes = computed(() => subtotal.value * 0.16) // 16% según tu CartView

const total = computed(() => {
  return subtotal.value + shippingCost.value + taxes.value
})

// Estados
const showSuccessModal = ref(false)

/**
 * Formatear moneda
 */
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-GT', {
    style: 'currency',
    currency: 'GTQ'
  }).format(value)
}

/**
 * Manejar pago exitoso
 */
const handlePaymentSuccess = (paymentData) => {
  console.log('✅ Pago exitoso:', paymentData)
  
  // Limpiar el carrito después del pago exitoso
  cartStore.clearCart()
  
  // Mostrar modal de éxito
  showSuccessModal.value = true
}

/**
 * Manejar error de pago
 */
const handlePaymentError = (error) => {
  console.error('❌ Error en el pago:', error)
  // Puedes mostrar un toast o notificación aquí
}

/**
 * Manejar cancelación
 */
const handlePaymentCancel = () => {
  console.log('❌ Pago cancelado')
  router.push('/carrito')
}

/**
 * Cargar datos del carrito y crear el pedido
 */
onMounted(async () => {
  loading.value = true
  
  try {
    // Cargar items del carrito
    await cartStore.fetchCart()
    
    if (!cartStore.items || cartStore.items.length === 0) {
      alert('Tu carrito está vacío')
      router.push('/cart')
      return
    }
    
    // Mapear items del carrito a orderItems para mostrar
    orderItems.value = cartStore.items.map(item => {
      // El backend devuelve los datos directamente en el item, no dentro de un objeto producto
      const precio = parseFloat(item.precio_unitario || 0)
      
      console.log('Item del carrito:', {
        id: item.id,
        nombre: item.nombre,
        precio_unitario: item.precio_unitario,
        cantidad: item.cantidad,
        subtotal: item.subtotal,
        precio_final: precio
      })
      
      return {
        id: item.id,
        name: item.nombre || 'Producto',
        quantity: item.cantidad || 1,
        price: precio
      }
    })
    
    console.log('Total calculado:', total.value)
    
    if (total.value <= 0) {
      alert('Error: No se pudo calcular el total del pedido')
      router.push('/cart')
      return
    }
    
    // Crear el pedido en el backend (sin confirmar aún)
    const direccionId = route.query.direccion_id
    if (!direccionId) {
      alert('Falta información de dirección')
      router.push('/cart')
      return
    }
    
    // Importar http para hacer la petición
    const http = (await import('@/http')).default
    const response = await http.post('/pedidos/checkout', {
      direccion_id: direccionId
    })
    
    if (response.data.pedido) {
      orderId.value = response.data.pedido.id
      orderNumber.value = `ORD-${String(orderId.value).padStart(6, '0')}`
      console.log('✅ Pedido creado:', orderId.value)
    } else {
      throw new Error('No se pudo crear el pedido')
    }
    
    loading.value = false
  } catch (error) {
    console.error('Error al crear pedido:', error)
    alert(error.response?.data?.error || 'Error al crear el pedido')
    router.push('/cart')
  }
})

/**
 * Ir a pedidos
 */
const goToOrders = () => {
  router.push('/account/orders')
}

/**
 * Volver al inicio
 */
const goHome = () => {
  router.push('/')
}
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
  background: var(--surface-2);
  padding: 3.5rem 0 4rem;
}

.checkout-page .container {
  max-width: 1200px;
}

.page-lead {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: var(--surface);
  padding: 1.75rem 2rem;
  margin-bottom: 2.5rem;
  border-radius: 1.5rem;
  box-shadow: 0 12px 28px rgba(113, 18, 18, 0.08);
}

.lead-icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 1.1rem;
  background: linear-gradient(135deg, rgba(113, 18, 18, 0.9), rgba(255, 192, 16, 0.9));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}

.lead-copy h1 {
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--ink-1);
  margin: 0 0 0.35rem 0;
}

.lead-copy p {
  margin: 0;
  color: var(--ink-3);
  font-size: 1rem;
  max-width: 540px;
}

.secure-pill {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1.2rem;
  border-radius: 999px;
  background: rgba(113, 18, 18, 0.08);
  color: var(--brand);
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.02em;
}

.secure-pill i {
  font-size: 0.85rem;
}

.order-summary-card {
  background: var(--surface);
  border-radius: 1.25rem;
  border: 1px solid rgba(113, 18, 18, 0.12);
  box-shadow: 0 14px 36px rgba(17, 24, 39, 0.08);
  overflow: hidden;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem 1.75rem;
  border-bottom: 1px solid var(--ink-5);
  background: linear-gradient(145deg, rgba(255, 221, 75, 0.14), rgba(113, 18, 18, 0.08));
}

.card-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 0.9rem;
  background: rgba(113, 18, 18, 0.12);
  color: var(--brand);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.card-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--ink-1);
  text-transform: capitalize;
}

.items-list {
  padding: 1.5rem 1.75rem;
  max-height: 420px;
  overflow-y: auto;
}

.items-list::-webkit-scrollbar {
  width: 6px;
}

.items-list::-webkit-scrollbar-track {
  background: rgba(156, 163, 175, 0.12);
  border-radius: 999px;
}

.items-list::-webkit-scrollbar-thumb {
  background: rgba(113, 18, 18, 0.4);
  border-radius: 999px;
}

.empty-cart {
  text-align: center;
  padding: 3rem 1rem;
  color: var(--ink-3);
}

.order-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid var(--ink-5);
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  flex-shrink: 0;
}

.image-placeholder {
  width: 48px;
  height: 48px;
  border-radius: 0.85rem;
  background: rgba(113, 18, 18, 0.1);
  color: var(--brand);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-weight: 600;
  color: var(--ink-1);
  margin: 0 0 0.2rem 0;
  font-size: 0.98rem;
}

.item-quantity,
.item-price {
  font-size: 0.85rem;
  color: var(--ink-3);
}

.item-total {
  font-weight: 700;
  font-size: 1.05rem;
  color: var(--brand);
}

.totals-section {
  padding: 1.5rem 1.75rem 1.75rem;
  background: rgba(113, 18, 18, 0.03);
  border-top: 1px solid rgba(113, 18, 18, 0.12);
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.65rem 0;
  font-size: 0.95rem;
}

.total-row .label {
  color: var(--ink-2);
  font-weight: 500;
}

.total-row .value {
  color: var(--ink-1);
  font-weight: 600;
}

.total-row .shipping {
  color: #1aa34a;
  font-weight: 700;
}

.total-row.grand-total {
  border-top: 1px solid rgba(113, 18, 18, 0.25);
  margin-top: 0.75rem;
  padding-top: 1rem;
}

.total-row.grand-total .label {
  font-size: 1.1rem;
  font-weight: 700;
}

.total-row.grand-total .value {
  color: var(--brand);
  font-weight: 800;
  font-size: 1.45rem;
}

.info-box {
  margin: 1.5rem 1.75rem 1.75rem;
  padding: 1.1rem 1.25rem;
  border-radius: 1rem;
  background: rgba(255, 221, 75, 0.18);
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.info-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.75rem;
  background: rgba(113, 18, 18, 0.12);
  color: var(--brand);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.info-title {
  margin: 0;
  font-weight: 600;
  color: var(--ink-1);
}

.info-subtitle {
  margin: 0.1rem 0 0;
  color: var(--ink-3);
  font-size: 0.85rem;
}

.checkout-form-panel {
  background: var(--surface);
  border-radius: 1.25rem;
  border: 1px solid rgba(113, 18, 18, 0.12);
  box-shadow: 0 14px 36px rgba(17, 24, 39, 0.08);
  padding: 2rem;
  min-height: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.panel-icon {
  font-size: 1.5rem;
  color: var(--brand);
  margin-bottom: 1rem;
}

.checkout-form-panel p {
  margin: 0;
  color: var(--ink-2);
}

.error-text {
  font-weight: 600;
  color: var(--brand);
  font-size: 1rem;
}

.error-link {
  margin-top: 0.75rem;
  color: var(--brand);
  font-weight: 600;
  text-decoration: none;
}

.error-link:hover {
  text-decoration: underline;
}

/* Animación del modal */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.fixed > div {
  animation: fadeIn 0.3s ease-out;
}

@media (max-width: 1024px) {
  .secure-pill {
    margin-left: 0;
  }

  .page-lead {
    flex-wrap: wrap;
    justify-content: flex-start;
  }
}

@media (max-width: 768px) {
  .checkout-page {
    padding: 2.5rem 0 3rem;
  }

  .page-lead {
    flex-direction: column;
    align-items: flex-start;
  }

  .lead-icon {
    width: 3rem;
    height: 3rem;
    font-size: 1.2rem;
  }

  .lead-copy h1 {
    font-size: 1.75rem;
  }

  .lead-copy p {
    max-width: none;
  }

  .secure-pill {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
  }

  .order-summary-card,
  .checkout-form-panel {
    margin-bottom: 1.75rem;
  }

  .items-list {
    max-height: none;
  }
}

@media (max-width: 480px) {
  .page-lead {
    padding: 1.5rem;
    border-radius: 1.25rem;
  }

  .card-header {
    padding: 1.25rem 1.5rem;
  }

  .items-list,
  .totals-section {
    padding: 1.25rem 1.5rem;
  }

  .checkout-form-panel {
    padding: 1.5rem;
  }
}

/* ===== MODAL DE ÉXITO ===== */
.success-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
  animation: modalFadeIn 0.3s ease-out;
}

.success-modal-content {
  background: var(--surface);
  border-radius: 1.75rem;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  animation: modalSlideUp 0.4s ease-out;
  position: relative;
}

.success-modal-header {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.95), rgba(4, 125, 74, 0.92));
  padding: 2.5rem 2rem 2rem;
  position: relative;
  overflow: hidden;
}

.success-icon-wrapper {
  width: 5rem;
  height: 5rem;
  background: rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  animation: successPulse 1.5s ease-in-out infinite;
}

.success-icon {
  font-size: 2.5rem;
  color: white;
}

.success-confetti {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  display: flex;
  justify-content: space-around;
  align-items: flex-start;
  padding-top: 1rem;
}

.confetti {
  font-size: 1.5rem;
  animation: confettiFall 3s ease-out infinite;
  opacity: 0;
}

.confetti:nth-child(1) {
  animation-delay: 0.2s;
}

.confetti:nth-child(2) {
  animation-delay: 0.5s;
}

.confetti:nth-child(3) {
  animation-delay: 0.8s;
}

.success-modal-body {
  padding: 2rem;
  text-align: center;
}

.success-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--ink-1);
  margin: 0 0 0.75rem 0;
  letter-spacing: -0.02em;
}

.success-subtitle {
  font-size: 1rem;
  color: var(--ink-2);
  margin: 0 0 1.5rem 0;
  line-height: 1.6;
}

.success-subtitle strong {
  color: var(--brand);
  font-weight: 600;
}

.success-info-box {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.85rem 1.35rem;
  background: rgba(40, 167, 69, 0.08);
  border: 1px solid rgba(40, 167, 69, 0.25);
  border-radius: 0.85rem;
  color: rgba(4, 125, 74, 1);
  font-size: 0.9rem;
  font-weight: 500;
}

.success-info-box i {
  font-size: 1.1rem;
}

.success-modal-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding: 0 2rem 2rem;
}

.btn-success-primary,
.btn-success-secondary {
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  padding: 1rem 1.5rem;
  border-radius: 0.9rem;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-success-primary {
  background: linear-gradient(135deg, rgba(113, 18, 18, 0.95), rgba(255, 192, 16, 0.92));
  color: white;
  box-shadow: 0 8px 16px rgba(113, 18, 18, 0.25);
}

.btn-success-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(113, 18, 18, 0.3);
}

.btn-success-primary:active {
  transform: translateY(0);
}

.btn-success-secondary {
  background: rgba(113, 18, 18, 0.06);
  color: var(--brand);
  border: 1px solid rgba(113, 18, 18, 0.2);
}

.btn-success-secondary:hover {
  background: rgba(113, 18, 18, 0.1);
}

/* Animaciones del modal */
@keyframes modalFadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes modalSlideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes successPulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
  }
}

@keyframes confettiFall {
  0% {
    opacity: 0;
    transform: translateY(0) rotate(0deg);
  }
  10% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    transform: translateY(100px) rotate(180deg);
  }
}

/* Transición del modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .success-modal-content,
.modal-fade-leave-active .success-modal-content {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-fade-enter-from .success-modal-content,
.modal-fade-leave-to .success-modal-content {
  transform: translateY(30px) scale(0.95);
  opacity: 0;
}

/* Responsive para el modal */
@media (max-width: 640px) {
  .success-modal-content {
    border-radius: 1.5rem;
    margin: 1rem;
  }

  .success-modal-header {
    padding: 2rem 1.5rem 1.5rem;
  }

  .success-icon-wrapper {
    width: 4rem;
    height: 4rem;
  }

  .success-icon {
    font-size: 2rem;
  }

  .success-modal-body {
    padding: 1.5rem;
  }

  .success-title {
    font-size: 1.5rem;
  }

  .success-modal-actions {
    padding: 0 1.5rem 1.5rem;
  }

  .btn-success-primary,
  .btn-success-secondary {
    padding: 0.875rem 1.25rem;
    font-size: 0.95rem;
  }
}
</style>
