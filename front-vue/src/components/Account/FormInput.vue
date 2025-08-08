<template>
  <div class="form-group" :class="{ 'form-group-full': fullWidth }">
    <label :for="id" v-if="label">
      {{ label }}
      <span class="required" v-if="required">*</span>
    </label>
    <div class="input-group">
      <i :class="['input-icon', icon]" v-if="icon"></i>
      <input 
        :id="id"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :class="['form-input', { 'error': hasError }]"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
      />
      <slot name="suffix"></slot>
    </div>
    <span v-if="errorMessage" class="error-message">
      <i class="fa fa-exclamation-circle"></i>
      {{ errorMessage }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  errorMessage: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])

const hasError = computed(() => !!props.errorMessage)
</script>

<style scoped>
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group-full {
  grid-column: span 2;
}

.form-group label {
  color: #374151;
  font-weight: 600;
  margin-bottom: 8px;
  font-size: 14px;
}

.required {
  color: #EF4444;
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
  padding: 12px 12px 12px 40px;
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

.error-message {
  color: #EF4444;
  font-size: 12px;
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 4px;
}

@media (max-width: 768px) {
  .form-group-full {
    grid-column: span 1;
  }
}
</style>
