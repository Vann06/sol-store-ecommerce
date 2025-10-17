import { defineStore } from 'pinia'
import { useCartStore } from './cart'
import api from '@/utils/http'
import http from '@/http'  // Nuevo servicio HTTP con JWT

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token'), // Cargar token desde localStorage
        isLoading: false,
        error: null,
        isAuthenticated: false
    }),
    
    getters: {
        getUser: (state) => state.user,
        isLoggedIn: (state) => !!state.user && !!state.token,
        // Nota: evitamos definir un getter con el mismo nombre que el estado
        // para no provocar advertencias de Pinia.
        userName: (state) => {
            if (!state.user) return ''
            return `${state.user.first_name || ''} ${state.user.last_name || ''}`.trim() || state.user.name || 'Usuario'
        },
        userEmail: (state) => state.user?.email || '',
        userRole: (state) => state.user?.role || 'cliente',
        isAdmin: (state) => state.user?.role === 'admin'
    },
    
    persist: {
        key: 'user-store',
        storage: localStorage,
        paths: ['user', 'token'] // Solo persistir estos campos
    },
    
    actions: {
        setUser(userData) {
            this.user = userData
            this.error = null
        },
        
        setToken(token) {
            this.token = token
            if (token) {
                localStorage.setItem('auth_token', token)
            } else {
                localStorage.removeItem('auth_token')
            }
        },
        
        setUserAndToken(userData, token) {
            this.setUser(userData)
            this.setToken(token)
            
            // Sincronizar carrito después del login
            if (token) {
                const cartStore = useCartStore()
                cartStore.syncCart()
            }
        },
        
        updateUser(userData) {
            if (this.user) {
                this.user = { ...this.user, ...userData }
            }
        },
        
        clearUser() {
            this.user = null
            this.token = null
            this.error = null
            this.isLoading = false
            this.isAuthenticated = false
            
            // Limpiar localStorage
            localStorage.removeItem('auth_token')
            localStorage.removeItem('access_token')
            
            // Limpiar carrito
            const cartStore = useCartStore()
            cartStore.clearLocalCart()
        },
        
        logout() {
            this.clearUser()
        },
        
        setLoading(loading) {
            this.isLoading = loading
        },
        
        setError(error) {
            this.error = error
        },
        
        clearError() {
            this.error = null
        },

        // ==========================================
        // NUEVOS MÉTODOS JWT
        // ==========================================
        
        async login(credentials) {
            this.setLoading(true)
            this.clearError()
            
            try {
                console.log('🔐 Enviando credenciales con JWT...')
                const response = await http.post('/login', credentials)
                
                if (response.data.access_token && response.data.user) {
                    // Guardar token en localStorage
                    localStorage.setItem('access_token', response.data.access_token)
                    
                    // Establecer datos del usuario
                    this.setUserAndToken(response.data.user, response.data.access_token)
                    this.isAuthenticated = true
                    
                    console.log('✅ Login exitoso:', response.data.user)
                    return { success: true, data: response.data }
                }
                
                throw new Error('Respuesta inválida del servidor')
                
            } catch (error) {
                console.error('❌ Error en login:', error)
                const errorMessage = error.response?.data?.message || error.message || 'Error en el login'
                this.setError(errorMessage)
                return { success: false, error: errorMessage }
            } finally {
                this.setLoading(false)
            }
        },

        async checkAuth() {
            const token = localStorage.getItem('access_token')
            if (!token) {
                console.log('🚫 No hay token disponible')
                return false
            }
            
            try {
                console.log('🔍 Verificando autenticación...')
                const response = await http.get('/me')
                
                if (response.data.user) {
                    this.setUser(response.data.user)
                    this.setToken(token)
                    this.isAuthenticated = true
                    console.log('✅ Usuario autenticado:', response.data.user)
                    return true
                }
            } catch (error) {
                console.error('❌ Error verificando autenticación:', error)
                this.clearUser()
            }
            
            return false
        },

        updateToken(newToken) {
            console.log('🔄 Actualizando token...')
            this.setToken(newToken)
        },

        async logoutWithAPI() {
            this.setLoading(true)
            
            try {
                if (this.token) {
                    console.log('🚪 Haciendo logout con API...')
                    await api.post('/logout')
                }
            } catch (error) {
                console.error('❌ Error en logout:', error)
            } finally {
                // Limpiar estado local siempre
                this.clearUser()
                this.isAuthenticated = false
                this.setLoading(false)
                console.log('✅ Logout completado')
            }
        },

        // Mantener compatibilidad con el logout existente
        logout() {
            // Llamar al logout con API para limpiar tokens en el servidor
            this.logoutWithAPI()
        },

        // Método para inicializar el store al cargar la app
        async initialize() {
            if (this.token) {
                await this.checkAuth()
            }
        }
    }
})