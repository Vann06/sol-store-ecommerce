import { onMounted } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useCartStore } from '@/stores/cart'
import http from '@/http'

/**
 * Composable para inicializar la aplicación
 * Se encarga de verificar el token almacenado
 */
export function useAppInit() {
  const userStore = useUserStore()
  const cartStore = useCartStore()

  const initializeApp = async () => {
    try {
      // Verificar si hay un token almacenado
      const storedToken = localStorage.getItem('auth_token')
      
  if (storedToken) {
        // El token se configura automáticamente via interceptores en http.js
        
        // Solo verificar el token si no hay usuario en el store
        if (!userStore.user) {
          try {
            await verifyToken(storedToken)
          } catch (error) {
            // Si el token es inválido, limpiar todo silenciosamente
            console.warn('Token stored is invalid, clearing...')
            await cleanupInvalidToken()
          }
        } else {
          // Si ya hay usuario, solo cargar el carrito
          userStore.setToken(storedToken)
          try {
            await cartStore.fetchCart()
          } catch (error) {
            console.warn('Could not load cart:', error.message)
          }
        }
      }
      // Si no hay token, hidratar carrito de invitado
      if (!storedToken) {
        cartStore.initIfGuest()
      }
    } catch (error) {
      console.error('Error initializing app:', error)
      // En caso de error general, limpiar todo
      await cleanupInvalidToken()
    }
  }

  const verifyToken = async (token) => {
    try {
      // Intentar obtener información del usuario con el token
      const response = await http.get('/user', {
        headers: { Authorization: `Bearer ${token}` },
        timeout: 5000 // 5 segundos de timeout
      })

      // Verificar que la respuesta tenga la estructura esperada
      if (response.data && (response.data.user || response.data.id)) {
        // Si viene como { user: {...} }
        const userData = response.data.user || response.data
        
        userStore.setUserAndToken(userData, token)
        
        // Cargar carrito
        try {
          await cartStore.fetchCart()
        } catch (cartError) {
          console.warn('Could not load cart after login:', cartError.message)
        }
      } else {
        throw new Error('Invalid response structure')
      }
    } catch (error) {
      console.warn('Token verification failed:', error.message)
      throw error // Re-lanzar para que lo maneje initializeApp
    }
  }

  const cleanupInvalidToken = async () => {
    // Limpiar localStorage
    localStorage.removeItem('auth_token')
    
    // Limpiar stores (el http.js maneja los headers automáticamente)
    userStore.logout() // Esto ya limpia el carrito también
  }

  // Auto-inicializar al montar, pero sin bloquear la UI
  onMounted(() => {
    // Usar setTimeout para no bloquear el renderizado inicial
    setTimeout(() => {
      initializeApp()
    }, 100)
  })

  return {
    initializeApp,
    verifyToken,
    cleanupInvalidToken
  }
}
