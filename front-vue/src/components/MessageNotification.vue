<template>
  <Transition name="notification" appear>
    <div
      v-if="isVisible"
      class="notification"
      :class="notificationClasses"
      role="alert"
      :aria-live="type === 'error' ? 'assertive' : 'polite'"
    >
      <div class="notification-content">
        <div class="notification-icon">
          <i :class="iconClass"></i>
        </div>
        
        <div class="notification-message">
          <p class="notification-text">{{ message }}</p>
        </div>
        
        <button
          v-if="dismissible"
          @click="handleDismiss"
          class="notification-close"
          type="button"
          aria-label="Cerrar notificación"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <!-- Barra de progreso para auto-dismiss -->
      <div
        v-if="autoDismiss && duration > 0"
        class="notification-progress"
        :style="{ animationDuration: `${duration}ms` }"
      ></div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  isVisible: {
    type: Boolean,
    default: true
  },
  dismissible: {
    type: Boolean,
    default: true
  },
  autoDismiss: {
    type: Boolean,
    default: true
  },
  duration: {
    type: Number,
    default: 5000
  },
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => [
      'top-left', 'top-center', 'top-right',
      'bottom-left', 'bottom-center', 'bottom-right'
    ].includes(value)
  }
})

const emit = defineEmits(['hide', 'dismissed'])

let timeoutId = null

// Computed properties
const notificationClasses = computed(() => [
  `notification-${props.type}`,
  `notification-${props.position}`,
  {
    'notification-dismissible': props.dismissible,
    'notification-auto-dismiss': props.autoDismiss
  }
])

const iconClass = computed(() => {
  const iconMap = {
    success: 'fas fa-check-circle',
    error: 'fas fa-times-circle',
    warning: 'fas fa-exclamation-triangle',
    info: 'fas fa-info-circle'
  }
  return iconMap[props.type] || iconMap.info
})

// Methods
const handleDismiss = () => {
  emit('hide')
  emit('dismissed')
}

const setupAutoDismiss = () => {
  if (props.autoDismiss && props.duration > 0 && props.isVisible) {
    timeoutId = setTimeout(() => {
      handleDismiss()
    }, props.duration)
  }
}

const clearAutoDismiss = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = null
  }
}

// Lifecycle hooks
onMounted(() => {
  setupAutoDismiss()
})

onUnmounted(() => {
  clearAutoDismiss()
})

// Watch for visibility changes
const { isVisible } = props
if (isVisible) {
  setupAutoDismiss()
} else {
  clearAutoDismiss()
}
</script>

<style scoped>
.notification {
  position: fixed;
  z-index: 9999;
  max-width: 400px;
  min-width: 320px;
  margin: 1rem;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Posicionamiento */
.notification-top-left {
  top: 0;
  left: 0;
}

.notification-top-center {
  top: 0;
  left: 50%;
  transform: translateX(-50%);
}

.notification-top-right {
  top: 0;
  right: 0;
}

.notification-bottom-left {
  bottom: 0;
  left: 0;
}

.notification-bottom-center {
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
}

.notification-bottom-right {
  bottom: 0;
  right: 0;
}

/* Contenido */
.notification-content {
  display: flex;
  align-items: flex-start;
  padding: 1rem 1.25rem;
  gap: 0.75rem;
}

.notification-icon {
  flex-shrink: 0;
  font-size: 1.25rem;
  line-height: 1;
}

.notification-message {
  flex: 1;
  min-width: 0;
}

.notification-text {
  margin: 0;
  font-size: 0.875rem;
  font-weight: 500;
  line-height: 1.4;
  word-wrap: break-word;
}

.notification-close {
  flex-shrink: 0;
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  opacity: 0.7;
  transition: opacity 0.2s ease;
  padding: 0.25rem;
  border-radius: 4px;
}

.notification-close:hover {
  opacity: 1;
  background: rgba(0, 0, 0, 0.1);
}

/* Barra de progreso */
.notification-progress {
  height: 3px;
  background: rgba(255, 255, 255, 0.3);
  animation: notification-progress linear forwards;
}

/* Variantes de color */
.notification-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
}

.notification-error {
  background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
  color: white;
}

.notification-warning {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
  color: #212529;
}

.notification-info {
  background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
  color: white;
}

/* Transiciones */
.notification-enter-active,
.notification-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Transiciones por posición */
.notification-top-left.notification-enter-from,
.notification-top-center.notification-enter-from,
.notification-top-right.notification-enter-from {
  opacity: 0;
  transform: translateY(-100%) scale(0.9);
}

.notification-top-center.notification-enter-from {
  transform: translateX(-50%) translateY(-100%) scale(0.9);
}

.notification-bottom-left.notification-enter-from,
.notification-bottom-center.notification-enter-from,
.notification-bottom-right.notification-enter-from {
  opacity: 0;
  transform: translateY(100%) scale(0.9);
}

.notification-bottom-center.notification-enter-from {
  transform: translateX(-50%) translateY(100%) scale(0.9);
}

.notification-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

/* Animación de progreso */
@keyframes notification-progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

/* Responsive */
@media (max-width: 480px) {
  .notification {
    max-width: calc(100vw - 2rem);
    min-width: unset;
    margin: 0.5rem;
  }
  
  .notification-top-center,
  .notification-bottom-center {
    left: 0.5rem;
    right: 0.5rem;
    transform: none;
    margin: 0.5rem 0;
  }
  
  .notification-content {
    padding: 0.875rem 1rem;
  }
}

/* Hover para pausar auto-dismiss */
.notification-auto-dismiss:hover .notification-progress {
  animation-play-state: paused;
}
</style>
