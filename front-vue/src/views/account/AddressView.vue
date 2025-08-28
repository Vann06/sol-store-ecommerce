<template>
  <div class="address-view">
    <div class="page-header">
      <h2><i class="fa fa-map-marker-alt"></i> Direcciones de Envío</h2>
      <p class="page-subtitle">Gestiona las direcciones donde quieres recibir tus pedidos</p>
    </div>

    <!-- Lista de direcciones guardadas -->
    <div class="addresses-list" v-if="addresses.length > 0">
      <h3><i class="fa fa-home"></i> Direcciones Guardadas</h3>
      <div class="addresses-grid">
        <div 
          v-for="address in addresses" 
          :key="address.id"
          :class="['address-card', { 'default': address.is_default }]"
        >
          <div class="address-header">
            <h4>{{ getTitle(address) }}</h4>
            <div class="address-badges">
              <span v-if="address.is_default" class="badge default-badge">
                <i class="fa fa-star"></i> Predeterminada
              </span>
              <span :class="['badge', 'type-badge', address.type]">
                <i :class="getTypeIcon(address.type)"></i>
                {{ getTypeLabel(address.type) }}
              </span>
            </div>
          </div>
          
          <div class="address-content">
            <p class="address-text">
              <i class="fa fa-map-marker-alt"></i>
              {{ formatAddress(address) }}
            </p>
            <p v-if="address.phone" class="address-phone">
              <i class="fa fa-phone"></i>
              {{ address.phone }}
            </p>
            <p v-if="address.notes" class="address-notes">
              <i class="fa fa-sticky-note"></i>
              {{ address.notes }}
            </p>
          </div>
          
          <div class="address-actions">
            <button @click="editAddress(address)" class="btn btn-outline btn-sm">
              <i class="fa fa-edit"></i>
              Editar
            </button>
            <button 
              v-if="!address.is_default"
              @click="setDefault(address.id)" 
              class="btn btn-outline btn-sm"
            >
              <i class="fa fa-star"></i>
              Predeterminada
            </button>
            <button 
              @click="deleteAddress(address.id)" 
              class="btn btn-danger btn-sm"
              :disabled="address.is_default"
            >
              <i class="fa fa-trash"></i>
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulario para nueva dirección o editar -->
    <div class="form-container">
      <h3>
        <i class="fa fa-plus-circle"></i>
        {{ editingAddress ? 'Editar Dirección' : 'Agregar Nueva Dirección' }}
      </h3>
      
      <form @submit.prevent="saveAddress" class="address-form">
        <div class="form-grid">
          <div class="form-group">
            <label for="title">Nombre de la Dirección *</label>
            <div class="input-group">
              <i class="fa fa-tag input-icon"></i>
              <input 
                type="text" 
                id="title"
                v-model="formData.title" 
                placeholder="ej: Casa, Trabajo, Casa de mis padres"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="type">Tipo de Dirección</label>
            <div class="input-group">
              <i class="fa fa-home input-icon"></i>
              <select 
                id="type"
                v-model="formData.type" 
                class="form-input"
              >
                <option value="home">Casa</option>
                <option value="work">Trabajo</option>
                <option value="other">Otro</option>
              </select>
            </div>
          </div>

          <div class="form-group form-group-full">
            <label for="street">Dirección Completa *</label>
            <div class="input-group">
              <i class="fa fa-road input-icon"></i>
              <input 
                type="text" 
                id="street"
                v-model="formData.street" 
                placeholder="Calle, número, colonia/barrio"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="city">Ciudad *</label>
            <div class="input-group">
              <i class="fa fa-city input-icon"></i>
              <input 
                type="text" 
                id="city"
                v-model="formData.city" 
                placeholder="Nombre de la ciudad"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="state">Estado/Provincia *</label>
            <div class="input-group">
              <i class="fa fa-map input-icon"></i>
              <input 
                type="text" 
                id="state"
                v-model="formData.state" 
                placeholder="Estado o provincia"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="zipCode">Código Postal *</label>
            <div class="input-group">
              <i class="fa fa-mail-bulk input-icon"></i>
              <input 
                type="text" 
                id="zipCode"
                v-model="formData.zip_code" 
                placeholder="12345"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="country">País *</label>
            <div class="input-group">
              <i class="fa fa-flag input-icon"></i>
              <select 
                id="country"
                v-model="formData.country" 
                class="form-input"
                required
              >
                <option value="">Seleccionar país</option>
                <option value="MX">México</option>
                <option value="US">Estados Unidos</option>
                <option value="CA">Canadá</option>
                <option value="ES">España</option>
                <option value="AR">Argentina</option>
                <option value="CO">Colombia</option>
                <option value="PE">Perú</option>
                <option value="CL">Chile</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="phone">Teléfono de Contacto</label>
            <div class="input-group">
              <i class="fa fa-phone input-icon"></i>
              <input 
                type="tel" 
                id="phone"
                v-model="formData.phone" 
                placeholder="+52 123 456 7890"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group form-group-full">
            <label for="notes">Notas Adicionales</label>
            <div class="input-group">
              <i class="fa fa-sticky-note input-icon"></i>
              <textarea 
                id="notes"
                v-model="formData.notes" 
                placeholder="Referencias, instrucciones especiales, etc."
                class="form-input textarea"
                rows="3"
              ></textarea>
            </div>
          </div>

          <div class="form-group form-group-full">
            <label class="checkbox-label">
              <input 
                type="checkbox"
                v-model="formData.is_default"
                class="checkbox-input"
              />
              <span class="checkbox-custom">
                <i class="fa fa-check"></i>
              </span>
              Establecer como dirección predeterminada
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button 
            type="button" 
            @click="cancelEdit" 
            class="btn btn-secondary"
            v-if="editingAddress"
          >
            <i class="fa fa-times"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <i class="fa fa-save"></i>
            {{ loading ? 'Guardando...' : (editingAddress ? 'Actualizar' : 'Guardar Dirección') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Mensaje de éxito/error -->
    <div v-if="message" :class="['message', messageType]" class="form-message">
      <i :class="messageIcon"></i>
      <span>{{ message }}</span>
    </div>
  </div>
 </template><script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAddressesStore } from '@/stores/addresses'

const loading = ref(false)
const message = ref('')
const messageType = ref('')
const editingAddress = ref(null)
const router = useRouter()
const addressesStore = useAddressesStore()
const addresses = computed(() => addressesStore.items)

const formData = reactive({
  title: '',
  type: 'home',
  street: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
  phone: '',
  notes: '',
  is_default: false
})

const messageIcon = computed(() => {
  switch (messageType.value) {
    case 'success': return 'fa fa-check-circle'
    case 'error': return 'fa fa-exclamation-circle'
    case 'warning': return 'fa fa-exclamation-triangle'
    default: return 'fa fa-info-circle'
  }
})

const getTypeIcon = (type) => {
  const icons = {
    home: 'fa fa-home',
    work: 'fa fa-briefcase',
    other: 'fa fa-map-marker-alt'
  }
  return icons[type] || 'fa fa-map-marker-alt'
}

const getTypeLabel = (type) => {
  const labels = {
    home: 'Casa',
    work: 'Trabajo',
    other: 'Otro'
  }
  return labels[type] || 'Otro'
}

const formatAddress = (address) => {
  if (address?.direccion) return address.direccion
  return `${address.street}, ${address.city}, ${address.state} ${address.zip_code}`
}

const getTitle = (address) => address?.title || 'Dirección'

const editAddress = (address) => {
  editingAddress.value = address.id
  Object.assign(formData, address)
}

const setDefault = async (addressId) => {
  loading.value = true
  const res = await addressesStore.setDefault(addressId)
  if (res.success) showMessage('Dirección predeterminada actualizada', 'success')
  else showMessage(res.error || 'Error al actualizar dirección predeterminada', 'error')
  loading.value = false
}

const deleteAddress = async (addressId) => {
  if (!confirm('¿Estás seguro de que quieres eliminar esta dirección?')) return
  
  loading.value = true
  const res = await addressesStore.remove(addressId)
  if (res.success) showMessage('Dirección eliminada correctamente', 'success')
  else showMessage(res.error || 'Error al eliminar la dirección', 'error')
  loading.value = false
}

const saveAddress = async () => {
  loading.value = true
  let res
  if (editingAddress.value) {
    res = await addressesStore.update(editingAddress.value, {
      direccion: formatAddress(formData),
      is_default: !!formData.is_default
    })
  } else {
    res = await addressesStore.add({
      direccion: formatAddress(formData),
      is_default: !!formData.is_default
    })
  }
  if (res.success) showMessage('Dirección guardada correctamente', 'success')
  else showMessage(res.error || 'Error al guardar la dirección', 'error')
  loading.value = false
  resetForm()
}

const cancelEdit = () => {
  resetForm()
}

const resetForm = () => {
  editingAddress.value = null
  Object.assign(formData, {
    title: '',
    type: 'home',
    street: '',
    city: '',
    state: '',
    zip_code: '',
    country: '',
    phone: '',
    notes: '',
    is_default: false
  })
}

const showMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
    messageType.value = ''
  }, 5000)
}

