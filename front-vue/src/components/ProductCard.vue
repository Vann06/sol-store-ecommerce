<template>
  <div class="product-card">
    <div class="product-image">
      <img 
        :src="product.imagen || '/img/no-image.png'" 
        :alt="product.nombre"
        class="product-img"
      />
      <div v-if="product.descuento" class="discount-badge">
        -{{ product.descuento }}%
      </div>
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ product.nombre }}</h3>
      <p v-if="product.descripcion" class="product-description">
        {{ truncateText(product.descripcion, 100) }}
      </p>
      
      <div class="product-pricing">
        <span class="current-price">{{ formatPrice(finalPrice) }}</span>
        <span 
          v-if="product.precio_original && product.descuento" 
          class="original-price"
        >
          {{ formatPrice(product.precio_original) }}
        </span>
      </div>
      
      <div class="product-meta">
        <div class="stock-info">
          <i class="fas fa-box"></i>
          <span :class="stockClass">{{ stockText }}</span>
        </div>
        <div v-if="product.rating" class="rating">
          <div class="stars">
            <i 
              v-for="star in 5" 
              :key="star"
              class="fas fa-star"
              :class="{ active: star <= product.rating }"
            ></i>
          </div>
          <span class="rating-count">({{ product.reviews_count || 0 }})</span>
        </div>
      </div>
    </div>
    
    <div class="product-actions">
      <!-- Componente AddToCartButton -->
      <AddToCartButton
        :product="product"
        :quantity="1"
        size="medium"
        :block="true"
        :show-quantity-selector="showQuantitySelector"
        :show-product-info="false"
        @added="handleProductAdded"
        @error="handleAddToCartError"
      />
      
      <!-- Botones adicionales -->
      <div class="secondary-actions">
        <BaseButton
          variant="ghost"
          size="small"
          @click="toggleWishlist"
          :class="{ 'in-wishlist': isInWishlist }"
        >
          <i :class="wishlistIcon"></i>
          {{ wishlistText }}
        </BaseButton>
        
        <BaseButton
          variant="ghost"
          size="small"
          @click="openQuickView"
        >
          <i class="fas fa-eye"></i>
          Vista rápida
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import AddToCartButton from '@/components/AddToCartButton.vue'
import BaseButton from '@/components/BaseButton.vue'
import { useCart } from '@/composables/useCart'
import { useMessages } from '@/composables/useMessages'
import { useRouter } from 'vue-router'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  showQuantitySelector: {
    type: Boolean,
    default: false
  },
  compact: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['added-to-cart', 'wishlist-toggle', 'quick-view'])

// Composables
const { formatPrice } = useCart()
const { showMessage } = useMessages()
const router = useRouter()

// Estado local
const isInWishlist = ref(false) // Esto vendría de un store de wishlist

// Computed properties
const finalPrice = computed(() => {
  if (props.product.descuento && props.product.precio_original) {
    return props.product.precio_original * (1 - props.product.descuento / 100)
  }
  return props.product.precio
})

const stockClass = computed(() => {
  const stock = props.product.stock
  if (stock <= 0) return 'stock-out'
  if (stock <= 5) return 'stock-low'
  return 'stock-available'
})

const stockText = computed(() => {
  const stock = props.product.stock
  if (stock <= 0) return 'Sin stock'
  if (stock <= 5) return `Últimos ${stock}`
  return `${stock} disponibles`
})

const wishlistIcon = computed(() => {
  return isInWishlist.value ? 'fas fa-heart' : 'far fa-heart'
})

const wishlistText = computed(() => {
  return isInWishlist.value ? 'Quitar' : 'Favorito'
})

// Métodos
const truncateText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}

const handleProductAdded = (data) => {
  emit('added-to-cart', data)
  showMessage(`${data.product.nombre} agregado al carrito`, 'success')
}

const handleAddToCartError = (error) => {
  if (error.type === 'auth_required') {
    showMessage('Debes iniciar sesión para agregar productos', 'warning')
    // Opcional: redirigir al login
    // router.push('/login')
  } else if (error.type === 'unavailable') {
    showMessage('Este producto no está disponible', 'warning')
  } else {
    showMessage(error.message || 'Error al agregar al carrito', 'error')
  }
}

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value
  emit('wishlist-toggle', {
    product: props.product,
    isInWishlist: isInWishlist.value
  })
  
  const message = isInWishlist.value 
    ? 'Agregado a favoritos' 
    : 'Quitado de favoritos'
  showMessage(message, 'success')
}

const openQuickView = () => {
  emit('quick-view', props.product)
  // Aquí podrías abrir un modal con más detalles del producto
}
</script>

<style scoped>
.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(125, 28, 43, 0.15);
}

.product-image {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

.discount-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  color: white;
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  z-index: 2;
}

.product-info {
  padding: 1rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.product-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0;
  line-height: 1.3;
}

.product-description {
  color: #718096;
  font-size: 0.875rem;
  margin: 0;
  line-height: 1.4;
}

.product-pricing {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.current-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #7d1c2b;
}

.original-price {
  font-size: 1rem;
  color: #a0aec0;
  text-decoration: line-through;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.stock-info i {
  color: #e5bf60;
}

.stock-available {
  color: #48bb78;
  font-weight: 500;
}

.stock-low {
  color: #ed8936;
  font-weight: 600;
}

.stock-out {
  color: #e53e3e;
  font-weight: 600;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stars {
  display: flex;
  gap: 2px;
}

.stars i {
  font-size: 0.875rem;
  color: #e2e8f0;
  transition: color 0.2s ease;
}

.stars i.active {
  color: #fbbf24;
}

.rating-count {
  font-size: 0.75rem;
  color: #718096;
}

.product-actions {
  padding: 1rem;
  border-top: 1px solid #f0f0f0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.secondary-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

.in-wishlist {
  color: #e53e3e !important;
}

/* Responsive */
@media (max-width: 576px) {
  .product-info {
    padding: 0.75rem;
    gap: 0.5rem;
  }
  
  .product-name {
    font-size: 1rem;
  }
  
  .current-price {
    font-size: 1.125rem;
  }
  
  .product-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .secondary-actions {
    flex-direction: column;
  }
}
</style>
