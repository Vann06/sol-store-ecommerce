<template>
  <div class="checkout-page">
    <div class="checkout-container">
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
      <div class="checkout-grid">
        
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

          <!-- Selector de Dirección de Envío -->
          <div class="address-section">
            <div class="address-header">
              <div class="address-icon">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
              </div>
              <h3>Dirección de envío</h3>
              <span v-if="isRecreatingOrder" class="updating-badge">
                <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                Actualizando...
              </span>
            </div>
            
            <div class="address-selector" :class="{ 'disabled': isRecreatingOrder }">
              <div 
                v-for="address in addresses" 
                :key="address.id"
                class="address-option"
                :class="{ 'selected': selectedAddressId === address.id }"
                @click="selectedAddressId = address.id"
              >
                <div class="radio-indicator">
                  <div class="radio-dot"></div>
                </div>
                <div class="address-content">
                  <div class="address-text">
                    <p class="address-main">{{ address.direccion }}</p>
                    <p v-if="address.id_municipio" class="address-detail">Municipio ID: {{ address.id_municipio }}</p>
                  </div>
                  <span v-if="address.is_default" class="default-badge">
                    Predeterminada
                  </span>
                </div>
              </div>
              
              <div v-if="addresses.length === 0" class="no-addresses">
                <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                <p>No tienes direcciones guardadas</p>
                <button @click="router.push('/account/addresses')" class="add-address-btn">
                  <i class="fa-solid fa-plus" aria-hidden="true"></i>
                  Agregar dirección
                </button>
              </div>
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

          <!-- Formulario de Stripe -->
          <StripePaymentForm
            v-if="!loading && selectedAddressId"
            :direccion-id="selectedAddressId"
            :amount="total"
            :order-summary="{
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
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute, onBeforeRouteLeave } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAddressesStore } from '@/stores/addresses'
import StripePaymentForm from '@/components/StripePaymentForm.vue'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const addressesStore = useAddressesStore()

// Datos del pedido
const orderId = ref(null)
const orderNumber = ref('')
const orderItems = ref([])
const loading = ref(true)
const paymentInProgress = ref(false)

// Direcciones
const selectedAddressId = ref(null)
const addresses = computed(() => addressesStore.items)
const isRecreatingOrder = ref(false)

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
 * Ya NO es necesario recrear el pedido al cambiar dirección
 * porque el pedido se crea solo después de confirmar el pago
 */

/**
 * Manejar pago exitoso
 */
const handlePaymentSuccess = (paymentData) => {
  console.log('✅ Pago exitoso:', paymentData)
  
  // ✅ VALIDACIÓN: Verificar que el pago realmente se completó
  if (!paymentData || !paymentData.paymentIntentId || !paymentData.orderId) {
    console.error('❌ Datos de pago inválidos:', paymentData)
    error.value = 'Error: No se pudo verificar el pago'
    return
  }
  
  // Marcar que el pago ya no está en progreso
  paymentInProgress.value = false
  
  // Marcar el pago como completado en sessionStorage para evitar duplicados
  sessionStorage.setItem('payment_completed', 'true')
  sessionStorage.setItem('completed_order_id', paymentData.orderId)
  
  // ✅ SINCRONIZAR el carrito con el backend (que ya lo vació después de verificar el pago)
  // No llamamos a clearCart() porque ya el backend lo hizo en verifyPayment
  // Solo necesitamos refrescar para actualizar el estado local
  cartStore.fetchCart().then(() => {
    console.log('✅ Carrito sincronizado después del pago')
  }).catch((err) => {
    console.warn('⚠️ Error al sincronizar carrito:', err)
    // Aún así, limpiamos localmente para asegurar una buena UX
    cartStore.items = []
    cartStore.total = 0
    cartStore.itemCount = 0
  })
  
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
    // ✅ VALIDACIÓN: Verificar si ya se completó un pago anteriormente
    const paymentCompleted = sessionStorage.getItem('payment_completed')
    if (paymentCompleted === 'true') {
      console.log('⚠️ Ya existe un pago completado, redirigiendo...')
      sessionStorage.removeItem('payment_completed')
      sessionStorage.removeItem('completed_order_id')
      router.push('/account/orders')
      return
    }
    
    // Cargar direcciones del usuario
    await addressesStore.fetchAll()
    
    // Obtener dirección desde query params o usar la predeterminada
    const queryAddressId = route.query.direccion_id
    const defaultAddressId = addressesStore.defaultId
    
    selectedAddressId.value = queryAddressId ? parseInt(queryAddressId) : defaultAddressId
    
    if (!selectedAddressId.value || !addressesStore.items.length) {
      alert('No tienes direcciones registradas. Por favor, agrega una dirección de envío.')
      router.push('/account/addresses')
      return
    }
    
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
    
    // Verificar que haya una dirección seleccionada
    if (!selectedAddressId.value) {
      alert('Falta información de dirección')
      router.push('/cart')
      return
    }
    
    // ✅ NO CREAR EL PEDIDO AÚN
    // El pedido se creará después de que Stripe confirme el pago
    // Esto evita pedidos huérfanos si el usuario cancela o retrocede
    
    // Marcar que el proceso de pago está en progreso
    paymentInProgress.value = true
    sessionStorage.setItem('checkout_in_progress', 'true')
    
    console.log('✅ Checkout listo, esperando pago con Stripe...')
    
    loading.value = false
  } catch (error) {
    console.error('Error al crear pedido:', error)
    alert(error.response?.data?.error || 'Error al crear el pedido')
    router.push('/cart')
  }
})

/**
 * Prevenir navegación durante el proceso de pago
 */
