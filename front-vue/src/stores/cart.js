import { defineStore } from 'pinia'
import http from '@/http'
import { useUserStore } from './userStore'

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
    // Inicializador ligero para invitados
    initIfGuest() {
      const userStore = useUserStore()
      if (!userStore?.token && (!this.items || this.items.length === 0)) {
        this.hydrateFromLocalStorage()
      }
    },
    // Clave para persistencia local
    getLocalKey() {
      return 'sol_cart_v1'
    },

    // Persistir carrito a localStorage (modo invitado y como respaldo)
    persistToLocalStorage() {
      try {
        const payload = {
          items: this.items.map(i => ({
            producto_id: i.producto_id ?? i.id,
            nombre: i.producto?.nombre || i.nombre,
            imagen: i.producto?.imagen || i.imagen,
            categoria: i.producto?.categoria || i.categoria,
            precio_unitario: i.precio_unitario || i.producto?.precio || 0,
            cantidad: i.cantidad || 1
          }))
        }
        localStorage.setItem(this.getLocalKey(), JSON.stringify(payload))
      } catch (e) {
        console.warn('No se pudo persistir carrito local:', e)
      }
    },

    // Hidratar carrito desde localStorage (cuando no hay sesión)
    hydrateFromLocalStorage() {
      try {
        const raw = localStorage.getItem(this.getLocalKey())
        if (!raw) return
        const data = JSON.parse(raw)
        if (Array.isArray(data.items)) {
          this.items = data.items.map(it => ({
            id: `${it.producto_id}-local`,
            producto_id: it.producto_id,
            cantidad: Math.max(1, Math.min(Number(it.cantidad || 1), 100)),
            producto: {
              nombre: it.nombre,
              imagen: it.imagen,
              categoria: it.categoria,
              precio: it.precio_unitario
            },
            precio_unitario: it.precio_unitario,
            subtotal: (Number(it.precio_unitario || 0) * Math.max(1, Math.min(Number(it.cantidad || 1), 100)))
          }))
          // No confiamos en totales guardados; se derivan de items
          this.total = this.items.reduce((acc, x) => acc + (x.precio_unitario || 0) * (x.cantidad || 0), 0)
          this.itemCount = this.items.reduce((acc, x) => acc + (x.cantidad || 0), 0)
        }
      } catch (e) {
        console.warn('No se pudo hidratar carrito local:', e)
      }
    },

    // En login: mergear carrito local con el del servidor y luego limpiar local
    async mergeLocalCartToServer() {
      const userStore = useUserStore()
      if (!userStore?.token) return
      try {
        const raw = localStorage.getItem(this.getLocalKey())
        if (!raw) return
        const data = JSON.parse(raw)
        if (!Array.isArray(data.items) || data.items.length === 0) return

        for (const it of data.items) {
          const cantidad = Math.max(1, Math.min(Number(it.cantidad || 1), 100))
          try {
            await http.post('/carrito/agregar', {
              producto_id: it.producto_id,
              cantidad
            }, { headers: this.getAuthHeaders() })
          } catch (e) {
            console.warn('No se pudo enviar item local al servidor:', it.producto_id, e?.response?.data || e.message)
          }
        }
        // Refrescar carrito desde servidor
        await this.fetchCart()
        // Limpiar copia local al finalizar merge
        localStorage.removeItem(this.getLocalKey())
      } catch (e) {
        console.warn('Fallo al mergear carrito local con servidor:', e)
      }
    },

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

        console.log('📡 Making request to: /carrito')
        const response = await http.get('/carrito', {
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

        // En caso de error y no autenticado, intentar usar carrito local
        if (error.response?.status === 401) {
          this.hydrateFromLocalStorage()
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
  // Token no obligatorio, backend soporta invitados

        // Validaciones
        if (!productId) {
          throw new Error('ID de producto requerido')
        }

        if (quantity < 1) {
          throw new Error('La cantidad debe ser mayor a 0')
        }

        // Tope absoluto 100 por producto
        const clamp = (n) => Math.max(1, Math.min(Number(n) || 1, 100))
        let safeQty = clamp(quantity)

        console.log('📡 Making add to cart request...')
        const response = await http.post('/carrito/agregar', {
          producto_id: productId,
          cantidad: safeQty
        }, {
          headers: this.getAuthHeaders()
        })

        console.log('✅ Add to cart response:', response.data)

        // Track add to cart event para Clarity
        if (typeof window !== 'undefined' && window.clarity && productData) {
          window.clarity('event', 'add_to_cart', {
            product_id: productId,
            product_name: productData.nombre || 'Unknown Product',
            category: productData.categoria || 'uncategorized',
            quantity: quantity,
            price: productData.precio || 0,
            value: (productData.precio || 0) * quantity,
            cart_total_items: this.totalItems + quantity
          })
        }

        // Actualizar el carrito local si es posible para mejor UX
        if (productData) {
          const existingItem = this.items.find(item => 
            item.producto_id === productId || item.id === productId
          )
          
          if (existingItem) {
            console.log('📝 Updating existing item quantity')
            existingItem.cantidad = Math.min(100, (existingItem.cantidad || 0) + safeQty)
          } else {
            console.log('🆕 Adding new item to local cart')
            this.items.push({
              id: Date.now(), // ID temporal
              producto_id: productId,
              cantidad: safeQty,
              producto: productData
            })
          }
          // Persistir local para invitados
          this.persistToLocalStorage()
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
  // Permitir invitados (backend resuelve por sesión/fingerprint)

        // Clamp 1..100
        const next = Math.max(1, Math.min(Number(newQuantity) || 1, 100))

        if (next < 1) {
          // Si la cantidad es 0 o menor, eliminar el item
          return await this.removeItem(itemId)
        }

        // Actualizar optimísticamente en el estado local
        const item = this.items.find(item => item.id === itemId)
        if (item) {
          const oldQuantity = item.cantidad
          item.cantidad = next

          try {
            await http.put(`/carrito/actualizar/${itemId}`, {
              cantidad: next
            }, {
              headers: this.getAuthHeaders()
            })

            // Refrescar desde el servidor
            await this.fetchCart()
            // Persistir local para invitados
            this.persistToLocalStorage()

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
  // Permitir invitados

        // Guardar referencia del item para posible rollback y tracking
        const itemIndex = this.items.findIndex(item => item.id === itemId)
        const removedItem = itemIndex !== -1 ? this.items[itemIndex] : null

        // Track remove from cart event para Clarity antes de eliminar
        if (typeof window !== 'undefined' && window.clarity && removedItem) {
          window.clarity('event', 'remove_from_cart', {
            product_id: removedItem.producto_id || removedItem.id,
            product_name: removedItem.producto?.nombre || 'Unknown Product',
            category: removedItem.producto?.categoria || 'uncategorized',
            quantity: removedItem.cantidad || 1,
            price: removedItem.producto?.precio || removedItem.precio_unitario || 0,
            cart_total_items: this.totalItems - (removedItem.cantidad || 1)
          })
        }

        // Eliminar optimísticamente del estado local
        if (itemIndex !== -1) {
          this.items.splice(itemIndex, 1)
        }

        try {
          await http.delete(`/carrito/eliminar/${itemId}`, {
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
  // Permitir invitados

  // Backend route is /carrito/vaciar
  await http.delete('/carrito/vaciar', {
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
      // Primero intenta mergear el carrito local en el servidor si hay sesión
      await this.mergeLocalCartToServer()
      // Luego obtener el carrito oficial desde el servidor
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
      try { localStorage.removeItem(this.getLocalKey()) } catch {}
    }
  }
})
