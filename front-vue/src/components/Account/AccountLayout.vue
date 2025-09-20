<template>
  <div class="account-layout">
    <!-- Sidebar de navegación -->
    <Sidebar />
    
    <!-- Contenido principal -->
    <div class="main-content">
      <!-- Notificaciones -->
      <MessageNotification 
        :message="message" 
        :type="messageType"
        :isVisible="isVisible"
        @hide="hideMessage" 
      />
      
      <!-- Contenido de la vista -->
      <slot />
    </div>
  </div>
</template>

<script setup>
import Sidebar from '@/components/Account/Sidebar.vue'
import MessageNotification from '@/components/MessageNotification.vue'
import { useMessages } from '@/composables/useMessages'

// Props opcionales para personalizar el layout
const props = defineProps({
  showSidebar: {
    type: Boolean,
    default: true
  },
  contentClass: {
    type: String,
    default: ''
  }
})

// Composable para mensajes
const { message, messageType, isVisible, hideMessage } = useMessages()
</script>

<style scoped>
.account-layout {
  display: flex;
  min-height: calc(100vh - 120px);
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.main-content {
  flex: 1;
  padding: 2rem;
  position: relative;
}

/* Responsive design */
@media (max-width: 768px) {
  .account-layout {
    flex-direction: column;
  }
  
  .main-content {
    padding: 1rem;
  }
}

/* Variaciones de layout */
.account-layout.no-sidebar .main-content {
  margin-left: 0;
}
</style>
