import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import http from '@/http'

export function useLogout() {
  const router = useRouter()
  const userStore = useUserStore()

  const logout = async () => {
    try {
      // Hacer logout en el backend
      const token = userStore.token || localStorage.getItem('auth_token')
      if (token) {
        await http.post('/logout', {}, {
          headers: { Authorization: `Bearer ${token}` }
        })
      }
    } catch (error) {
      console.error('Error en logout del backend:', error)
      // Continuar con el logout local aunque falle el backend
    } finally {
      // Usar el nuevo método del store que limpia todo
      userStore.logout()
      
      // Redirigir inmediatamente al home con mensaje de éxito
      await router.push({ 
        path: '/', 
        query: { 
          message: 'Sesión cerrada exitosamente',
          messageType: 'success'
        } 
      })
      
      // Forzar recarga completa de la página para asegurar que todo se limpie
      window.location.href = '/'
    }
  }

  return {
    logout
  }
}
