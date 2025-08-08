<template>
  <div class="add-to-cart-container">
    <!-- Selector de cantidad (si showQuantitySelector es true) -->
    <div v-if="showQuantitySelector" class="quantity-selector">
      <label for="quantity" class="quantity-label">Cantidad:</label>
      <div class="quantity-input-group">
        <button 
          type="button"
          class="quantity-btn quantity-btn-decrease"
          :disabled="localQuantity <= 1"
          @click="decreaseQuantity"
        >
          <i class="fas fa-minus"></i>
        </button>
        <input 
          id="quantity"
          type="number" 
          class="quantity-input"
          v-model.number="localQuantity"
          :min="1"
          :max="maxQuantity"
          @blur="validateQuantity"
        />
        <button 
          type="button"
          class="quantity-btn quantity-btn-increase"
          :disabled="localQuantity >= maxQuantity"
          @click="increaseQuantity"
        >
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <!-- Botón principal -->
    <BaseButton
      :variant="buttonVariant"
      :size="size"
      :disabled="isDisabled"
      :loading="isLoading"
      :block="block"
      @click="handleAddToCart"
      class="add-to-cart-btn"
    >
      <i :class="buttonIcon" class="btn-icon"></i>
      {{ buttonText }}
    </BaseButton>

    <!-- Información adicional del producto -->
    <div v-if="showProductInfo && product" class="product-info">
      <p v-if="product.stock !== undefined" class="stock-info">
        <i class="fas fa-box"></i>
        {{ product.stock > 0 ? `${product.stock} disponibles` : 'Sin stock' }}
      </p>
      <p v-if="product.precio" class="price-info">
        <span class="current-price">{{ formatPrice(product.precio) }}</span>
        <span v-if="product.precio_original && product.precio_original > product.precio" class="original-price">
          {{ formatPrice(product.precio_original) }}
        </span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import BaseButton from '@/components/Account/BaseButton.vue'
import { useCart } from '@/composables/useCart'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  quantity: {
    type: Number,
    default: 1
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary'
  },
  block: {
    type: Boolean,
    default: false
  },
  showQuantitySelector: {
    type: Boolean,
    default: true
  },
  showProductInfo: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  customText: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['added', 'error', 'quantity-change'])

// Composables
const { 
  addToCart, 
  isProductInCart, 
  getProductQuantity, 
  formatPrice,
  isProductAvailable,
  operationStates,
  isAuthenticated
} = useCart()

// Estado local
const localQuantity = ref(props.quantity)

// Computed properties
const maxQuantity = computed(() => {
  return props.product?.stock || 99
})

const isInCart = computed(() => {
  return isProductInCart(props.product?.id)
})

const currentQuantityInCart = computed(() => {
  return getProductQuantity(props.product?.id)
})

const isAvailable = computed(() => {
  return isProductAvailable(props.product)
})

const isLoading = computed(() => {
  return operationStates.value.isAdding
})

const isDisabled = computed(() => {
  return props.disabled || 
         !isAvailable.value || 
         !isAuthenticated.value ||
         isLoading.value ||
         localQuantity.value < 1 ||
         localQuantity.value > maxQuantity.value
})

const buttonVariant = computed(() => {
  if (!isAuthenticated.value) return 'secondary'
  if (!isAvailable.value) return 'secondary'
  if (isInCart.value) return 'success'
  return props.variant
})

const buttonIcon = computed(() => {
  if (!isAuthenticated.value) return 'fas fa-sign-in-alt'
  if (!isAvailable.value) return 'fas fa-times-circle'
  if (isInCart.value) return 'fas fa-check'
  return 'fas fa-shopping-cart'
})

const buttonText = computed(() => {
  if (props.customText) return props.customText
  
  if (!isAuthenticated.value) return 'Iniciar Sesión'
  if (!isAvailable.value) return 'No Disponible'
  if (isInCart.value) return `En Carrito (${currentQuantityInCart.value})`
  
  return props.showQuantitySelector 
    ? `Agregar ${localQuantity.value} al carrito`
    : 'Agregar al carrito'
})

// Métodos
const increaseQuantity = () => {
  if (localQuantity.value < maxQuantity.value) {
    localQuantity.value++
  }
}

const decreaseQuantity = () => {
  if (localQuantity.value > 1) {
    localQuantity.value--
  }
}

const validateQuantity = () => {
  if (localQuantity.value < 1) {
    localQuantity.value = 1
  } else if (localQuantity.value > maxQuantity.value) {
    localQuantity.value = maxQuantity.value
  }
}

const handleAddToCart = async () => {
  if (!isAuthenticated.value) {
    // Emitir evento para que el componente padre maneje la redirección al login
    emit('error', { type: 'auth_required', message: 'Se requiere autenticación' })
    return
  }

  if (!isAvailable.value) {
    emit('error', { type: 'unavailable', message: 'Producto no disponible' })
    return
  }

  try {
    const result = await addToCart(props.product, localQuantity.value)

    if (result.success) {
      emit('added', {
        product: props.product,
        quantity: localQuantity.value,
        totalInCart: getProductQuantity(props.product.id)
      })
    } else {
      emit('error', {
        type: 'add_failed',
        message: result.error || 'Error al agregar al carrito'
      })
    }
  } catch (error) {
    console.error('Error adding to cart:', error)
    emit('error', {
      type: 'unexpected',
      message: 'Error inesperado'
    })
  }
}

// Watchers
watch(localQuantity, (newQuantity) => {
  emit('quantity-change', newQuantity)
})

// Sincronizar con props
watch(() => props.quantity, (newQuantity) => {
  localQuantity.value = newQuantity
})
</script>

<style scoped>
.add-to-cart-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Selector de cantidad */
.quantity-selector {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.quantity-label {
  font-weight: 600;
  color: #7d1c2b;
  font-size: 0.875rem;
}

.quantity-input-group {
  display: flex;
  align-items: center;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  background: white;
  overflow: hidden;
  max-width: 140px;
}

.quantity-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #7d1c2b;
}

.quantity-btn:hover:not(:disabled) {
  background: rgba(125, 28, 43, 0.1);
}

.quantity-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-input {
  width: 60px;
  text-align: center;
  border: none;
  outline: none;
  font-weight: 600;
  color: #7d1c2b;
  background: transparent;
  height: 36px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.quantity-input[type="number"] {
  -moz-appearance: textfield;
}

/* Botón principal */
.add-to-cart-btn {
  transition: all 0.3s ease;
}

.btn-icon {
  font-size: 1rem;
}

/* Información del producto */
.product-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: rgba(125, 28, 43, 0.05);
  border-radius: 8px;
  font-size: 0.875rem;
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  margin: 0;
}

.stock-info i {
  color: #e5bf60;
}

.price-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

.current-price {
  font-weight: 700;
  color: #7d1c2b;
  font-size: 1rem;
}

.original-price {
  font-size: 0.8rem;
  color: #6c757d;
  text-decoration: line-through;
}

/* Estados especiales */
.add-to-cart-container.compact {
  gap: 0.5rem;
}

.add-to-cart-container.compact .quantity-input-group {
  max-width: 120px;
}

.add-to-cart-container.compact .quantity-btn {
  width: 32px;
  height: 32px;
}

.add-to-cart-container.compact .quantity-input {
  width: 50px;
  height: 32px;
}

/* Responsive */
@media (max-width: 576px) {
  .quantity-input-group {
    max-width: 120px;
  }
  
  .quantity-btn {
    width: 32px;
    height: 32px;
  }
  
  .quantity-input {
    width: 50px;
    height: 32px;
  }
  
  .product-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style>
