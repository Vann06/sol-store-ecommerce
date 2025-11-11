<template>
  <div class="stripe-payment-form">
    <!-- Header con gradiente -->
  <div class="payment-header">
      <div class="header-icon">
        <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
      </div>
      <div class="header-copy">
        <h3 class="title">Información de pago</h3>
        <p class="subtitle">Procesamos tus pagos de forma segura con Stripe.</p>
      </div>
    </div>

    <!-- Resumen del pedido con mejor diseño -->
    <div v-if="orderSummary" class="order-summary-card">
      <div class="summary-header">
        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
        <h4>Resumen del pedido</h4>
      </div>
      <div class="summary-content">
        <div class="summary-row">
          <span class="label">Pedido</span>
          <span class="value">#{{ orderSummary.orderNumber }}</span>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-row total-row">
          <span class="label">Total a pagar</span>
          <span class="value">{{ formatCurrency(amount) }}</span>
        </div>
      </div>
    </div>

    <!-- Formulario de facturación con mejor diseño -->
    <div class="billing-section">
      <div class="section-title">
        <i class="fa-solid fa-user" aria-hidden="true"></i>
        <span>Datos de facturación</span>
      </div>
      
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">
            <span>Nombre completo</span>
            <span class="required">*</span>
          </label>
          <input
            v-model="billingDetails.name"
            type="text"
            class="form-input"
            placeholder="Juan Pérez"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">
            <span>Email</span>
            <span class="required">*</span>
          </label>
          <input
            v-model="billingDetails.email"
            type="email"
            class="form-input"
            placeholder="correo@ejemplo.com"
            required
          />
        </div>

        <div class="form-group full-width">
          <label class="form-label">
            <span>Teléfono</span>
            <span class="optional">(opcional)</span>
          </label>
          <input
            v-model="billingDetails.phone"
            type="tel"
            class="form-input"
            placeholder="+502 1234-5678"
          />
        </div>
      </div>
    </div>

    <!-- Elemento de tarjeta de Stripe con mejor diseño -->
    <div class="card-section">
      <div class="section-title">
        <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
        <span>Información de tarjeta</span>
        <span class="required">*</span>
      </div>
      
      <div class="card-element-wrapper">
        <div class="card-field">
          <span class="card-field-label">Número de tarjeta</span>
          <div ref="cardNumberRef" class="card-element"></div>
        </div>
        <div class="card-field-row">
          <div class="card-field">
            <span class="card-field-label">Fecha de expiración</span>
            <div ref="cardExpiryRef" class="card-element"></div>
          </div>
          <div class="card-field">
            <span class="card-field-label">CVC</span>
            <div ref="cardCvcRef" class="card-element"></div>
          </div>
        </div>
      </div>
      
      <!-- Error de validación de tarjeta -->
      <transition name="fade">
        <div v-if="cardError" class="error-badge">
          <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
          <span>{{ cardError }}</span>
        </div>
      </transition>

      <!-- Tarjetas aceptadas con iconos -->
      <div class="accepted-cards">
        <span class="cards-label">Métodos de pago aceptados:</span>
        <div class="cards-icons">
          <div class="card-badge visa">VISA</div>
          <div class="card-badge mastercard">Mastercard</div>
          <div class="card-badge amex">AMEX</div>
        </div>
      </div>
    </div>

    <!-- Mensajes de error mejorados -->
    <transition name="slide-down">
      <div v-if="error" class="alert alert-error">
        <div class="alert-content">
          <p class="alert-title">
            <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
            Error al procesar el pago
          </p>
          <p class="alert-message">{{ error }}</p>
        </div>
      </div>
    </transition>

    <!-- Mensajes de éxito mejorados -->
    <transition name="slide-down">
      <div v-if="success" class="alert alert-success">
        <div class="alert-content">
          <p class="alert-title">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            ¡Pago exitoso!
          </p>
          <p class="alert-message">Tu pedido ha sido confirmado correctamente.</p>
        </div>
      </div>
    </transition>

    <!-- Botones de acción mejorados -->
    <div class="actions">
      <button
        @click="handleSubmit"
        :disabled="loading || success"
        class="btn btn-primary"
        :class="{ 'btn-loading': loading, 'btn-success': success }"
      >
        <span v-if="loading">
          <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
          Procesando pago...
        </span>
        <span v-else-if="success">
          <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
          Pago completado
        </span>
        <span v-else>
          <i class="fa-solid fa-lock" aria-hidden="true"></i>
          Pagar {{ formatCurrency(amount) }}
        </span>
      </button>

      <button
        v-if="!success"
        @click="handleCancel"
        :disabled="loading"
        class="btn btn-secondary"
      >
        <i class="fa-regular fa-circle-xmark" aria-hidden="true"></i>
        Cancelar
      </button>
    </div>

    <!-- Nota de seguridad mejorada -->
    <div class="security-badge">
      <div class="security-icon">
        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
      </div>
      <span>Conexión segura protegida con encriptación SSL de 256 bits</span>
      <div class="stripe-logo">
        <span>Powered by</span>
        <strong>Stripe</strong>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useStripe } from '@/composables/useStripe'

