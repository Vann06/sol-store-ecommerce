<template>
  <div class="cart-item" :class="{ 'cart-item-compact': compact }">
    <div class="item-image">
      <img 
        :src="itemImage" 
        :alt="item.nombre"
        class="product-image"
        @error="handleImageError"
      />
    </div>
    
    <div class="item-details">
      <h4 class="item-name">{{ item.nombre }}</h4>
      <div class="item-meta">
        <span v-if="item.categoria" class="item-category">{{ item.categoria }}</span>
        <span v-if="item.tematica" class="item-theme">{{ item.tematica }}</span>
      </div>
      <div class="item-price">
        <span class="unit-price">{{ formatPrice(item.precio_unitario) }} c/u</span>
        <span class="subtotal">{{ formatPrice(item.subtotal) }}</span>
      </div>
    </div>
    
    <div class="item-controls">
      <!-- Controles de cantidad -->
      <div class="quantity-controls">
        <button 
          type="button"
          class="qty-btn qty-decrease"
          :disabled="updating || item.cantidad <= 1"
          @click="updateQuantity(item.cantidad - 1)"
        >
          <i class="fas fa-minus"></i>
        </button>
        
        <span class="quantity">{{ item.cantidad }}</span>
        
        <button 
          type="button"
          class="qty-btn qty-increase"
          :disabled="updating"
          @click="updateQuantity(item.cantidad + 1)"
        >
          <i class="fas fa-plus"></i>
        </button>
      </div>
      
      <!-- Botón eliminar -->
      <button 
        type="button"
        class="remove-btn"
        :disabled="removing"
        @click="removeItem"
        title="Eliminar producto"
      >
        <i class="fas fa-trash" v-if="!removing"></i>
        <i class="fas fa-spinner fa-spin" v-else></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  compact: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update-quantity', 'remove'])

// Estado local
const updating = ref(false)
const removing = ref(false)
const imageError = ref(false)

// Computed
const itemImage = computed(() => {
  if (imageError.value) {
    return '/img/no-image-placeholder.png' // Imagen por defecto
  }
  
  // El backend puede devolver diferentes formatos de imagen
  return props.item.imagen || '/img/no-image-placeholder.png'
})

// Métodos
const formatPrice = (price) => {
  if (!price) return 'Q 0.00'
  return `Q ${Number(price).toFixed(2)}`
}

const handleImageError = () => {
  imageError.value = true
}

const updateQuantity = async (newQuantity) => {
  if (newQuantity < 1 || updating.value) return
  
  updating.value = true
  
  try {
    await emit('update-quantity', props.item.id, newQuantity)
  } finally {
    updating.value = false
  }
}

const removeItem = async () => {
  if (removing.value) return
  
  removing.value = true
  
  try {
    await emit('remove', props.item.id)
  } finally {
    removing.value = false
  }
}
</script>

<style scoped>
.cart-item {
  display: flex;
  gap: 12px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  transition: all 0.2s ease;
}

.cart-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.cart-item-compact {
  padding: 12px;
  gap: 8px;
}

.item-image {
  flex-shrink: 0;
}

.product-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  background: #f3f4f6;
}

.cart-item-compact .product-image {
  width: 48px;
  height: 48px;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 4px 0;
  line-height: 1.2;
}

.cart-item-compact .item-name {
  font-size: 13px;
  margin: 0 0 2px 0;
}

.item-meta {
  display: flex;
  gap: 8px;
  margin-bottom: 6px;
}

.item-category,
.item-theme {
  font-size: 11px;
  color: #6b7280;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 4px;
}

.item-price {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.unit-price {
  font-size: 12px;
  color: #6b7280;
}

.subtotal {
  font-size: 14px;
  font-weight: 600;
  color: #7d1c2b;
}

.cart-item-compact .subtotal {
  font-size: 13px;
}

.item-controls {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: flex-end;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f9fafb;
  border-radius: 6px;
  padding: 4px;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border: none;
  border-radius: 4px;
  background: white;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 12px;
}

.qty-btn:hover:not(:disabled) {
  color: #7d1c2b;
  background: #f3f4f6;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  min-width: 24px;
  text-align: center;
  font-weight: 600;
  font-size: 14px;
  color: #111827;
}

.remove-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  background: #fef2f2;
  color: #dc2626;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 12px;
}

.remove-btn:hover:not(:disabled) {
  background: #fee2e2;
  color: #991b1b;
}

.remove-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 640px) {
  .cart-item {
    flex-direction: column;
    gap: 8px;
  }
  
  .item-controls {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}
</style>
