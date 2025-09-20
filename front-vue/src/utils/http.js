import axios from 'axios'
import { useUserStore } from '@/stores/user'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true, // Para cookies HttpOnly
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor - agregar token a headers
api.interceptors.request.use(
  (config) => {
    const userStore = useUserStore()
    const token = userStore.token
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - manejar renovación automática
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config
    
    // Si es error 401 y no es la ruta de login/refresh
    if (error.response?.status === 401 && !originalRequest._retry && 
        !originalRequest.url.includes('/login') && 
        !originalRequest.url.includes('/refresh')) {
      
      originalRequest._retry = true
      
      try {
        console.log('🔄 Token expirado, intentando renovar...')
        
        // Intentar renovar token
        const refreshResponse = await api.post('/refresh')
        
        if (refreshResponse.data.access_token) {
          const userStore = useUserStore()
          userStore.updateToken(refreshResponse.data.access_token)
          
          // Reintentar request original con nuevo token
          originalRequest.headers.Authorization = `Bearer ${refreshResponse.data.access_token}`
          return api(originalRequest)
        }
        
      } catch (refreshError) {
        console.error('❌ Error renovando token:', refreshError)
        
        // Si falla el refresh, hacer logout
        const userStore = useUserStore()
        userStore.logout()
        
        // Redirigir a login
        if (window.location.pathname !== '/login') {
          window.location.href = '/login?message=Sesión expirada'
        }
      }
    }
    
    return Promise.reject(error)
  }
)

export default api