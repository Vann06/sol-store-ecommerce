<template>
  <div class="account-details">
    <PageHeader title="Datos Personales" subtitle="Mantén tu información actualizada" />

    <div v-if="user" class="form-container">
      <form @submit.prevent="saveChanges" class="account-form">
        <div class="form-grid">
          <FormInput
            id="firstName"
            v-model="formData.first_name"
            label="Nombre"
            type="text"
            icon="fas fa-user"
            placeholder="Ingresa tu nombre"
            :required="true"
            :error="errors.first_name"
          />

          <FormInput
            id="lastName"
            v-model="formData.last_name"
            label="Apellido"
            type="text"
            icon="fas fa-user"
            placeholder="Ingresa tu apellido"
            :required="true"
            :error="errors.last_name"
          />

          <FormInput
            id="email"
            v-model="formData.email"
            label="Correo Electrónico"
            type="email"
            icon="fas fa-envelope"
            placeholder="Ingresa tu correo electrónico"
            :required="true"
            :error="errors.email"
            class="form-group-full"
          />

          <FormInput
            id="phone"
            v-model="formData.phone"
            label="Teléfono"
            type="tel"
            icon="fas fa-phone"
            placeholder="Ingresa tu teléfono"
            :error="errors.phone"
          />

          <FormInput
            id="birthDate"
            v-model="formData.birth_date"
            label="Fecha de Nacimiento"
            type="date"
            icon="fas fa-calendar"
            :error="errors.birth_date"
          />
        </div>

        <div class="form-actions">
          <BaseButton 
            type="button" 
            variant="secondary" 
            @click="resetForm"
          >
            <i class="fas fa-undo"></i>
            Cancelar
          </BaseButton>
          
          <BaseButton 
            type="submit" 
            variant="primary" 
            :loading="loading"
          >
            <i class="fas fa-save"></i>
            Guardar Cambios
          </BaseButton>
        </div>
      </form>
    </div>

    <div v-else class="loading-container">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando información...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useUserStore } from '@/stores/userStore'
import PageHeader from '@/components/PageHeader.vue'
import FormInput from '@/components/FormInput.vue'
import BaseButton from '@/components/BaseButton.vue'
import { useMessages } from '@/composables/useMessages'

const userStore = useUserStore()
const { showMessage } = useMessages()

// Datos reactivos
const formData = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  birth_date: ''
})

const originalData = ref({})
const errors = ref({})
const loading = ref(false)

// Computed
const user = computed(() => userStore.user)
const hasChanges = computed(() => {
  return JSON.stringify(formData.value) !== JSON.stringify(originalData.value)
})

// Métodos
const initFormData = () => {
  if (user.value) {
    formData.value = {
      first_name: user.value.first_name || '',
      last_name: user.value.last_name || '',
      email: user.value.email || '',
      phone: user.value.phone || '',
      birth_date: user.value.birth_date || ''
    }
    originalData.value = { ...formData.value }
  }
}

const validateForm = () => {
  errors.value = {}
  let isValid = true

  if (!formData.value.first_name.trim()) {
    errors.value.first_name = 'El nombre es obligatorio'
    isValid = false
  }

  if (!formData.value.last_name.trim()) {
    errors.value.last_name = 'El apellido es obligatorio'
    isValid = false
  }

  if (!formData.value.email.trim()) {
    errors.value.email = 'El correo electrónico es obligatorio'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'El formato del correo electrónico no es válido'
    isValid = false
  }

  if (formData.value.phone && !/^\+?[\d\s-()]{8,}$/.test(formData.value.phone)) {
    errors.value.phone = 'El formato del teléfono no es válido'
    isValid = false
  }

  return isValid
}

const saveChanges = async () => {
  if (!validateForm()) return

  loading.value = true
  try {
    // Simular llamada API
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Actualizar el store (en una app real, esto vendría del backend)
    userStore.updateUser(formData.value)
    originalData.value = { ...formData.value }
    
    showMessage('Información actualizada exitosamente', 'success')
  } catch (error) {
    console.error('Error updating profile:', error)
    showMessage('Error al actualizar la información', 'error')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  formData.value = { ...originalData.value }
  errors.value = {}
}

// Lifecycle
onMounted(() => {
  initFormData()
})

// Watchers
watch(user, () => {
  initFormData()
}, { immediate: true })
</script>

<style scoped>
.account-details {
  padding: 1.5rem;
  max-width: 800px;
  margin: 0 auto;
}

.form-container {
  background: var(--surface);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(31,41,55,0.08);
  border: 1px solid rgba(122, 0, 25, 0.1);
  margin-top: 1.5rem;
}

.account-form {
  width: 100%;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.form-group-full {
  grid-column: 1 / -1;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1.5rem;
  border-top: 1px solid var(--ink-5);
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
  background: var(--surface);
  border-radius: 16px;
  margin-top: 1.5rem;
  box-shadow: 0 4px 20px rgba(31,41,55,0.08);
}

.loading-spinner { text-align: center; color: var(--brand); }

.loading-spinner i { font-size: 2rem; margin-bottom: 1rem; color: var(--accent); }

.loading-spinner p { font-weight: 500; color: var(--brand); }

@media (max-width: 768px) {
  .account-details {
    padding: 1rem;
  }

  .form-container {
    padding: 1.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>
