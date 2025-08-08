import { defineStore } from 'pinia'
import { useCartStore } from './cart'

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        token: null,
        isLoading: false,
        error: null
    }),
    
    getters: {
        getUser: (state) => state.user,
        isLoggedIn: (state) => !!state.user && !!state.token,
        isAuthenticated: (state) => !!state.user && !!state.token,
        userName: (state) => {
            if (!state.user) return ''
            return `${state.user.first_name || ''} ${state.user.last_name || ''}`.trim() || state.user.name || 'Usuario'
        },
        userEmail: (state) => state.user?.email || '',
        // isAdmin: (state) => state.user && state.user.role === 'admin',
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
            
            // Limpiar localStorage
            localStorage.removeItem('auth_token')
            
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
        }
    }
})