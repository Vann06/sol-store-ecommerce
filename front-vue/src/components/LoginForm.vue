<template>
    <section class="login-box">
      <img src="/img/logo_2.png" alt="Logo" class="logo" />
      <h2 class="admin-title">Login</h2>

      <!-- Mostrar errores -->
      <div v-if="userStore.error" class="error-message">
        <i class="fas fa-exclamation-circle"></i>
        {{ userStore.error }}
      </div>
  
      <form @submit.prevent="handleLogin">
        <label>Email</label>
        <input 
          type="email" 
          v-model="email" 
          required 
          :disabled="loading"
          placeholder="tu@email.com"
        />
  
        <label>Password</label>
        <input 
          type="password" 
          v-model="password" 
          required 
          :disabled="loading"
          placeholder="Tu contraseña"
        />
  
        <button 
          type="submit" 
          class="btn-primary"
          :disabled="loading"
          :class="{ 'loading': loading }"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          {{ loading ? 'Iniciando sesión...' : 'Login' }}
        </button>
      </form>
    </section>
  </template>
  
  <script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useCartStore } from '@/stores/cart'
import { useMessages } from '@/composables/useMessages'
import axios from 'axios'

const email = ref('')
const password = ref('')
const loading = ref(false)
const router = useRouter()
const userStore = useUserStore()
const cartStore = useCartStore()
const { showMessage } = useMessages()

const handleLogin = async () => {
  if (loading.value) return
  
  if (!email.value || !password.value) {
    showMessage('Por favor complete todos los campos', 'error')
    return
  }
  
  loading.value = true
  userStore.clearError()

  try {
    // Usar axios para mejor manejo de errores y timeouts
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    }, {
      timeout: 10000, // 10 segundos de timeout
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    console.log('Login response:', response.data) // Para debug

    // Verificar múltiples estructuras de respuesta posibles del backend Laravel
    let token = null
    let userData = null

    if (response.data) {
      // Posibles estructuras:
      // { token: "...", user: {...} }
      // { access_token: "...", user: {...} }
      // { data: { token: "...", user: {...} } }
      // { success: true, data: { token: "...", user: {...} } }

      const data = response.data.data || response.data

      token = data.token || data.access_token || data.auth_token
      userData = data.user

      // Si no encontramos token, verificar en response directo
      if (!token) {
        token = response.data.token || response.data.access_token
      }

      // Si no encontramos userData, verificar en response directo  
      if (!userData) {
        userData = response.data.user
      }
    }

    // Validar que tenemos lo necesario
    if (!token) {
      console.error('No token in response:', response.data)
      throw new Error('No se recibió token de autenticación del servidor')
    }

    if (!userData || !userData.id) {
      console.error('No user data in response:', response.data)  
      throw new Error('No se recibieron datos del usuario del servidor')
    }

    // Login exitoso - usar el nuevo método del store
    userStore.setUserAndToken(userData, token)

    // Configurar axios para futuras peticiones
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    // Cargar carrito si está disponible
    try {
      await cartStore.fetchCart()
    } catch (cartError) {
      console.warn('Could not load cart after login:', cartError.message)
      // No fallar el login si no se puede cargar el carrito
    }

    // Redirigir según el rol del usuario
    if (userData.role === 'admin') {
      window.location.href = 'http://localhost:8000/admin/products'
    } else {
      // Redirigir al home después del login exitoso
      router.push({ 
        path: '/', 
        query: { 
          message: `¡Bienvenido ${userData.first_name || userData.name || 'Usuario'}!`,
          messageType: 'success'
        } 
      })
    }

  } catch (error) {
    console.error('Login error:', error)
    
    let errorMessage = 'Error al iniciar sesión'
    
    if (error.code === 'ECONNABORTED') {
      errorMessage = 'Tiempo de espera agotado. Verifica tu conexión.'
    } else if (error.response) {
      // Error del servidor
      const status = error.response.status
      const data = error.response.data
      
      if (status === 401) {
        errorMessage = 'Email o contraseña incorrectos'
      } else if (status === 422) {
        // Errores de validación Laravel
        if (data.errors) {
          errorMessage = Object.values(data.errors).flat().join(', ')
        } else if (data.message) {
          errorMessage = data.message
        }
      } else if (status >= 500) {
        errorMessage = 'Error en el servidor. Intenta más tarde.'
      } else if (data && data.message) {
        errorMessage = data.message
      }
    } else if (error.request) {
      errorMessage = 'No se pudo conectar con el servidor. Verifica tu conexión.'
    } else if (error.message) {
      errorMessage = error.message
    }
    
    userStore.setError(errorMessage)
    showMessage(errorMessage, 'error')
  } finally {
    loading.value = false
  }
}

</script>
  
  <style scoped>
  .login-box {
    flex: 1;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  
  .logo {
    height: 60px;
    margin-bottom: 10px;
  }
  
  .admin-title {
    color: #7d1c2b;
    margin-bottom: 20px;
    font-size: 2rem;
    font-weight: 700;
  }

  .error-message {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    border-left: 4px solid #dc2626;
    width: 100%;
    max-width: 300px;
    font-size: 14px;
  }
  
  form {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 300px;
  }
  
  label {
    margin-top: 10px;
    font-weight: 600;
    font-size: 14px;
    color: #7d1c2b;
    margin-bottom: 6px;
  }
  
  input {
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f9fafb;
  }

  input:focus {
    outline: none;
    border-color: #e5bf60;
    background: white;
    box-shadow: 0 0 0 3px rgba(229, 191, 96, 0.1);
  }

  input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #f3f4f6;
  }

  input::placeholder {
    color: #9ca3af;
  }
  
  .btn-primary {
    margin-top: 20px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    min-height: 48px;
  }
  
  .btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #a27345 0%, #7d1c2b 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(125, 28, 43, 0.3);
  }

  .btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .btn-primary.loading {
    background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .login-box {
      padding: 40px 20px;
    }
    
    .admin-title {
      font-size: 1.75rem;
    }
    
    form {
      max-width: 280px;
    }
  }
  </style>
  