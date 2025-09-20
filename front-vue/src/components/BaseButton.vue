<template>
  <component
    :is="tag"
    :type="buttonType"
    :disabled="disabled || loading"
    :to="to"
    :href="href"
    :target="target"
    :rel="rel"
    class="base-button"
    :class="buttonClasses"
    @click="handleClick"
    v-bind="$attrs"
  >
    <!-- Loading spinner -->
    <div v-if="loading" class="button-spinner">
      <div class="spinner"></div>
    </div>
    
    <!-- Contenido del botón -->
    <span class="button-content" :class="{ 'button-content-hidden': loading }">
      <slot></slot>
    </span>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => [
      'primary', 
      'secondary', 
      'success', 
      'danger', 
      'warning', 
      'info', 
      'light', 
      'dark',
      'outline-primary',
      'outline-secondary',
      'ghost'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large', 'extra-large'].includes(value)
  },
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  block: {
    type: Boolean,
    default: false
  },
  rounded: {
    type: Boolean,
    default: false
  },
  // Props para RouterLink
  to: {
    type: [String, Object],
    default: undefined
  },
  // Props para enlaces externos
  href: {
    type: String,
    default: undefined
  },
  target: {
    type: String,
    default: undefined
  },
  rel: {
    type: String,
    default: undefined
  }
})

const emit = defineEmits(['click'])

// Computed properties
const tag = computed(() => {
  if (props.to) return 'RouterLink'
  if (props.href) return 'a'
  return 'button'
})

const buttonType = computed(() => {
  return tag.value === 'button' ? props.type : undefined
})

const buttonClasses = computed(() => [
  `btn-${props.variant}`,
  `btn-${props.size}`,
  {
    'btn-block': props.block,
    'btn-rounded': props.rounded,
    'btn-loading': props.loading,
    'btn-disabled': props.disabled
  }
])

// Methods
const handleClick = (event) => {
  if (props.disabled || props.loading) {
    event.preventDefault()
    return
  }
  emit('click', event)
}
</script>

<style scoped>
.base-button {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-family: inherit;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  border: 2px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  user-select: none;
  vertical-align: middle;
  line-height: 1;
  white-space: nowrap;
  overflow: hidden;
}

.base-button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

/* Contenido del botón */
.button-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: opacity 0.2s ease;
}

.button-content-hidden {
  opacity: 0;
}

/* Spinner */
.button-spinner {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: button-spin 1s linear infinite;
}

/* Variantes de color */
.btn-primary {
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  color: white;
  border-color: #7d1c2b;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #a27345 0%, #7d1c2b 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(125, 28, 43, 0.3);
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border-color: #6c757d;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
  border-color: #545b62;
  transform: translateY(-1px);
}

.btn-success {
  background: #28a745;
  color: white;
  border-color: #28a745;
}

.btn-success:hover:not(:disabled) {
  background: #218838;
  border-color: #1e7e34;
  transform: translateY(-1px);
}

.btn-danger {
  background: #dc3545;
  color: white;
  border-color: #dc3545;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
  border-color: #bd2130;
  transform: translateY(-1px);
}

.btn-warning {
  background: #ffc107;
  color: #212529;
  border-color: #ffc107;
}

.btn-warning:hover:not(:disabled) {
  background: #e0a800;
  border-color: #d39e00;
  transform: translateY(-1px);
}

/* Variantes outline */
.btn-outline-primary {
  background: transparent;
  color: #7d1c2b;
  border-color: #7d1c2b;
}

.btn-outline-primary:hover:not(:disabled) {
  background: #7d1c2b;
  color: white;
  transform: translateY(-1px);
}

.btn-outline-secondary {
  background: transparent;
  color: #6c757d;
  border-color: #6c757d;
}

.btn-outline-secondary:hover:not(:disabled) {
  background: #6c757d;
  color: white;
  transform: translateY(-1px);
}

/* Variante ghost */
.btn-ghost {
  background: transparent;
  color: #7d1c2b;
  border-color: transparent;
}

.btn-ghost:hover:not(:disabled) {
  background: rgba(125, 28, 43, 0.1);
  color: #7d1c2b;
}

/* Tamaños */
.btn-small {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 6px;
}

.btn-small .spinner {
  width: 14px;
  height: 14px;
}

.btn-medium {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1.125rem;
  border-radius: 10px;
}

.btn-large .spinner {
  width: 20px;
  height: 20px;
}

.btn-extra-large {
  padding: 1.25rem 2.5rem;
  font-size: 1.25rem;
  border-radius: 12px;
}

.btn-extra-large .spinner {
  width: 24px;
  height: 24px;
}

/* Modificadores */
.btn-block {
  display: flex;
  width: 100%;
}

.btn-rounded {
  border-radius: 50px;
}

/* Estados */
.btn-loading {
  pointer-events: none;
}

.btn-disabled,
.base-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

/* Animaciones */
@keyframes button-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Focus visible para accesibilidad */
.base-button:focus-visible {
  box-shadow: 0 0 0 3px rgba(125, 28, 43, 0.3);
}

/* Responsive */
@media (max-width: 576px) {
  .btn-medium {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
  }
  
  .btn-large {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }
}
</style>