const props = defineProps({
  orderId: {
    type: Number,
    required: true
  },
  amount: {
    type: Number,
    required: true
  },
  orderSummary: {
    type: Object,
    default: null
  },
  currency: {
    type: String,
    default: 'GTQ'
  }
})

const emit = defineEmits(['success', 'error', 'cancel'])

// Referencias
const cardNumberRef = ref(null)
const cardExpiryRef = ref(null)
const cardCvcRef = ref(null)
const cardError = ref(null)
const error = ref(null)
const success = ref(false)
const loading = ref(false)
const clientSecret = ref(null)
const paymentIntentId = ref(null)

// Datos de facturación
const billingDetails = ref({
  name: '',
  email: '',
  phone: ''
})

// Composable de Stripe
const {
  initializeStripe,
  createCardElement,
  createPaymentIntent,
  confirmPayment,
  verifyPayment,
  destroyCardElement,
  error: stripeError
} = useStripe()

// Watch para errores de Stripe
watch(stripeError, (newError) => {
  if (newError) {
    cardError.value = newError
  }
})

/**
 * Formatear moneda
 */
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-GT', {
    style: 'currency',
    currency: props.currency
  }).format(value)
}

/**
 * Validar formulario
 */
const validateForm = () => {
  if (!billingDetails.value.name.trim()) {
    error.value = 'Por favor ingresa tu nombre completo'
    return false
  }

  if (!billingDetails.value.email.trim()) {
    error.value = 'Por favor ingresa tu email'
    return false
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(billingDetails.value.email)) {
    error.value = 'Por favor ingresa un email válido'
    return false
  }

  return true
}

/**
 * Procesar el pago
 */
const handleSubmit = async () => {
  error.value = null
  cardError.value = null

  // ✅ VALIDACIÓN: Prevenir doble submit
  if (loading.value || success.value) {
    console.warn('⚠️ Pago ya en proceso o completado')
    return
  }

  // Validar formulario
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    // Usar el clientSecret que ya tenemos del onMounted
    if (!clientSecret.value) {
      error.value = 'No se pudo inicializar el pago. Por favor recarga la página.'
      loading.value = false
      return
    }
    
    // ✅ VALIDACIÓN: Verificar que el paymentIntentId corresponda al pedido
    if (!paymentIntentId.value) {
      error.value = 'Error: No se pudo obtener el identificador del pago'
      loading.value = false
      return
    }

    console.log('💳 Confirmando pago con Stripe...')
    const paymentResult = await confirmPayment(
      clientSecret.value,
      {
        name: billingDetails.value.name,
        email: billingDetails.value.email,
        phone: billingDetails.value.phone
      }
    )

    if (!paymentResult.success) {
      error.value = paymentResult.error
      loading.value = false
      emit('error', paymentResult.error)
      return
    }
    
    // ✅ VALIDACIÓN: Verificar que el paymentIntent sea el correcto
    if (paymentResult.paymentIntent.id !== paymentIntentId.value) {
      error.value = 'Error de seguridad: El identificador del pago no coincide'
      loading.value = false
      emit('error', error.value)
      return
    }
    
    // ✅ VALIDACIÓN: Verificar que el estado sea 'succeeded'
    if (paymentResult.paymentIntent.status !== 'succeeded') {
      error.value = `El pago no se completó exitosamente. Estado: ${paymentResult.paymentIntent.status}`
      loading.value = false
      emit('error', error.value)
      return
    }

    console.log('✅ Pago confirmado en Stripe con estado:', paymentResult.paymentIntent.status)

    // 3. Verificar el pago en el backend
    console.log('🔍 Verificando pago en el backend...')
    const verifyResult = await verifyPayment(paymentResult.paymentIntent.id)

    if (!verifyResult.success) {
      error.value = verifyResult.error
      loading.value = false
      emit('error', verifyResult.error)
      return
    }

    console.log('✅ Pago verificado y pedido confirmado')

    // 4. Mostrar éxito
    success.value = true
    loading.value = false

    // ✅ Emitir evento de éxito con todos los datos verificados
    emit('success', {
      orderId: verifyResult.orderId,
      paymentIntentId: paymentResult.paymentIntent.id,
      amount: props.amount,
      paymentStatus: verifyResult.paymentStatus,
      paidAt: verifyResult.paidAt
    })

  } catch (err) {
    console.error('❌ Error en el proceso de pago:', err)
    error.value = err.message || 'Error inesperado al procesar el pago'
    loading.value = false
    emit('error', error.value)
  }
}

