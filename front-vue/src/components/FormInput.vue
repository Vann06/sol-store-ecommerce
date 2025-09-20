<template>
  <div class="form-input-wrapper" :class="wrapperClasses">
    <label v-if="label" :for="id" class="form-label">
      <i v-if="icon" :class="icon" class="label-icon"></i>
      {{ label }}
      <span v-if="required" class="required-marker">*</span>
    </label>
    
    <div class="input-container" :class="containerClasses">
      <!-- Input principal -->
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :autocomplete="autocomplete"
        class="form-input"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        v-bind="$attrs"
      />
      
      <!-- Ícono izquierdo -->
      <div v-if="icon && showIconInInput" class="input-icon input-icon-left">
        <i :class="icon"></i>
      </div>
      
      <!-- Ícono derecho / botón -->
      <div v-if="rightIcon || showClearButton" class="input-icon input-icon-right">
        <button 
          v-if="showClearButton && modelValue" 
          type="button" 
          class="clear-button"
          @click="clearInput"
        >
          <i class="fas fa-times"></i>
        </button>
        <i v-else-if="rightIcon" :class="rightIcon"></i>
      </div>
    </div>
    
    <!-- Mensaje de error -->
    <div v-if="error" class="error-message">
      <i class="fas fa-exclamation-circle"></i>
      {{ error }}
    </div>
    
    <!-- Texto de ayuda -->
    <div v-if="help && !error" class="help-text">
      {{ help }}
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  rightIcon: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  help: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  showIconInInput: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'outlined', 'filled'].includes(value)
  },
  // Props para inputs numéricos
  min: {
    type: [String, Number],
    default: undefined
  },
  max: {
    type: [String, Number],
    default: undefined
  },
  step: {
    type: [String, Number],
    default: undefined
  },
  autocomplete: {
    type: String,
    default: undefined
  }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'clear'])

// Estado interno
const isFocused = ref(false)

// Computed properties
const wrapperClasses = computed(() => [
  `form-input-${props.size}`,
  `form-input-${props.variant}`,
  {
    'form-input-focused': isFocused.value,
    'form-input-error': props.error,
    'form-input-disabled': props.disabled
  }
])

const containerClasses = computed(() => ({
  'has-icon-left': props.icon && props.showIconInInput,
  'has-icon-right': props.rightIcon || showClearButton.value
}))

const inputClasses = computed(() => ({
  'input-error': props.error,
  'input-focused': isFocused.value
}))

const showClearButton = computed(() => {
  return props.clearable && !props.disabled && !props.readonly
})

// Methods
const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const handleFocus = (event) => {
  isFocused.value = true
  emit('focus', event)
}

const handleBlur = (event) => {
  isFocused.value = false
  emit('blur', event)
}

const clearInput = () => {
  emit('update:modelValue', '')
  emit('clear')
}
</script>

<style scoped>
.form-input-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
}

/* Label */
.form-label {
  font-weight: 600;
  color: #7d1c2b;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.label-icon {
  color: #e5bf60;
}

.required-marker {
  color: #dc3545;
  font-weight: 700;
}

/* Input container */
.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

/* Input base */
.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
  color: #495057;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: #e5bf60;
  background: white;
  box-shadow: 0 0 0 3px rgba(229, 191, 96, 0.1);
}

.form-input::placeholder {
  color: #adb5bd;
  opacity: 1;
}

/* Input con iconos */
.input-container.has-icon-left .form-input {
  padding-left: 2.75rem;
}

.input-container.has-icon-right .form-input {
  padding-right: 2.75rem;
}

/* Iconos */
.input-icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  z-index: 2;
}

.input-icon-left {
  left: 0.75rem;
}

.input-icon-right {
  right: 0.75rem;
}

.clear-button {
  background: none;
  border: none;
  color: #adb5bd;
  cursor: pointer;
  font-size: 0.875rem;
  padding: 0.25rem;
  border-radius: 4px;
  transition: color 0.2s ease;
}

.clear-button:hover {
  color: #6c757d;
  background: rgba(108, 117, 125, 0.1);
}

/* Estados */
.form-input-error .form-input,
.form-input.input-error {
  border-color: #dc3545;
  background-color: #fff5f5;
}

.form-input-error .form-input:focus,
.form-input.input-error:focus {
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.form-input:disabled {
  background-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

.form-input:readonly {
  background-color: #f8f9fa;
  border-color: #dee2e6;
}

/* Mensajes */
.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  font-weight: 500;
}

.help-text {
  color: #6c757d;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Variantes de tamaño */
.form-input-small .form-input {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.form-input-small.has-icon-left .form-input {
  padding-left: 2.25rem;
}

.form-input-small.has-icon-right .form-input {
  padding-right: 2.25rem;
}

.form-input-large .form-input {
  padding: 1rem 1.25rem;
  font-size: 1.125rem;
}

.form-input-large.has-icon-left .form-input {
  padding-left: 3.25rem;
}

.form-input-large.has-icon-right .form-input {
  padding-right: 3.25rem;
}

/* Variantes de estilo */
.form-input-outlined .form-input {
  background: transparent;
  border-width: 2px;
}

.form-input-filled .form-input {
  background: #e9ecef;
  border: 1px solid transparent;
}

.form-input-filled .form-input:focus {
  background: white;
  border-color: #e5bf60;
}

/* Responsive */
@media (max-width: 576px) {
  .form-input {
    font-size: 16px; /* Previene zoom en iOS */
  }
}
</style>