onBeforeRouteLeave((to, from, next) => {
  // Si el pago está en progreso y no ha sido completado exitosamente
  if (paymentInProgress.value && !showSuccessModal.value) {
    const confirmed = window.confirm(
      '⚠️ Tienes un pago en proceso.\n\n' +
      'Si abandonas esta página, perderás el progreso y deberás iniciar el proceso nuevamente.\n\n' +
      '¿Estás seguro de que deseas salir?'
    )
    
    if (!confirmed) {
      next(false) // Cancelar la navegación
      return
    }
    
    // Si confirma, limpiar el estado
    sessionStorage.removeItem('checkout_in_progress')
    sessionStorage.removeItem('pending_order_id')
    paymentInProgress.value = false
  }
  
  next() // Permitir la navegación
})

/**
 * Advertir al usuario si intenta cerrar la pestaña durante el pago
 */
const handleBeforeUnload = (e) => {
  if (paymentInProgress.value && !showSuccessModal.value) {
    e.preventDefault()
    e.returnValue = '' // Chrome requiere esto
    return ''
  }
}

/**
 * Agregar listener para beforeunload
 */
onMounted(() => {
  window.addEventListener('beforeunload', handleBeforeUnload)
})

/**
 * Limpiar listener al desmontar
 */
onBeforeUnmount(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload)
  
  // Limpiar sessionStorage si el pago se completó exitosamente
  if (showSuccessModal.value) {
    sessionStorage.removeItem('checkout_in_progress')
    sessionStorage.removeItem('pending_order_id')
  }
})

/**
 * Ir a pedidos
 */
const goToOrders = () => {
  // Limpiar estado antes de navegar
  paymentInProgress.value = false
  sessionStorage.removeItem('checkout_in_progress')
  sessionStorage.removeItem('pending_order_id')
  sessionStorage.removeItem('payment_completed')
  sessionStorage.removeItem('completed_order_id')
  
  router.push('/account/orders')
}

/**
 * Volver al inicio
 */
const goHome = () => {
  // Limpiar estado antes de navegar
  paymentInProgress.value = false
  sessionStorage.removeItem('checkout_in_progress')
  sessionStorage.removeItem('pending_order_id')
  sessionStorage.removeItem('payment_completed')
  sessionStorage.removeItem('completed_order_id')
  
  router.push('/')
}
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
  background: var(--surface-2);
  padding: 3.5rem 0 4rem;
}

.checkout-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
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

.checkout-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

@media (min-width: 1024px) {
  .checkout-grid {
    grid-template-columns: repeat(2, 1fr);
  }
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

/* ===== SELECTOR DE DIRECCIÓN ===== */
.address-section {
  padding: 1.5rem 1.75rem;
  border-top: 1px solid rgba(113, 18, 18, 0.12);
  background: rgba(113, 18, 18, 0.02);
}

.address-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.address-icon {
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 0.65rem;
  background: rgba(113, 18, 18, 0.12);
  color: var(--brand);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
}

.address-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--ink-1);
  flex: 1;
}

.updating-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.85rem;
  border-radius: 999px;
  background: rgba(255, 192, 16, 0.15);
  color: rgba(180, 83, 9, 1);
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.address-selector {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  transition: opacity 0.2s ease;
}

.address-selector.disabled {
  opacity: 0.6;
  pointer-events: none;
}

.address-option {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1rem 1.25rem;
  border-radius: 0.85rem;
  background: var(--surface);
  border: 2px solid rgba(113, 18, 18, 0.15);
  cursor: pointer;
  transition: all 0.2s ease;
}

.address-option:hover {
  border-color: rgba(113, 18, 18, 0.35);
  background: rgba(255, 221, 75, 0.08);
}

.address-option.selected {
  border-color: var(--brand);
  background: rgba(255, 221, 75, 0.12);
  box-shadow: 0 4px 12px rgba(113, 18, 18, 0.15);
}

.radio-indicator {
  width: 1.35rem;
  height: 1.35rem;
  border-radius: 50%;
  border: 2px solid rgba(113, 18, 18, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 0.1rem;
  transition: all 0.2s ease;
}

.address-option.selected .radio-indicator {
  border-color: var(--brand);
  background: var(--brand);
}

.radio-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background: white;
  opacity: 0;
  transform: scale(0);
  transition: all 0.2s ease;
}

.address-option.selected .radio-dot {
  opacity: 1;
  transform: scale(1);
}

.address-content {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.75rem;
}

.address-text {
  flex: 1;
}

.address-main {
  margin: 0 0 0.25rem 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--ink-1);
  line-height: 1.4;
}

.address-detail {
  margin: 0;
  font-size: 0.8rem;
  color: var(--ink-3);
}

.default-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(40, 167, 69, 0.12);
  color: rgba(4, 125, 74, 1);
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  white-space: nowrap;
}

.no-addresses {
  text-align: center;
  padding: 2rem 1rem;
  color: var(--ink-3);
}

.no-addresses i {
  font-size: 2.5rem;
  color: var(--ink-4);
  margin-bottom: 0.75rem;
}

.no-addresses p {
  margin: 0 0 1rem 0;
  font-size: 0.95rem;
}

.add-address-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1.25rem;
  border-radius: 0.75rem;
  background: var(--brand);
  color: white;
  border: none;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-address-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(113, 18, 18, 0.25);
}

.add-address-btn:active {
  transform: translateY(0);
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
  
  .checkout-container {
    padding: 0 1.5rem;
  }
}

@media (max-width: 768px) {
  .checkout-page {
    padding: 2.5rem 0 3rem;
  }
  
  .checkout-container {
    padding: 0 1rem;
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
  
  .checkout-grid {
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .checkout-container {
    padding: 0 0.75rem;
  }
  
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
