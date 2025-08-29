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
    // Verificar autenticación
    if (!isAuthenticated.value) {
      if (showNotification) {
        showMessage('Debes iniciar sesión para agregar productos al carrito', 'warning')
      }
      return { success: false, requiresAuth: true }
    }

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

    isAdding.value = true

    try {
      const result = await cartStore.addToCart(product.id, quantity, {
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
    if (!isAuthenticated.value) {
      if (showNotification) {
        showMessage('Debes iniciar sesión', 'warning')
      }
      return { success: false, requiresAuth: true }
    }

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
    if (!isAuthenticated.value) {
      if (showNotification) {
        showMessage('Debes iniciar sesión', 'warning')
      }
      return { success: false, requiresAuth: true }
    }

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
    if (!isAuthenticated.value) {
      if (showNotification) {
        showMessage('Debes iniciar sesión', 'warning')
      }
      return { success: false, requiresAuth: true }
    }

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
  const isProductInCart = (productId) => {
    return cartStore.isProductInCart(productId)
  }

  const getProductQuantity = (productId) => {
    return cartStore.getProductQuantity(productId)
  }

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

  // Calcular descuentos (si los hay)
  const calculateDiscount = (originalPrice, discountPercent) => {
    return originalPrice * (discountPercent / 100)
  }

  const getFinalPrice = (originalPrice, discountPercent = 0) => {
    return originalPrice - calculateDiscount(originalPrice, discountPercent)
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
