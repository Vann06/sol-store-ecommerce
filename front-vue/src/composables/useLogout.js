import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useCartStore } from '@/stores/cart'
import http from '@/http'

export function useLogout() {
  const router = useRouter()
  const userStore = useUserStore()
  const cartStore = useCartStore()

  const logout = async () => {
    console.log('🚪 Iniciando logout...')
    
    try {
      // Hacer logout en el backend
      const token = userStore.token || localStorage.getItem('auth_token')
      if (token) {
        console.log('📤 Enviando logout al backend...')
        await http.post('/logout', {}, {
          headers: { Authorization: `Bearer ${token}` }
        })
      }
    } catch (error) {
      console.error('❌ Error en logout del backend:', error)
      // Continuar con el logout local aunque falle el backend
    }

    // Limpiar TODOS los datos locales
    console.log('🧹 Limpiando datos locales...')
    
    // Limpiar user store
    userStore.user = null
    userStore.token = null
    userStore.isAuthenticated = false
    userStore.error = null
    
    // Limpiar localStorage
    localStorage.removeItem('auth_token')
    localStorage.clear() // Limpiar todo el localStorage
    
    // Limpiar sessionStorage
    sessionStorage.clear()
    
    // Limpiar el carrito (sin hacer llamadas al servidor)
    try {
      cartStore.clearLocalCart()
      console.log('🛒 Carrito limpiado')
    } catch (error) {
      console.error('Error limpiando carrito:', error)
    }
    
    console.log('✅ Logout completado')
    
    // Pequeño delay para asegurar que todo se limpió
    setTimeout(() => {
      // Redirigir al home y recargar la página
      window.location.href = '/'
    }, 100)
  }

  return {
    logout
  }
}