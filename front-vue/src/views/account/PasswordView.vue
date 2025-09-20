<template>
  <div class="password-view">
    <div class="page-header">
      <h2><i class="fa fa-lock"></i> Cambiar Contraseña</h2>
      <p class="page-subtitle">Mantén tu cuenta segura con una contraseña fuerte</p>
    </div>

    <div class="form-container">
      <form @submit.prevent="updatePassword" class="password-form">
        <div class="security-tips">
          <h3><i class="fa fa-shield-alt"></i> Consejos de Seguridad</h3>
          <ul>
            <li>Usa al menos 8 caracteres</li>
            <li>Incluye mayúsculas y minúsculas</li>
            <li>Añade números y símbolos</li>
            <li>No uses información personal</li>
          </ul>
        </div>

        <div class="form-fields">
          <div class="form-group">
            <label for="currentPassword">Contraseña Actual *</label>
            <div class="input-group">
              <i class="fa fa-key input-icon"></i>
              <input 
                type="password" 
                id="currentPassword"
                v-model="formData.currentPassword" 
                placeholder="Ingresa tu contraseña actual"
                class="form-input"
                :class="{ 'error': errors.currentPassword }"
                required
              />
              <button 
                type="button" 
                class="toggle-password"
                @click="togglePasswordVisibility('current')"
              >
                <i :class="showPasswords.current ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
            <span v-if="errors.currentPassword" class="error-message">
              {{ errors.currentPassword }}
            </span>
          </div>

          <div class="form-group">
            <label for="newPassword">Nueva Contraseña *</label>
            <div class="input-group">
              <i class="fa fa-lock input-icon"></i>
              <input 
                :type="showPasswords.new ? 'text' : 'password'"
                id="newPassword"
                v-model="formData.newPassword" 
                placeholder="Ingresa tu nueva contraseña"
                class="form-input"
                :class="{ 'error': errors.newPassword }"
                @input="validatePassword"
                required
              />
              <button 
                type="button" 
                class="toggle-password"
                @click="togglePasswordVisibility('new')"
              >
                <i :class="showPasswords.new ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
            <div class="password-strength">
              <div class="strength-bar">
                <div 
                  class="strength-fill" 
                  :class="passwordStrength.level"
                  :style="{ width: passwordStrength.percentage + '%' }"
                ></div>
              </div>
              <span class="strength-text" :class="passwordStrength.level">
                {{ passwordStrength.text }}
              </span>
            </div>
            <span v-if="errors.newPassword" class="error-message">
              {{ errors.newPassword }}
            </span>
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirmar Nueva Contraseña *</label>
            <div class="input-group">
              <i class="fa fa-check-circle input-icon"></i>
              <input 
                :type="showPasswords.confirm ? 'text' : 'password'"
                id="confirmPassword"
                v-model="formData.confirmPassword" 
                placeholder="Confirma tu nueva contraseña"
                class="form-input"
                :class="{ 'error': errors.confirmPassword }"
                @input="validateConfirmPassword"
                required
              />
              <button 
                type="button" 
                class="toggle-password"
                @click="togglePasswordVisibility('confirm')"
              >
                <i :class="showPasswords.confirm ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
            <span v-if="errors.confirmPassword" class="error-message">
              {{ errors.confirmPassword }}
            </span>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" @click="resetForm" class="btn btn-secondary">
            <i class="fa fa-undo"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
            <i class="fa fa-save"></i>
            {{ loading ? 'Actualizando...' : 'Actualizar Contraseña' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Mensaje de éxito/error -->
    <div v-if="message" :class="['message', messageType]" class="form-message">
      <i :class="messageIcon"></i>
      <span>{{ message }}</span>
    </div>
  </div>
</template><script setup>
import { ref, reactive, computed } from 'vue'

const loading = ref(false)
const message = ref('')
const messageType = ref('')

const formData = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const errors = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const showPasswords = reactive({
  current: false,
  new: false,
  confirm: false
})

const passwordStrength = computed(() => {
  const password = formData.newPassword
  if (!password) return { level: '', text: '', percentage: 0 }

  let score = 0
  const checks = [
    { regex: /.{8,}/, text: 'Al menos 8 caracteres' },
    { regex: /[a-z]/, text: 'Letra minúscula' },
    { regex: /[A-Z]/, text: 'Letra mayúscula' },
    { regex: /\d/, text: 'Número' },
    { regex: /[^A-Za-z0-9]/, text: 'Símbolo especial' }
  ]

  checks.forEach(check => {
    if (check.regex.test(password)) score++
  })

  const levels = [
    { min: 0, level: 'weak', text: 'Muy débil', percentage: 20 },
    { min: 1, level: 'weak', text: 'Débil', percentage: 40 },
    { min: 2, level: 'fair', text: 'Regular', percentage: 60 },
    { min: 3, level: 'good', text: 'Buena', percentage: 80 },
    { min: 4, level: 'strong', text: 'Fuerte', percentage: 100 }
  ]

  for (let i = levels.length - 1; i >= 0; i--) {
    if (score >= levels[i].min) {
      return levels[i]
    }
  }

  return levels[0]
})

const isFormValid = computed(() => {
  return formData.currentPassword &&
         formData.newPassword &&
         formData.confirmPassword &&
         formData.newPassword === formData.confirmPassword &&
         passwordStrength.value.level !== 'weak' &&
         !Object.values(errors).some(error => error)
})

const messageIcon = computed(() => {
  switch (messageType.value) {
    case 'success': return 'fa fa-check-circle'
    case 'error': return 'fa fa-exclamation-circle'
    case 'warning': return 'fa fa-exclamation-triangle'
    default: return 'fa fa-info-circle'
  }
})

const togglePasswordVisibility = (field) => {
  showPasswords[field] = !showPasswords[field]
}

const validatePassword = () => {
  errors.newPassword = ''
  
  if (formData.newPassword.length < 8) {
    errors.newPassword = 'La contraseña debe tener al menos 8 caracteres'
    return
  }
  
  if (formData.newPassword === formData.currentPassword) {
    errors.newPassword = 'La nueva contraseña debe ser diferente a la actual'
    return
  }
  
  validateConfirmPassword()
}

const validateConfirmPassword = () => {
  errors.confirmPassword = ''
  
  if (formData.confirmPassword && formData.newPassword !== formData.confirmPassword) {
    errors.confirmPassword = 'Las contraseñas no coinciden'
  }
}

const updatePassword = async () => {
  loading.value = true
  clearErrors()
  
  try {
    // Validaciones finales
    if (!validateForm()) {
      loading.value = false
      return
    }
    
    // Aquí puedes agregar la lógica para actualizar en el backend
    // await axios.put('/api/user/password', {
    //   current_password: formData.currentPassword,
    //   new_password: formData.newPassword
    // })
    
    // Simular llamada al backend
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Simular posible error de contraseña actual incorrecta
    if (formData.currentPassword === 'wrong') {
      throw new Error('La contraseña actual es incorrecta')
    }
    
    showMessage('Contraseña actualizada correctamente', 'success')
    resetForm()
  } catch (error) {
    if (error.message.includes('contraseña actual')) {
      errors.currentPassword = error.message
    } else {
      showMessage('Error al actualizar la contraseña', 'error')
    }
  } finally {
    loading.value = false
  }
}

const validateForm = () => {
  let isValid = true
  
  if (!formData.currentPassword) {
    errors.currentPassword = 'La contraseña actual es requerida'
    isValid = false
  }
  
  if (!formData.newPassword) {
    errors.newPassword = 'La nueva contraseña es requerida'
    isValid = false
  } else if (formData.newPassword.length < 8) {
    errors.newPassword = 'La contraseña debe tener al menos 8 caracteres'
    isValid = false
  }
  
  if (!formData.confirmPassword) {
    errors.confirmPassword = 'Debes confirmar la nueva contraseña'
    isValid = false
  } else if (formData.newPassword !== formData.confirmPassword) {
    errors.confirmPassword = 'Las contraseñas no coinciden'
    isValid = false
  }
  
  return isValid
}

const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
}

const resetForm = () => {
  formData.currentPassword = ''
  formData.newPassword = ''
  formData.confirmPassword = ''
  clearErrors()
  showMessage('Formulario restablecido', 'warning')
}

const showMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
    messageType.value = ''
  }, 5000)
}
</script>