/**
 * Cancelar el pago
 */
const handleCancel = () => {
  emit('cancel')
}

/**
 * Inicializar Stripe al montar el componente
 */
onMounted(async () => {
  loading.value = true
  
  try {
    // Crear PaymentIntent y obtener la clave pública de Stripe
    console.log('🔧 Inicializando Stripe...')
    const intentResult = await createPaymentIntent(props.orderId, props.amount)
    
    if (!intentResult.success) {
      error.value = intentResult.error
      loading.value = false
      return
    }

    // Guardar el clientSecret y paymentIntentId para usarlo después
    clientSecret.value = intentResult.clientSecret
    paymentIntentId.value = intentResult.paymentIntentId

    console.log('✅ PaymentIntent creado:', paymentIntentId.value)

    // Inicializar Stripe con la clave pública
    await initializeStripe(intentResult.publishableKey)

    // Crear elemento de tarjeta
    if (cardNumberRef.value && cardExpiryRef.value && cardCvcRef.value) {
      createCardElement({
        number: cardNumberRef.value,
        expiry: cardExpiryRef.value,
        cvc: cardCvcRef.value
      })
    }

    loading.value = false
  } catch (err) {
    console.error('Error al inicializar Stripe:', err)
    error.value = 'Error al cargar el formulario de pago. Verifica que las credenciales de Stripe estén configuradas.'
    loading.value = false
  }
})

/**
 * Limpiar al desmontar
 */
onBeforeUnmount(() => {
  destroyCardElement()
})
</script>

<style scoped>
.stripe-payment-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  color: var(--ink-1);
}

.payment-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.75rem 2rem;
  border-radius: 1.5rem;
  background: linear-gradient(135deg, rgba(113, 18, 18, 0.95), rgba(255, 192, 16, 0.95));
  color: #fff;
  box-shadow: 0 18px 42px rgba(113, 18, 18, 0.35);
}

.header-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  background: rgba(255, 255, 255, 0.22);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.header-copy {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.payment-header .title {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 700;
  letter-spacing: -0.01em;
  text-transform: capitalize;
}

.payment-header .subtitle {
  margin: 0;
  font-size: 0.95rem;
  opacity: 0.92;
}

.order-summary-card,
.billing-section,
.card-section {
  background: var(--surface);
  border-radius: 1.25rem;
  border: 1px solid rgba(113, 18, 18, 0.14);
  padding: 1.75rem;
  box-shadow: 0 14px 36px rgba(17, 24, 39, 0.08);
  position: relative;
  z-index: 1;
}

.summary-header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1.1rem;
  color: var(--brand);
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  font-size: 0.85rem;
}

.summary-header h4 {
  margin: 0;
  font-size: 1rem;
  text-transform: initial;
}

.summary-content {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.98rem;
  color: var(--ink-2);
}

.summary-row .value {
  font-weight: 600;
  color: var(--ink-1);
}

.summary-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(113, 18, 18, 0.18), transparent);
}

.total-row {
  border-top: 1px solid rgba(113, 18, 18, 0.18);
  padding-top: 0.85rem;
  margin-top: 0.25rem;
  font-size: 1.2rem;
}

.total-row .label {
  font-weight: 600;
  color: var(--ink-1);
}

.total-row .value {
  color: var(--brand);
  font-weight: 700;
  font-size: 1.55rem;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  font-weight: 600;
  color: var(--brand);
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
  letter-spacing: 0.02em;
}

.section-title i {
  font-size: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
}

@media (min-width: 640px) {
  .form-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .full-width {
    grid-column: 1 / -1;
  }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--ink-1);
}

.required {
  color: var(--danger);
  font-weight: 700;
}

.optional {
  color: var(--ink-3);
  font-weight: 500;
  font-size: 0.8rem;
}

.form-input {
  width: 100%;
  padding: 0.85rem 1rem;
  border: 1px solid rgba(113, 18, 18, 0.18);
  border-radius: 0.75rem;
  font-size: 0.97rem;
  color: var(--ink-1);
  background: var(--surface);
  transition: border 0.2s ease, box-shadow 0.2s ease;
  outline: none;
}

.form-input::placeholder {
  color: var(--ink-4);
}

.form-input:focus {
  border-color: rgba(113, 18, 18, 0.55);
  box-shadow: 0 0 0 3px rgba(113, 18, 18, 0.16);
}

.card-element-wrapper {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  position: relative;
  z-index: 1;
}

.card-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  position: relative;
}

.card-field-row {
  display: flex;
  gap: 1rem;
}

.card-field-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--ink-3);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.card-element {
  min-height: 48px;
  padding: 12px 14px;
  width: 100%;
  border: 1px solid rgba(113, 18, 18, 0.2);
  border-radius: 0.85rem;
  background: var(--surface);
  box-shadow: inset 0 1px 2px rgba(17, 24, 39, 0.05);
  transition: border 0.2s ease, box-shadow 0.2s ease;
  position: relative;
}

