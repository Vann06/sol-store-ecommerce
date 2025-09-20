import { defineStore } from 'pinia'
import api from '@/utils/http'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'), // Fallback para compatibilidad
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    isLoggedIn: (state) => state.isAuthenticated && !!state.user,
    userRole: (state) => state.user?.role || 'cliente',
    isAdmin: (state) => state.user?.role === 'admin'
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.post('/login', credentials)
        
        if (response.data.access_token && response.data.user) {
          this.token = response.data.access_token
          this.user = response.data.user
          this.isAuthenticated = true
          
          // Mantener compatibilidad con localStorage (opcional)
          localStorage.setItem('auth_token', response.data.access_token)
          
          return { success: true, data: response.data }
        }
        
        throw new Error('Respuesta inválida del servidor')
        
      } catch (error) {
        this.error = error.response?.data?.message || error.message
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async checkAuth() {
      if (!this.token) return false
      
      try {
        const response = await api.get('/me')
        
        if (response.data.user) {
          this.user = response.data.user
          this.isAuthenticated = true
          return true
        }
      } catch (error) {
        console.error('Error verificando autenticación:', error)
        this.logout()
      }
      
      return false
    },

    updateToken(newToken) {
      this.token = newToken
      localStorage.setItem('auth_token', newToken)
    },

    async logout() {
      try {
        if (this.token) {
          await api.post('/logout')
        }
      } catch (error) {
        console.error('Error en logout:', error)
      } finally {
        // Limpiar estado local siempre
        this.user = null
        this.token = null
        this.isAuthenticated = false
        this.error = null
        
        // Limpiar localStorage
        localStorage.removeItem('auth_token')
        
        // Limpiar headers de axios
        delete api.defaults.headers.common['Authorization']
      }
    },

    clearError() {
      this.error = null
    }
  },

  persist: {
    paths: ['user', 'isAuthenticated'] // No persistir token por seguridad
  }
})