<style scoped>
.password-view {
  max-width: 600px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.page-header h2 {
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.page-header h2 i {
  color: #7d1c2b;
  margin-right: 12px;
}

.page-subtitle {
  color: #666;
  font-size: 16px;
  margin: 0;
}

.form-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.password-form {
  padding: 30px;
}

.security-tips {
  background: #fdf8f0;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 30px;
  border-left: 4px solid #e5bf60;
}

.security-tips h3 {
  color: #a27345;
  margin: 0 0 15px 0;
  font-size: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.security-tips ul {
  margin: 0;
  padding-left: 20px;
  color: #a27345;
}

.security-tips li {
  margin-bottom: 4px;
  font-size: 14px;
}

.form-fields {
  display: flex;
  flex-direction: column;
  gap: 24px;
  margin-bottom: 30px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: #374151;
  font-weight: 600;
  margin-bottom: 8px;
  font-size: 14px;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #9CA3AF;
  font-size: 16px;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 12px 45px 12px 40px;
  border: 2px solid #E5E7EB;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
  background-color: #FAFAFA;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #7d1c2b;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(125, 28, 43, 0.1);
}

.form-input.error {
  border-color: #EF4444;
  background-color: #FEF2F2;
}

.toggle-password {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: #9CA3AF;
  cursor: pointer;
  padding: 8px;
  font-size: 14px;
  transition: color 0.3s ease;
}

.toggle-password:hover {
  color: #7d1c2b;
}

.password-strength {
  margin-top: 8px;
}

.strength-bar {
  height: 4px;
  background: #E5E7EB;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 4px;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.strength-fill.weak {
  background: #EF4444;
}

.strength-fill.fair {
  background: #e5bf60;
}

.strength-fill.good {
  background: #a27345;
}

.strength-fill.strong {
  background: #7d1c2b;
}

.strength-text {
  font-size: 12px;
  font-weight: 600;
}

.strength-text.weak {
  color: #EF4444;
}

.strength-text.fair {
  color: #e5bf60;
}

.strength-text.good {
  color: #a27345;
}

.strength-text.strong {
  color: #7d1c2b;
}

.error-message {
  color: #EF4444;
  font-size: 12px;
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid #E5E7EB;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  min-width: 140px;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(125, 28, 43, 0.4);
}

.btn-secondary {
  background: #F3F4F6;
  color: #374151;
  border: 1px solid #D1D5DB;
}

.btn-secondary:hover {
  background: #E5E7EB;
  border-color: #9CA3AF;
}

.form-message {
  position: fixed;
  top: 100px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  z-index: 1000;
  animation: slideIn 0.3s ease-out;
}

.form-message.success {
  background-color: #D1FAE5;
  color: #065F46;
  border: 1px solid #A7F3D0;
}

.form-message.error {
  background-color: #FEE2E2;
  color: #991B1B;
  border: 1px solid #FECACA;
}

.form-message.warning {
  background-color: #FEF3C7;
  color: #92400E;
  border: 1px solid #FDE68A;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    min-width: auto;
  }
  
  .security-tips {
    margin-bottom: 20px;
    padding: 15px;
  }
}
</style>
  