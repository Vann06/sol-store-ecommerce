<template>
  <button 
    :type="type"
    :disabled="disabled"
    :class="buttonClasses"
    @click="$emit('click')"
  >
    <i :class="icon" v-if="icon"></i>
    <slot></slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'danger'].includes(value)
  },
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['sm', 'normal', 'lg'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    default: ''
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click'])

const buttonClasses = computed(() => [
  'btn',
  `btn-${props.variant}`,
  `btn-${props.size}`,
  {
    'btn-full-width': props.fullWidth,
    'btn-disabled': props.disabled
  }
])
</script>

<style scoped>
.btn {
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 140px;
}

.btn:disabled,
.btn-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Variants */
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

.btn-secondary:hover:not(:disabled) {
  background: #E5E7EB;
  border-color: #9CA3AF;
}

.btn-outline {
  background: transparent;
  color: #7d1c2b;
  border: 1px solid #7d1c2b;
}

.btn-outline:hover:not(:disabled) {
  background: #7d1c2b;
  color: white;
}

.btn-danger {
  background: #EF4444;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #DC2626;
}

/* Sizes */
.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
  min-width: auto;
}

.btn-lg {
  padding: 16px 32px;
  font-size: 16px;
  min-width: 160px;
}

.btn-full-width {
  width: 100%;
  min-width: auto;
}
</style>
