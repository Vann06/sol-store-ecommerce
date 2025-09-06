import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUserStore = defineStore('user', () => {
  const user = ref(null)

  function setUser(data) {
    user.value = data
    
    // Track login event para Clarity
    if (typeof window !== 'undefined' && window.clarity) {
      // Identificar usuario en Clarity
      if (data?.id) {
        window.clarity('identify', data.id.toString(), {
          email: data.email,
          role: data.role || 'cliente',
          first_name: data.first_name
        })
      }
      
      // Track evento de login
      window.clarity('event', 'user_login_success', {
        user_id: data?.id,
        user_role: data?.role || 'cliente',
        method: 'email'
      })
    }
  }

  function logout() {
    // Track logout event antes de limpiar datos
    if (typeof window !== 'undefined' && window.clarity && user.value) {
      window.clarity('event', 'user_logout', {
        user_id: user.value.id,
        session_duration: 'unknown' // Se podría calcular si se guarda timestamp de login
      })
    }
    
    user.value = null
  }

  return { user, setUser, logout }

},
{
  persist:true
}
)
