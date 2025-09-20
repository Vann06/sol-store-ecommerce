<template>
  <section class="login-box">
    <img src="/img/logo_2.png" alt="Logo" class="logo" />
    <h2 class="admin-title">Login</h2>

    <!-- Mensaje de la query string (sesión expirada, etc.) -->
    <div v-if="routeMessage" class="info-message">
      <i class="fas fa-info-circle"></i>
      {{ routeMessage }}
    </div>

    <!-- Mostrar errores -->
    <div v-if="error" class="error-message">
      <i class="fas fa-exclamation-circle"></i>
      {{ error }}
    </div>

    <form @submit.prevent="handleLogin">
      <label>Email</label>
      <input 
        type="email" 
        v-model="email" 
        required 
        :disabled="loading"
        placeholder="tu@email.com"
        autocomplete="email"
      />

      <label>Password</label>
      <input 
        type="password" 
        v-model="password" 
        required 
        :disabled="loading"
        placeholder="Tu contraseña"
        autocomplete="current-password"
      />

      <button 
        type="submit" 
        class="btn-primary"
        :disabled="loading"
        :class="{ 'loading': loading }"
      >
        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
        {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
      </button>
    </form>

    <div class="auth-links">
      <router-link to="/account/create" class="signup-link">
        ¿No tienes cuenta? Regístrate aquí
      </router-link>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useCartStore } from '@/stores/cart'

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const routeMessage = ref('')

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const cartStore = useCartStore()

// Verificar si hay mensaje en la query string
onMounted(() => {
  if (route.query.message) {
    routeMessage.value = route.query.message
  }
  
  // Limpiar errores previos del store
  userStore.clearError()
})

const handleLogin = async () => {
  if (loading.value) return
  
  // Validación básica
  if (!email.value || !password.value) {
    error.value = 'Por favor complete todos los campos'
    return
  }
  
  loading.value = true
  error.value = ''
  userStore.clearError()

  try {
    console.log('🔐 Enviando credenciales con JWT...')

    // Usar el método login del userStore que ahora tiene JWT
    const result = await userStore.login({
      email: email.value,
      password: password.value
    })

    if (result.success) {
      console.log('✅ Login exitoso con JWT:', result.data)

      // Cargar carrito después del login exitoso
      try {
        await cartStore.fetchCart()
        console.log('🛒 Carrito cargado exitosamente')
      } catch (cartError) {
        console.warn('⚠️ No se pudo cargar el carrito:', cartError)
        // No fallar el login si no se puede cargar el carrito
      }

      // Redirigir según el rol del usuario
      const user = result.data.user
      const redirectTo = route.query.redirect

      if (user.role === 'admin') {
        // Admin va al panel administrativo
        console.log('👑 Redirigiendo admin al panel...')
        window.location.href = 'http://localhost:8000/admin/products'
      } else {
        // Usuario normal
        console.log('👤 Redirigiendo usuario a:', redirectTo || '/')
        
        const destination = redirectTo || '/'
        await router.push({
          path: destination,
          query: { 
            message: `¡Bienvenido ${user.first_name || user.name || 'Usuario'}!`,
            messageType: 'success'
          }
        })
      }

    } else {
      // Error en el login
      error.value = result.error || 'Error al iniciar sesión'
      console.error('❌ Login fallido:', error.value)
    }

  } catch (err) {
    console.error('❌ Error inesperado en login:', err)
    
    // Manejo de errores específicos
    if (err.code === 'ECONNABORTED') {
      error.value = 'Tiempo de espera agotado. Verifica tu conexión.'
    } else if (err.response) {
      const status = err.response.status
      const data = err.response.data
      
      if (status === 401) {
        error.value = 'Email o contraseña incorrectos'
      } else if (status === 422) {
        error.value = data.message || 'Datos de login inválidos'
      } else if (status >= 500) {
        error.value = 'Error en el servidor. Intenta más tarde.'
      } else {
        error.value = data.message || 'Error al conectar con el servidor'
      }
    } else {
      error.value = err.message || 'Error inesperado'
    }

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

.info-message {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  color: #1d4ed8;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  border-left: 4px solid #3b82f6;
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

.auth-links {
  margin-top: 20px;
  text-align: center;
}

.signup-link {
  color: #7d1c2b;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.2s ease;
}

.signup-link:hover {
  color: #a27345;
  text-decoration: underline;
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