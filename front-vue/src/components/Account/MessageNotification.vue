<template>
  <div :class="['form-message', messageType]" v-if="message">
    <i :class="messageIcon"></i>
    <span>{{ message }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  message: {
    type: String,
    default: ''
  },
  messageType: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  }
})

const messageIcon = computed(() => {
  const icons = {
    success: 'fa fa-check-circle',
    error: 'fa fa-exclamation-circle',
    warning: 'fa fa-exclamation-triangle',
    info: 'fa fa-info-circle'
  }
  return icons[messageType.value] || icons.info
})
</script>

<style scoped>
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
  background-color: #e8f5e8;
  color: #7d1c2b;
  border: 1px solid #a27345;
}

.form-message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.form-message.warning {
  background-color: #fef3c7;
  color: #a27345;
  border: 1px solid #e5bf60;
}

.form-message.info {
  background-color: #fdf8f0;
  color: #7d1c2b;
  border: 1px solid #e5bf60;
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
</style>
