<template>
  <div class="clarity-debug" v-if="isEnabled && isDevelopment">
    <div class="debug-header" @click="toggleDebug">
      <span>🔍 Clarity Debug</span>
      <span class="toggle">{{ showDebug ? '▼' : '▶' }}</span>
    </div>
    
    <div v-if="showDebug" class="debug-content">
      <div class="debug-section">
        <h4>Estado</h4>
        <p>Inicializado: <span :class="isInitialized ? 'success' : 'error'">{{ isInitialized ? 'Sí' : 'No' }}</span></p>
        <p>Habilitado: <span :class="isEnabled ? 'success' : 'error'">{{ isEnabled ? 'Sí' : 'No' }}</span></p>
        <p>Project ID: {{ projectId || 'No configurado' }}</p>
      </div>
      
      <div class="debug-section">
        <h4>Eventos de Prueba</h4>
        <button @click="testEvent('test_button_click', { component: 'ClarityDebug' })" class="debug-btn">
          Probar Evento
        </button>
        <button @click="testPageView" class="debug-btn">
          Probar Page View
        </button>
        <button @click="testEcommerceEvent" class="debug-btn">
          Probar E-commerce
        </button>
      </div>
      
      <div class="debug-section">
        <h4>Últimos Eventos ({{ recentEvents.length }})</h4>
        <div class="events-list">
          <div v-for="event in recentEvents.slice(-5)" :key="event.timestamp" class="event-item">
            <span class="event-name">{{ event.name }}</span>
            <span class="event-time">{{ formatTime(event.timestamp) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useClarity } from '@/composables/useClarity'

const { isInitialized, isEnabled, trackEvent, trackEcommerce } = useClarity()

const showDebug = ref(false)
const recentEvents = ref([])
const isDevelopment = computed(() => import.meta.env.DEV)
const projectId = computed(() => import.meta.env.VITE_CLARITY_PROJECT_ID)

const toggleDebug = () => {
  showDebug.value = !showDebug.value
}

const testEvent = (eventName, eventData = {}) => {
  trackEvent(eventName, eventData)
  addToRecentEvents(eventName)
}

const testPageView = () => {
  trackEcommerce.pageView('debug_page_view', 'testing')
  addToRecentEvents('page_view')
}

const testEcommerceEvent = () => {
  trackEcommerce.addToCart(999, 'Producto de Prueba', 1, 100)
  addToRecentEvents('add_to_cart')
}

const addToRecentEvents = (eventName) => {
  recentEvents.value.push({
    name: eventName,
    timestamp: new Date()
  })
  
  // Mantener solo los últimos 10 eventos
  if (recentEvents.value.length > 10) {
    recentEvents.value.shift()
  }
}

const formatTime = (timestamp) => {
  return timestamp.toLocaleTimeString()
}

// Interceptar eventos de Clarity para mostrarlos en debug
onMounted(() => {
  if (isDevelopment.value) {
    const originalClarity = window.clarity
    window.clarity = (...args) => {
      if (args[0] === 'event' && args[1]) {
        addToRecentEvents(args[1])
      }
      return originalClarity?.(...args)
    }
  }
})
</script>

<style scoped>
.clarity-debug {
  position: fixed;
  top: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.9);
  color: white;
  border-radius: 8px;
  padding: 10px;
  font-family: monospace;
  font-size: 12px;
  z-index: 9999;
  max-width: 300px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.debug-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  padding: 5px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  margin-bottom: 10px;
}

.debug-header:hover {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  padding: 5px;
  margin: 0 -5px 10px -5px;
}

.debug-content {
  max-height: 400px;
  overflow-y: auto;
}

.debug-section {
  margin-bottom: 15px;
}

.debug-section h4 {
  margin: 0 0 8px 0;
  color: #60a5fa;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.debug-section p {
  margin: 4px 0;
}

.success {
  color: #10b981;
  font-weight: bold;
}

.error {
  color: #ef4444;
  font-weight: bold;
}

.debug-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 4px 8px;
  margin: 2px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 10px;
  transition: background 0.2s;
}

.debug-btn:hover {
  background: #2563eb;
}

.events-list {
  max-height: 120px;
  overflow-y: auto;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 4px;
  padding: 5px;
}

.event-item {
  display: flex;
  justify-content: space-between;
  padding: 2px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.event-item:last-child {
  border-bottom: none;
}

.event-name {
  color: #fbbf24;
  font-weight: bold;
}

.event-time {
  color: #9ca3af;
  font-size: 10px;
}

.toggle {
  font-size: 10px;
  color: #9ca3af;
}

/* Hacer responsive en móviles */
@media (max-width: 768px) {
  .clarity-debug {
    top: 10px;
    right: 10px;
    max-width: 250px;
    font-size: 11px;
  }
}
</style>
