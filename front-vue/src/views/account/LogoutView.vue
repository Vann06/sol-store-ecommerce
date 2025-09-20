<template>
  <div class="logout-container">
    <div class="logout-card">
      <div class="logout-icon">
        <i class="fa fa-sign-out-alt"></i>
      </div>
      <h2>Cerrando Sesión</h2>
      <p>Por favor espera mientras procesamos tu solicitud...</p>
      
      <div class="loading-spinner">
        <div class="spinner">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
        </div>
      </div>
      
      <div class="logout-steps">
        <div class="step" :class="{ active: currentStep >= 1, completed: currentStep > 1 }">
          <i class="fa fa-user-times"></i>
          <span>Cerrando sesión</span>
        </div>
        <div class="step" :class="{ active: currentStep >= 2, completed: currentStep > 2 }">
          <i class="fa fa-trash"></i>
          <span>Limpiando datos</span>
        </div>
        <div class="step" :class="{ active: currentStep >= 3, completed: currentStep > 3 }">
          <i class="fa fa-home"></i>
          <span>Redirigiendo al inicio</span>
        </div>
      </div>
      
      <p class="logout-message">{{ logoutMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useLogout } from '@/composables/useLogout'

const { logout } = useLogout()
const currentStep = ref(0)
const logoutMessage = ref('Iniciando proceso de logout...')

const messages = [
  'Iniciando proceso de logout...',
  'Cerrando tu sesión de forma segura...',
  'Limpiando datos temporales...',
  'Redirigiendo al inicio...',
  '¡Logout completado exitosamente!'
]

onMounted(async () => {
  // Simular pasos del logout con animación
  for (let i = 1; i <= 3; i++) {
    currentStep.value = i
    logoutMessage.value = messages[i]
    await new Promise(resolve => setTimeout(resolve, 800))
  }
  
  // Ejecutar el logout
  logoutMessage.value = messages[4]
  await logout()
})
</script>

<style scoped>
.logout-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  padding: 20px;
}

.logout-card {
  background: white;
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}

.logout-icon {
  font-size: 48px;
  color: #7d1c2b;
  margin-bottom: 20px;
  animation: pulse 2s infinite;
}

.logout-card h2 {
  color: #333;
  margin-bottom: 10px;
  font-size: 24px;
  font-weight: 600;
}

.logout-card > p {
  color: #666;
  margin-bottom: 30px;
  font-size: 16px;
}

.loading-spinner {
  margin: 30px 0;
}

.spinner {
  width: 40px;
  height: 40px;
  position: relative;
  margin: 0 auto;
}

.double-bounce1, .double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #7d1c2b;
  opacity: 0.6;
  position: absolute;
  top: 0;
  left: 0;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.double-bounce2 {
  animation-delay: -1.0s;
}

.logout-steps {
  display: flex;
  justify-content: space-between;
  margin: 30px 0;
  gap: 10px;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  flex: 1;
  padding: 15px 10px;
  border-radius: 8px;
  background: #F8F9FA;
  color: #9CA3AF;
  transition: all 0.3s ease;
  position: relative;
}

.step.active {
  background: #fdf8f0;
  color: #7d1c2b;
  transform: scale(1.05);
}

.step.completed {
  background: #e8f5e8;
  color: #a27345;
}

.step i {
  font-size: 20px;
  margin-bottom: 4px;
}

.step span {
  font-size: 12px;
  font-weight: 600;
  text-align: center;
}

.logout-message {
  font-size: 14px;
  color: #7d1c2b;
  font-weight: 500;
  margin-top: 20px;
  font-style: italic;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes sk-bounce {
  0%, 100% {
    transform: scale(0.0);
  } 50% {
    transform: scale(1.0);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .logout-card {
    padding: 30px 20px;
  }
  
  .logout-steps {
    flex-direction: column;
    gap: 8px;
  }
  
  .step {
    flex-direction: row;
    justify-content: flex-start;
    padding: 12px 15px;
  }
  
  .step span {
    text-align: left;
  }
}
</style>