.card-element:focus-within {
  border-color: rgba(113, 18, 18, 0.55);
  box-shadow: 0 0 0 3px rgba(113, 18, 18, 0.16);
}

/* Asegurar que los iframes de Stripe sean interactivos */
.card-element iframe {
  pointer-events: auto !important;
  position: relative;
  z-index: 1;
}

.error-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.85rem;
  padding: 0.7rem 1rem;
  border-radius: 0.75rem;
  background: rgba(220, 53, 69, 0.12);
  color: var(--danger);
  font-size: 0.9rem;
  font-weight: 600;
}

.accepted-cards {
  margin-top: 1.25rem;
  padding-top: 1.1rem;
  border-top: 1px solid rgba(113, 18, 18, 0.12);
}

.cards-label {
  display: block;
  font-size: 0.75rem;
  color: var(--ink-3);
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
}

.cards-icons {
  display: flex;
  gap: 0.4rem;
}

.card-badge {
  padding: 0.35rem 0.65rem;
  border-radius: 0.6rem;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.card-badge.visa {
  background: linear-gradient(135deg, #1a1f71 0%, #0e4c92 100%);
  color: #fff;
}

.card-badge.mastercard {
  background: linear-gradient(135deg, #eb001b 0%, #f79e1b 100%);
  color: #fff;
}

.card-badge.amex {
  background: linear-gradient(135deg, #006fcf 0%, #00a3e0 100%);
  color: #fff;
}

.alert {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 1rem;
  border: 1px solid;
  margin-bottom: 1.25rem;
}

.alert-title {
  margin: 0 0 0.3rem 0;
  font-weight: 700;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.alert-message {
  margin: 0;
  font-size: 0.85rem;
  color: var(--ink-2);
}

.alert-error {
  background: rgba(220, 53, 69, 0.08);
  border-color: rgba(220, 53, 69, 0.25);
  color: var(--danger);
}

.alert-success {
  background: rgba(40, 167, 69, 0.08);
  border-color: rgba(40, 167, 69, 0.25);
  color: var(--success);
}

.actions {
  display: flex;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.55rem;
  padding: 0.95rem 1.75rem;
  font-size: 0.98rem;
  font-weight: 600;
  border-radius: 0.9rem;
  border: none;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn:disabled {
  cursor: not-allowed;
  opacity: 0.65;
}

.btn-primary {
  flex: 1;
  background: linear-gradient(135deg, rgba(113, 18, 18, 0.95), rgba(255, 192, 16, 0.92));
  color: #fff;
  box-shadow: 0 12px 24px rgba(113, 18, 18, 0.25);
}

.btn-primary:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 32px rgba(113, 18, 18, 0.28);
}

.btn-primary:not(:disabled):active {
  transform: translateY(0);
}

.btn-primary.btn-success {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.95), rgba(4, 125, 74, 0.92));
  box-shadow: 0 12px 24px rgba(40, 167, 69, 0.22);
}

.btn-secondary {
  padding: 0.95rem 1.35rem;
  background: rgba(113, 18, 18, 0.06);
  color: var(--brand);
  border: 1px solid rgba(113, 18, 18, 0.25);
}

.btn-secondary:not(:disabled):hover {
  background: rgba(113, 18, 18, 0.1);
}

.security-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  padding: 1.1rem 1.5rem;
  border-radius: 1.1rem;
  background: rgba(113, 18, 18, 0.05);
  border: 1px solid rgba(113, 18, 18, 0.15);
  font-size: 0.82rem;
  color: var(--ink-3);
  flex-wrap: wrap;
  text-align: center;
}

.security-icon {
  width: 2rem;
  height: 2rem;
  border-radius: 0.65rem;
  background: rgba(113, 18, 18, 0.12);
  color: var(--brand);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
}

.stripe-logo {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.82rem;
}

.stripe-logo strong {
  color: #635bff;
  font-weight: 700;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 768px) {
  .payment-header {
    flex-direction: column;
    align-items: flex-start;
    border-radius: 1.25rem;
  }

  .order-summary-card,
  .billing-section,
  .card-section {
    padding: 1.5rem;
  }

  .card-field-row {
    flex-direction: column;
  }

  .actions {
    flex-direction: column-reverse;
  }

  .btn,
  .btn-secondary {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .payment-header {
    padding: 1.5rem;
  }

  .order-summary-card,
  .billing-section,
  .card-section {
    padding: 1.25rem;
  }

  .form-input {
    font-size: 0.9rem;
    padding: 0.75rem 0.9rem;
  }

  .card-element {
    min-height: 44px;
  }
}
</style>
