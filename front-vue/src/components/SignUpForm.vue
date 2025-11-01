<template>
    <div class="form-wrapper">
      <img src="/img/logo_2.png" alt="Logo" class="logo" />
      <h2 class="title">Crea tu Cuenta</h2>

      <!-- Mensaje de éxito -->
      <div v-if="successMessage" class="success-message">
        <i class="fas fa-check-circle"></i>
        {{ successMessage }}
      </div>

      <!-- Botón de Google OAuth -->
      <GoogleLoginButton 
        button-text="Registrarse con Google"
        @error="handleGoogleError"
        class="google-btn-margin"
      />

      <!-- Separador -->
      <div class="divider">
        <span>o regístrate con email</span>
      </div>
  
      <form @submit.prevent="handleSubmit">
        <div class="form-grid">
          <div class="form-group">
            <label>Primer Nombre *</label>
            <input 
              type="text" 
              v-model="firstName" 
              required 
              :disabled="loading"
              placeholder="Juan"
            />
            <p v-if="errors.first_name" class="error-text">
              {{ errors.first_name[0] }}
            </p>
          </div>
  
          <div class="form-group">
            <label>Apellido *</label>
            <input 
              type="text" 
              v-model="lastName" 
              required 
              :disabled="loading"
              placeholder="Pérez"
            />
            <p v-if="errors.last_name" class="error-text">
              {{ errors.last_name[0] }}
            </p>
          </div>
  
          <div class="form-group full-width">
            <label>Email *</label>
            <input 
              type="email" 
              v-model="email" 
              required 
              :disabled="loading"
              placeholder="tu@email.com"
            />
            <p v-if="errors.email" class="error-text">
              {{ errors.email[0] }}
            </p>
          </div>
  
          <div class="form-group">
            <label>Contraseña *</label>
            <input 
              type="password" 
              v-model="password" 
              required 
              :disabled="loading"
              placeholder="Mínimo 8 caracteres"
            />
            <p v-if="errors.password" class="error-text">
              {{ errors.password[0] }}
            </p>
          </div>
  
          <div class="form-group">
            <label>Confirmar Contraseña *</label>
            <input 
              type="password" 
              v-model="confirmPassword" 
              required 
              :disabled="loading"
              placeholder="Repite tu contraseña"
            />
            <p v-if="errors.password_confirmation" class="error-text">
              {{ errors.password_confirmation[0] }}
            </p>
          </div>
        </div>
  
        <button 
          class="submit-button" 
          type="submit"
          :disabled="loading"
          :class="{ 'loading': loading }"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          {{ loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
        </button>
      </form>

      <div class="login-link">
        ¿Ya tienes cuenta? 
        <router-link to="/account/login">Inicia sesión aquí</router-link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import http from '@/http'
  import GoogleLoginButton from '@/components/GoogleLoginButton.vue'
  
  const router = useRouter()
  
  const firstName = ref('')
  const lastName = ref('')
  const email = ref('')
  const password = ref('')
  const confirmPassword = ref('')
  const errors = ref({})
  const loading = ref(false)
  const successMessage = ref('')

const handleGoogleError = (errorMessage) => {
  errors.value = { general: [errorMessage] }
}
 
const handleSubmit = async () => {
  if (loading.value) return
  
  errors.value = {}
  successMessage.value = ''
  loading.value = true

  try {
    const response = await http.post('/register', {
      first_name: firstName.value,
      last_name: lastName.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    })

    console.log('✅ Registro exitoso:', response.data)
    
    successMessage.value = '¡Cuenta creada exitosamente! Redirigiendo...'
    
    // Redirigir al login después de 2 segundos
    setTimeout(() => {
      router.push({
        path: '/account/login',
        query: { 
          message: 'Cuenta creada exitosamente. Por favor inicia sesión.',
          email: email.value
        }
      })
    }, 2000)

  } catch (error) {
    console.error('❌ Error en registro:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else if (error.response?.data?.message) {
      errors.value = { general: [error.response.data.message] }
    } else {
      errors.value = { general: ['Ocurrió un error. Intenta de nuevo'] }
    }
  } finally {
    loading.value = false
  }
}
  </script>
  
  <style scoped>
  .form-wrapper {
  width: 100%;
  max-width: 700px;
  padding: 50px 40px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
}

  .logo {
    height: 60px;
    margin-bottom: 10px;
  }
  
  .title {
    font-size: 28px;
    color: #7d1c2b;
    margin-bottom: 30px;
    font-weight: 700;
  }

  .success-message {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    font-size: 14px;
    border: 1px solid #6ee7b7;
  }

  .google-btn-margin {
    width: 100%;
    margin-bottom: 20px;
  }

  .divider {
    width: 100%;
    text-align: center;
    margin: 20px 0;
    position: relative;
  }

  .divider::before,
  .divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 42%;
    height: 1px;
    background: #e2e8f0;
  }

  .divider::before {
    left: 0;
  }

  .divider::after {
    right: 0;
  }

  .divider span {
    background: white;
    padding: 0 10px;
    color: #718096;
    font-size: 14px;
    position: relative;
    z-index: 1;
  }
  
  form {
    width: 100%;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }
  
  label {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
    color: #2d3748;
  }
  
  input[type="text"],
  input[type="email"],
  input[type="password"] {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
  }

  input:focus {
    outline: none;
    border-color: #7d1c2b;
    box-shadow: 0 0 0 3px rgba(125, 28, 43, 0.1);
  }

  input:disabled {
    background-color: #f7fafc;
    cursor: not-allowed;
    opacity: 0.6;
  }

  .error-text {
    color: #dc2626;
    font-size: 13px;
    margin-top: 6px;
  }
  
  .submit-button {
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
    padding: 14px 24px;
    background: linear-gradient(135deg, #7d1c2b 0%, #a72341 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  
  .submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(125, 28, 43, 0.3);
  }

  .submit-button:active:not(:disabled) {
    transform: translateY(0);
  }

  .submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .submit-button.loading {
    pointer-events: none;
  }

  .login-link {
    margin-top: 20px;
    text-align: center;
    color: #4a5568;
    font-size: 14px;
  }

  .login-link a {
    color: #7d1c2b;
    text-decoration: none;
    font-weight: 600;
    margin-left: 5px;
  }

  .login-link a:hover {
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .form-wrapper {
      padding: 40px 20px;
      max-width: 100%;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-group.full-width {
      grid-column: 1;
    }

    .title {
      font-size: 24px;
    }
  }

  </style>
  