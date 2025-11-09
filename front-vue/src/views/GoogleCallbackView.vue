<template>
  <div class="callback-container">
    <div class="callback-content">
      <div class="spinner-wrapper">
        <i class="fas fa-spinner fa-spin"></i>
      </div>
      <h2>{{ message }}</h2>
      <p v-if="!error" class="subtitle">Por favor espera un momento...</p>
      <div v-if="error" class="error-box">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ error }}</p>
        <router-link to="/login" class="btn-back">
          Volver al inicio de sesión
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useCartStore } from '@/stores/cart'
import api from '@/utils/http'

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const cartStore = useCartStore()

const message = ref('Verificando tu cuenta de Google...')
const error = ref('')

onMounted(async () => {
  // Obtener el token y datos del usuario de la URL
  const token = route.query.token
  const userEncoded = route.query.user
  const errorParam = route.query.error
  
  // Si el usuario canceló o hubo error
  if (errorParam) {
    error.value = 'Autenticación cancelada o denegada'
    message.value = 'No se pudo completar el inicio de sesión'
    setTimeout(() => router.push('/account/login'), 3000)
    return
  }
  
  if (!token || !userEncoded) {
    error.value = 'No se recibieron datos de autenticación'
    message.value = 'Error en la autenticación'
    setTimeout(() => router.push('/account/login'), 3000)
    return
  }
  
  try {
    console.log('🔐 Procesando callback de Google...')
    
    // Decodificar datos del usuario
    const userData = JSON.parse(atob(userEncoded))
    
    message.value = '¡Bienvenido! Iniciando sesión...'
    
    // Guardar token y usuario en el store
    userStore.token = token
    userStore.user = userData
    userStore.isAuthenticated = true
    
    // Compatibilidad con localStorage
    localStorage.setItem('auth_token', token)
    
    console.log('✅ Login con Google exitoso:', userData.email)
    
    // Sincronizar carrito si hay productos
    if (cartStore.items.length > 0) {
      await cartStore.syncWithBackend()
    }
    
    // Redirigir al home después de un breve delay
    setTimeout(() => {
      router.push('/')
    }, 1500)
    
  } catch (err) {
    console.error('❌ Error en callback de Google:', err)
    error.value = err.message || 'Error al procesar la autenticación. Intenta nuevamente.'
    message.value = 'No se pudo completar el inicio de sesión'
    
    setTimeout(() => router.push('/account/login'), 4000)
  }
})
</script>

<style scoped>
.callback-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.callback-content {
  background: white;
  border-radius: 16px;
  padding: 48px;
  max-width: 500px;
  width: 100%;
  text-align: center;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.spinner-wrapper {
  margin-bottom: 24px;
}

.fa-spinner {
  font-size: 48px;
  color: #667eea;
}

h2 {
  font-size: 24px;
  color: #1a202c;
  margin-bottom: 12px;
  font-weight: 600;
}

.subtitle {
  color: #718096;
  font-size: 16px;
}

.error-box {
  margin-top: 24px;
  padding: 20px;
  background: #fff5f5;
  border: 1px solid #fc8181;
  border-radius: 8px;
}

.error-box .fa-exclamation-circle {
  font-size: 32px;
  color: #e53e3e;
  margin-bottom: 12px;
}

.error-box p {
  color: #c53030;
  margin-bottom: 16px;
}

.btn-back {
  display: inline-block;
  padding: 10px 24px;
  background: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 500;
  transition: background 0.3s ease;
}

.btn-back:hover {
  background: #5568d3;
}
</style>