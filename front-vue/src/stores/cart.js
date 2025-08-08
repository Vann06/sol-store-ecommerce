import { defineStore } from 'pinia'
import axios from 'axios'
import { useUserStore } from './userStore'

const API_BASE_URL = 'http://localhost:8000/api'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    total: 0,
    itemCount: 0,
    isUpdating: false
  }),

  getters: {
    // Calcular total de items (usando la cantidad de cada item)
    totalItems: (state) => {
      return state.items.reduce((total, item) => total + (item.cantidad || 0), 0)
    },

    // Calcular precio total
    totalPrice: (state) => {
      return state.items.reduce((total, item) => {
        const precio = item.precio_unitario || item.subtotal / item.cantidad || 0
        return total + (precio * item.cantidad)
      }, 0)
    },

    // Verificar si un producto está en el carrito
    isProductInCart: (state) => (productId) => {
      return state.items.some(item => item.producto_id === productId)
    },

    // Obtener cantidad de un producto específico
    getProductQuantity: (state) => (productId) => {
      const item = state.items.find(item => item.producto_id === productId)
      return item ? item.cantidad : 0
    },

    // Verificar si el carrito está vacío
    isEmpty: (state) => state.items.length === 0,

    // Obtener resumen del carrito
    cartSummary: (state) => ({
      itemCount: state.totalItems,
      total: state.totalPrice || state.total, // Usar el total del servidor si está disponible
      isEmpty: state.items.length === 0
    })
  },

  actions: {
    // Obtener token de autenticación
    getAuthToken() {
      const userStore = useUserStore()
      return userStore.token || localStorage.getItem('auth_token')
    },

    // Configurar headers de autenticación
    getAuthHeaders() {
      const token = this.getAuthToken()
      return token ? { Authorization: `Bearer ${token}` } : {}
    },

    // Limpiar errores
    clearError() {
      this.error = null
    },

    // Obtener carrito del servidor
    async fetchCart() {
      console.log('🛒 Fetching cart from server...')
      this.loading = true
      this.error = null

      try {
        const token = this.getAuthToken()
        console.log('🔑 Token available:', !!token)
        
        if (!token) {
          console.log('❌ No token, clearing cart')
          this.items = []
          return { success: false, message: 'Usuario no autenticado' }
        }

        console.log('📡 Making request to:', `${API_BASE_URL}/carrito`)
        const response = await axios.get(`${API_BASE_URL}/carrito`, {
          headers: this.getAuthHeaders()
        })

        console.log('📦 Cart response:', response.data)
        
        // El backend devuelve la estructura directamente, no dentro de 'data'
        this.items = response.data.items || []
        this.total = response.data.total || 0
        this.itemCount = response.data.cantidad_total_productos || response.data.cantidad_items || 0

        console.log('✅ Cart loaded:', {
          itemsCount: this.items.length,
          total: this.total,
          itemCount: this.itemCount,
          items: this.items
        })

        return { success: true, data: response.data }

      } catch (error) {
        console.error('❌ Error fetching cart:', error)
        console.error('Response data:', error.response?.data)
        
        this.error = error.response?.data?.message || 'Error al cargar el carrito'
        
        // Si el token es inválido, limpiar el carrito
        if (error.response?.status === 401) {
          console.log('🔒 401 error, clearing cart and logging out')
          this.items = []
          const userStore = useUserStore()
          userStore.logout()
        }

        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    // Agregar producto al carrito
    async addToCart(productId, quantity = 1, productData = null) {
      console.log('🛒 Adding to cart:', { productId, quantity, productData })
      this.isUpdating = true
      this.error = null

      try {
        const token = this.getAuthToken()
        if (!token) {
          console.log('❌ No token for addToCart')
          throw new Error('Debes iniciar sesión para agregar productos al carrito')
        }

        // Validaciones
        if (!productId) {
          throw new Error('ID de producto requerido')
        }

        if (quantity < 1) {
          throw new Error('La cantidad debe ser mayor a 0')
        }

        console.log('📡 Making add to cart request...')
        const response = await axios.post(`${API_BASE_URL}/carrito/agregar`, {
          producto_id: productId,
          cantidad: quantity
        }, {
          headers: this.getAuthHeaders()
        })

        console.log('✅ Add to cart response:', response.data)

        // Actualizar el carrito local si es posible para mejor UX
        if (productData) {
          const existingItem = this.items.find(item => 
            item.producto_id === productId || item.id === productId
          )
          
          if (existingItem) {
            console.log('📝 Updating existing item quantity')
            existingItem.cantidad += quantity
          } else {
            console.log('🆕 Adding new item to local cart')
            this.items.push({
              id: Date.now(), // ID temporal
              producto_id: productId,
              cantidad: quantity,
              producto: productData
            })
          }
        }

        // Refrescar desde el servidor para datos actualizados
        console.log('🔄 Refreshing cart from server...')
        await this.fetchCart()

        return { 
          success: true, 
          message: `${productData?.nombre || 'Producto'} agregado al carrito`,
          data: response.data 
        }

      } catch (error) {
        console.error('❌ Error adding to cart:', error)
        console.error('Response data:', error.response?.data)
        
        this.error = error.response?.data?.message || error.message || 'Error al agregar al carrito'
        
        return { success: false, error: this.error }
      } finally {
        this.isUpdating = false
      }
    },

    // Actualizar cantidad de un item
    async updateQuantity(itemId, newQuantity) {
      this.isUpdating = true
      this.error = null

      try {
        const token = this.getAuthToken()
        if (!token) {
          throw new Error('Usuario no autenticado')
        }

        if (newQuantity < 1) {
          // Si la cantidad es 0 o menor, eliminar el item
          return await this.removeItem(itemId)
        }

        // Actualizar optimísticamente en el estado local
        const item = this.items.find(item => item.id === itemId)
        if (item) {
          const oldQuantity = item.cantidad
          item.cantidad = newQuantity

          try {
            await axios.put(`${API_BASE_URL}/carrito/actualizar/${itemId}`, {
              cantidad: newQuantity
            }, {
              headers: this.getAuthHeaders()
            })

            // Refrescar desde el servidor
            await this.fetchCart()

            return { success: true, message: 'Cantidad actualizada' }

          } catch (error) {
            // Revertir cambio optimista en caso de error
            item.cantidad = oldQuantity
            throw error
          }
        } else {
          throw new Error('Item no encontrado en el carrito')
        }

      } catch (error) {
        console.error('Error updating quantity:', error)
        this.error = error.response?.data?.message || error.message || 'Error al actualizar cantidad'
        
        return { success: false, error: this.error }
      } finally {
        this.isUpdating = false
      }
    },

    // Eliminar item del carrito
    async removeItem(itemId) {
      this.isUpdating = true
      this.error = null

      try {
        const token = this.getAuthToken()
        if (!token) {
          throw new Error('Usuario no autenticado')
        }

        // Guardar referencia del item para posible rollback
        const itemIndex = this.items.findIndex(item => item.id === itemId)
        const removedItem = itemIndex !== -1 ? this.items[itemIndex] : null

        // Eliminar optimísticamente del estado local
        if (itemIndex !== -1) {
          this.items.splice(itemIndex, 1)
        }

        try {
          await axios.delete(`${API_BASE_URL}/carrito/eliminar/${itemId}`, {
            headers: this.getAuthHeaders()
          })

          // Refrescar desde el servidor
          await this.fetchCart()

          return { 
            success: true, 
            message: `${removedItem?.producto?.nombre || 'Producto'} eliminado del carrito` 
          }

        } catch (error) {
          // Revertir eliminación optimista en caso de error
          if (removedItem && itemIndex !== -1) {
            this.items.splice(itemIndex, 0, removedItem)
          }
          throw error
        }

      } catch (error) {
        console.error('Error removing item:', error)
        this.error = error.response?.data?.message || error.message || 'Error al eliminar producto'
        
        return { success: false, error: this.error }
      } finally {
        this.isUpdating = false
      }
    },

    // Limpiar carrito completamente
    async clearCart() {
      this.loading = true
      this.error = null

      try {
        const token = this.getAuthToken()
        if (!token) {
          throw new Error('Usuario no autenticado')
        }

        await axios.delete(`${API_BASE_URL}/carrito/limpiar`, {
          headers: this.getAuthHeaders()
        })

        this.items = []
        this.total = 0
        this.itemCount = 0

        return { success: true, message: 'Carrito limpiado' }

      } catch (error) {
        console.error('Error clearing cart:', error)
        this.error = error.response?.data?.message || error.message || 'Error al limpiar carrito'
        
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    // Sincronizar carrito (útil después del login)
    async syncCart() {
      return await this.fetchCart()
    },

    // Limpiar carrito local (logout)
    clearLocalCart() {
      this.items = []
      this.total = 0
      this.itemCount = 0
      this.error = null
      this.loading = false
      this.isUpdating = false
    }
  }
})
