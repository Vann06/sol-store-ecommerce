<template>
  <button 
    @click="handleGoogleLogin" 
    class="btn-google"
    :disabled="loading"
    :class="{ 'loading': loading }"
    type="button"
  >
    <svg v-if="!loading" class="google-icon" viewBox="0 0 24 24">
      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
    
    <i v-if="loading" class="fas fa-spinner fa-spin"></i>
    
    <span>{{ loading ? 'Conectando...' : buttonText }}</span>
  </button>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import api from '@/utils/http'

const props = defineProps({
  buttonText: {
    type: String,
    default: 'Continuar con Google'
  }
})

const emit = defineEmits(['success', 'error'])

const router = useRouter()
const userStore = useUserStore()
const loading = ref(false)

const handleGoogleLogin = async () => {
  if (loading.value) return
  
  loading.value = true
  
  try {
    console.log('🔐 Iniciando autenticación con Google...')
    
    // Obtener la URL de autorización de Google
    const response = await api.get('/auth/google')
    
    if (response.data.url) {
      // Guardar la URL de retorno en sessionStorage
      sessionStorage.setItem('google_auth_return', window.location.pathname)
      
      // Redirigir a Google para autenticación
      window.location.href = response.data.url
    } else {
      throw new Error('No se recibió URL de Google')
    }
    
  } catch (error) {
    console.error('❌ Error al iniciar Google OAuth:', error)
    loading.value = false
    
    const errorMessage = error.response?.data?.message || 
                        'Error al conectar con Google. Intenta nuevamente.'
    
    emit('error', errorMessage)
  }
}
</script>

<style scoped>
.btn-google {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 12px 24px;
  background: white;
  border: 1px solid #dadce0;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #3c4043;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Roboto', sans-serif;
}

.btn-google:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #d2d3d4;
  box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3),
              0 1px 3px 1px rgba(60, 64, 67, 0.15);
}

.btn-google:active:not(:disabled) {
  background: #f1f3f4;
  box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3);
}

.btn-google:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-google.loading {
  pointer-events: none;
}

.google-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.fa-spinner {
  font-size: 16px;
  color: #5f6368;
}

span {
  flex: 1;
  text-align: center;
}
</style>