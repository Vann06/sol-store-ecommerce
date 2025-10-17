import { computed, ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useUserStore } from '@/stores/userStore'
import { useMessages } from './useMessages'

/**
 * Composable para manejar operaciones del carrito
 * Provee una interfaz simplificada para interactuar con el carrito
 */
export function useCart() {
  const cartStore = useCartStore()
  const userStore = useUserStore()
  const { showMessage } = useMessages()

  // Estado local para operaciones
  const isAdding = ref(false)
  const isRemoving = ref(false)

  // Computed properties reactivas
  const items = computed(() => cartStore.items)
  const totalItems = computed(() => cartStore.totalItems)
  const totalPrice = computed(() => cartStore.totalPrice)
  const isEmpty = computed(() => cartStore.isEmpty)
  const isLoading = computed(() => cartStore.loading)
  const error = computed(() => cartStore.error)
  const cartSummary = computed(() => cartStore.cartSummary)

  // Verificar si el usuario está autenticado
  const isAuthenticated = computed(() => userStore.isAuthenticated)

  // Métodos principales
  const addToCart = async (product, quantity = 1, showNotification = true) => {
    // Permitir invitados: el backend soporta carrito de invitado por sesión

    // Validar producto
    if (!product || !product.id) {
      if (showNotification) {
        showMessage('Producto no válido', 'error')
      }
      return { success: false, error: 'Producto no válido' }
    }

    // Validar stock si está disponible
    if (product.stock !== undefined && product.stock < quantity) {
      if (showNotification) {
        showMessage('Stock insuficiente', 'warning')
      }
      return { success: false, error: 'Stock insuficiente' }
    }

    // Clamp 1..100
    const clamp = (n) => Math.max(1, Math.min(Number(n) || 1, 100))
    const safeQty = clamp(quantity)

    isAdding.value = true

    try {
      const result = await cartStore.addToCart(product.id, safeQty, {
        id: product.id,
        nombre: product.nombre || product.name,
        precio_base: product.precio || product.price,
        precio: product.precio || product.price, // Para compatibilidad
        imagen: product.imagen || product.image,
        stock: product.stock
      })

      if (result.success && showNotification) {
        showMessage(result.message || 'Producto agregado al carrito', 'success')
      } else if (!result.success && showNotification) {
        showMessage(result.error || 'Error al agregar producto', 'error')
      }

      return result

    } catch (error) {
      console.error('Error in useCart.addToCart:', error)
      if (showNotification) {
        showMessage('Error inesperado al agregar producto', 'error')
      }
      return { success: false, error: error.message }
    } finally {
      isAdding.value = false
    }
  }

  const removeFromCart = async (itemId, showNotification = true) => {
    // Permitir invitados

    isRemoving.value = true

    try {
      const result = await cartStore.removeItem(itemId)

      if (result.success && showNotification) {
        showMessage(result.message || 'Producto eliminado del carrito', 'success')
      } else if (!result.success && showNotification) {
        showMessage(result.error || 'Error al eliminar producto', 'error')
      }

      return result

    } catch (error) {
      console.error('Error in useCart.removeFromCart:', error)
      if (showNotification) {
        showMessage('Error inesperado al eliminar producto', 'error')
      }
      return { success: false, error: error.message }
    } finally {
      isRemoving.value = false
    }
  }

  const updateQuantity = async (itemId, newQuantity, showNotification = true) => {
    // Permitir invitados

    try {
      const result = await cartStore.updateQuantity(itemId, newQuantity)

      if (result.success && showNotification) {
        showMessage(result.message || 'Cantidad actualizada', 'success')
      } else if (!result.success && showNotification) {
        showMessage(result.error || 'Error al actualizar cantidad', 'error')
      }

      return result

    } catch (error) {
      console.error('Error in useCart.updateQuantity:', error)
      if (showNotification) {
        showMessage('Error inesperado al actualizar cantidad', 'error')
      }
      return { success: false, error: error.message }
    }
  }

  const clearCart = async (showNotification = true) => {
    // Permitir invitados

    try {
      const result = await cartStore.clearCart()

      if (result.success && showNotification) {
        showMessage(result.message || 'Carrito limpiado', 'success')
      } else if (!result.success && showNotification) {
        showMessage(result.error || 'Error al limpiar carrito', 'error')
      }

      return result

    } catch (error) {
      console.error('Error in useCart.clearCart:', error)
      if (showNotification) {
        showMessage('Error inesperado al limpiar carrito', 'error')
      }
      return { success: false, error: error.message }
    }
  }

  // Métodos utilitarios

  const refreshCart = async () => {
    return await cartStore.fetchCart()
  }

  const clearError = () => {
    cartStore.clearError()
  }

  // Formateo de precio
  const formatPrice = (price) => {
    if (price == null || isNaN(price)) return ''
    return new Intl.NumberFormat('es-GT', {
      style: 'currency',
      currency: 'GTQ',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(Number(price))
  }

  // Helpers esperados por componentes
  const isProductInCart = (productId) => {
    if (!productId) return false
    return cartStore.isProductInCart(productId)
  }

  const getProductQuantity = (productId) => {
    if (!productId) return 0
    return cartStore.getProductQuantity(productId)
  }

  // Calcular descuentos y precio final (si los hay)
  const calculateDiscount = (product) => {
    if (!product) return 0
    const original = Number(product.precio_original ?? product.price_original ?? 0)
    const discountPct = Number(product.descuento ?? 0)
    if (original > 0 && discountPct > 0) {
      return (original * discountPct) / 100
    }
    const current = Number(product.precio ?? product.price ?? 0)
    return original > current && current > 0 ? (original - current) : 0
  }

  const getFinalPrice = (product) => {
    if (!product) return 0
    const original = Number(product.precio_original ?? product.price_original ?? 0)
    const discountPct = Number(product.descuento ?? 0)
    if (original > 0 && discountPct > 0) {
      return original * (1 - discountPct / 100)
    }
    return Number(product.precio ?? product.price ?? 0)
  }

  // Validar disponibilidad del producto
  const isProductAvailable = (product) => {
    return product && 
           product.stock > 0 && 
           product.activo !== false &&
           !product.discontinued
  }

  // Estado de carga específico para operaciones
  const operationStates = computed(() => ({
    isAdding: isAdding.value,
    isRemoving: isRemoving.value,
    isUpdating: cartStore.isUpdating,
    isLoading: cartStore.loading,
    hasError: !!cartStore.error
  }))

  return {
    // Estados reactivos
    items,
    totalItems,
    totalPrice,
    isEmpty,
    isLoading,
    error,
    cartSummary,
    isAuthenticated,
    operationStates,
  // Mensajería
  showMessage,

    // Métodos principales
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    refreshCart,
    clearError,

    // Utilidades
    isProductInCart,
    getProductQuantity,
    formatPrice,
    calculateDiscount,
    getFinalPrice,
    isProductAvailable
  }
}
