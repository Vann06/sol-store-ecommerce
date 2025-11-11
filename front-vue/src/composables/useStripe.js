import { ref } from 'vue'
import { loadStripe } from '@stripe/stripe-js'
import http from '@/http'

export function useStripe() {
  const loading = ref(false)
  const error = ref(null)
  const stripe = ref(null)
  const elements = ref(null)
  const cardElement = ref(null)
  const cardElementParts = ref({ number: null, expiry: null, cvc: null })

  /**
   * Inicializar Stripe con la clave publicable
   */
  const initializeStripe = async (publishableKey) => {
    try {
      stripe.value = await loadStripe(publishableKey)
      return stripe.value
    } catch (err) {
      error.value = 'Error al cargar Stripe: ' + err.message
      console.error('Error initializing Stripe:', err)
      return null
    }
  }

  /**
   * Crear el elemento de tarjeta de Stripe
   */
  const createCardElement = (containers = {}) => {
    if (!stripe.value) {
      error.value = 'Stripe no está inicializado'
      return null
    }

    if (!containers.number || !containers.expiry || !containers.cvc) {
      error.value = 'No se pudieron montar los campos de tarjeta'
      return null
    }

    try {
      elements.value = stripe.value.elements()
      
      // Estilo personalizado para el input de tarjeta
      const style = {
        base: {
          color: '#1f2937',
          fontFamily: '"Poppins", "Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: 'antialiased',
          fontSize: '16px',
          lineHeight: '1.5',
          '::placeholder': {
            color: '#9ca3af'
          }
        },
        invalid: {
          color: '#dc3545',
          iconColor: '#dc3545'
        }
      }

      const handleChange = (event) => {
        if (event.error) {
          error.value = event.error.message
        } else {
          error.value = null
        }
      }

      const numberElement = elements.value.create('cardNumber', {
        style,
        placeholder: '1234 1234 1234 1234'
      })
      numberElement.mount(containers.number)
      numberElement.on('change', handleChange)

      const expiryElement = elements.value.create('cardExpiry', {
        style,
        placeholder: 'MM / AA'
      })
      expiryElement.mount(containers.expiry)
      expiryElement.on('change', handleChange)

      const cvcElement = elements.value.create('cardCvc', {
        style,
        placeholder: 'CVC'
      })
      cvcElement.mount(containers.cvc)
      cvcElement.on('change', handleChange)

      cardElementParts.value = {
        number: numberElement,
        expiry: expiryElement,
        cvc: cvcElement
      }

      cardElement.value = numberElement

      return numberElement
    } catch (err) {
      error.value = 'Error al crear elemento de tarjeta: ' + err.message
      console.error('Error creating card element:', err)
      return null
    }
  }

  /**
   * Crear una intención de pago
   */
  const createPaymentIntent = async (orderId, amount) => {
    loading.value = true
    error.value = null

    try {
      const response = await http.post('/payments/create-intent', {
        order_id: orderId,
        amount: amount
      })

      if (response.data.success) {
        return {
          success: true,
          clientSecret: response.data.client_secret,
          paymentIntentId: response.data.payment_intent_id,
          publishableKey: response.data.publishable_key
        }
      } else {
        error.value = response.data.message || 'Error al crear intención de pago'
        return { success: false, error: error.value }
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'Error al crear intención de pago'
      error.value = errorMessage
      console.error('Error creating payment intent:', err)
      return { success: false, error: errorMessage }
    } finally {
      loading.value = false
    }
  }

  /**
   * Confirmar el pago
   */
  const confirmPayment = async (clientSecret, billingDetails = {}) => {
    loading.value = true
    error.value = null

    try {
      if (!stripe.value || !cardElement.value) {
        throw new Error('Stripe no está inicializado correctamente')
      }

      const { error: stripeError, paymentIntent } = await stripe.value.confirmCardPayment(
        clientSecret,
        {
          payment_method: {
            card: cardElement.value,
            billing_details: billingDetails
          }
        }
      )

      if (stripeError) {
        error.value = stripeError.message
        return {
          success: false,
          error: stripeError.message
        }
      }

      return {
        success: true,
        paymentIntent: paymentIntent
      }
    } catch (err) {
      error.value = err.message || 'Error al confirmar el pago'
      console.error('Error confirming payment:', err)
      return {
        success: false,
        error: error.value
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Verificar el pago en el backend
   */
  const verifyPayment = async (paymentIntentId) => {
    loading.value = true
    error.value = null

    try {
      const response = await http.post('/payments/verify', {
        payment_intent_id: paymentIntentId
      })

      if (response.data.success) {
        return {
          success: true,
          orderId: response.data.order_id,
          paymentStatus: response.data.payment_status,
          paidAt: response.data.paid_at
        }
      } else {
        error.value = response.data.message || 'Error al verificar el pago'
        return { success: false, error: error.value }
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'Error al verificar el pago'
      error.value = errorMessage
      console.error('Error verifying payment:', err)
      return { success: false, error: errorMessage }
    } finally {
      loading.value = false
    }
  }

  /**
   * Cancelar un pago
   */
  const cancelPayment = async (paymentIntentId) => {
    loading.value = true
    error.value = null

    try {
      const response = await http.post('/payments/cancel', {
        payment_intent_id: paymentIntentId
      })

      return response.data
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'Error al cancelar el pago'
      error.value = errorMessage
      console.error('Error canceling payment:', err)
      return { success: false, error: errorMessage }
    } finally {
      loading.value = false
    }
  }

  /**
   * Destruir el elemento de tarjeta
   */
  const destroyCardElement = () => {
    if (cardElementParts.value.number) {
      cardElementParts.value.number.destroy()
    }
    if (cardElementParts.value.expiry) {
      cardElementParts.value.expiry.destroy()
    }
    if (cardElementParts.value.cvc) {
      cardElementParts.value.cvc.destroy()
    }

    cardElement.value = null
    cardElementParts.value = { number: null, expiry: null, cvc: null }
  }

  return {
    loading,
    error,
    stripe,
    cardElement,
    initializeStripe,
    createCardElement,
    createPaymentIntent,
    confirmPayment,
    verifyPayment,
    cancelPayment,
    destroyCardElement
  }
}