// Cargar desde backend
onMounted(() => { addressesStore.fetchAll() })
</script>

<style scoped>
.address-view {
  max-width: 1000px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.page-header h2 {
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.page-header h2 i {
  color: #7d1c2b;
  margin-right: 12px;
}

.page-subtitle {
  color: #666;
  font-size: 16px;
  margin: 0;
}

.addresses-list {
  margin-bottom: 40px;
}

.addresses-list h3 {
  color: #333;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.addresses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.address-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 2px solid #E5E7EB;
  transition: all 0.3s ease;
}

.address-card.default {
  border-color: #7d1c2b;
  background: linear-gradient(135deg, #fdf8f0 0%, #f9f1e6 100%);
}

.address-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
  gap: 10px;
}

.address-header h4 {
  color: #333;
  font-size: 18px;
  font-weight: 600;
  margin: 0;
  flex: 1;
}

.address-badges {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-items: flex-end;
}

.badge {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.default-badge {
  background: #fef3c7;
  color: #a27345;
}

.type-badge.home {
  background: #e8f5e8;
  color: #7d1c2b;
}

.type-badge.work {
  background: #e0f2fe;
  color: #7d1c2b;
}

.type-badge.other {
  background: #E5E7EB;
  color: #374151;
}

.address-content {
  margin-bottom: 15px;
}

.address-content p {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin: 0 0 8px 0;
  font-size: 14px;
  color: #666;
}

.address-content i {
  color: #9CA3AF;
  margin-top: 2px;
  flex-shrink: 0;
}

.address-text {
  font-weight: 500;
  color: #333 !important;
}

.address-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
  min-width: auto;
}

.form-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  padding: 30px;
}

.form-container h3 {
  color: #333;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 30px;
}

.form-group-full {
  grid-column: span 2;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: #374151;
  font-weight: 600;
  margin-bottom: 8px;
  font-size: 14px;
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
  top: 12px;
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

.textarea {
  resize: vertical;
  min-height: 80px;
  padding-top: 12px;
}

.textarea + .input-icon {
  top: 20px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  margin-bottom: 0;
  font-weight: 500 !important;
}

.checkbox-input {
  display: none;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid #E5E7EB;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.checkbox-input:checked + .checkbox-custom {
  background: #7d1c2b;
  border-color: #7d1c2b;
  color: white;
}

.checkbox-custom i {
  font-size: 12px;
  opacity: 0;
  transform: scale(0.5);
  transition: all 0.2s ease;
}

.checkbox-input:checked + .checkbox-custom i {
  opacity: 1;
  transform: scale(1);
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid #E5E7EB;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  min-width: 140px;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

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

.btn-secondary:hover {
  background: #E5E7EB;
  border-color: #9CA3AF;
}

.btn-outline {
  background: transparent;
  color: #7d1c2b;
  border: 1px solid #7d1c2b;
}

.btn-outline:hover {
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

.btn-danger:disabled {
  background: #F3F4F6;
  color: #9CA3AF;
  border: 1px solid #E5E7EB;
}

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
  background-color: #D1FAE5;
  color: #065F46;
  border: 1px solid #A7F3D0;
}

.form-message.error {
  background-color: #FEE2E2;
  color: #991B1B;
  border: 1px solid #FECACA;
}

.form-message.warning {
  background-color: #FEF3C7;
  color: #92400E;
  border: 1px solid #FDE68A;
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

/* Responsive */
@media (max-width: 768px) {
  .addresses-grid {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .form-group-full {
    grid-column: span 1;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    min-width: auto;
  }
  
  .address-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .address-badges {
    align-items: flex-start;
    flex-direction: row;
  }
  
  .address-actions {
    justify-content: stretch;
  }
  
  .address-actions .btn {
    flex: 1;
  }
}
</style>
  