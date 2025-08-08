<template>
  <div class="cart-widget">
    <button 
      class="cart-button"
      @click="toggleCart"
      :class="{ 'cart-button-active': isOpen }"
    >
      <div class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span 
          v-if="totalItems > 0" 
          class="cart-badge"
          :class="{ 'cart-badge-pulse': justAdded }"
        >
          {{ totalItems > 99 ? '99+' : totalItems }}
        </span>
      </div>
      <div class="cart-text">
        <span class="cart-label">Carrito</span>
        <span class="cart-total">{{ formatPrice(totalPrice) }}</span>
      </div>
    </button>

    <!-- Dropdown del carrito -->
    <Transition name="cart-dropdown">
      <div 
        v-if="isOpen" 
        class="cart-dropdown"
        @click.stop
      >
        <div class="cart-header">
          <h3>Mi Carrito</h3>
          <button 
            class="close-cart"
            @click="closeCart"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="cart-body">
          <!-- Loading state -->
          <div v-if="isLoading" class="cart-loading">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Cargando carrito...</p>
          </div>

          <!-- Empty cart -->
          <div v-else-if="isEmpty" class="cart-empty">
            <div class="empty-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h4>Tu carrito está vacío</h4>
            <p>Agrega algunos productos para comenzar</p>
            <BaseButton 
              variant="primary" 
              size="small"
              @click="goToProducts"
            >
              Explorar productos
            </BaseButton>
          </div>

          <!-- Cart items -->
          <div v-else class="cart-items">
            <CartItem
              v-for="item in items"
              :key="item.id"
              :item="item"
              :compact="true"
              @update-quantity="handleUpdateQuantity"
              @remove="handleRemoveItem"
            />
          </div>
        </div>

        <!-- Cart footer con acciones -->
        <div v-if="!isEmpty && !isLoading" class="cart-footer">
          <div class="cart-summary">
            <div class="summary-line">
              <span>Subtotal:</span>
              <span class="price">{{ formatPrice(totalPrice) }}</span>
            </div>
            <div v-if="shipping > 0" class="summary-line">
              <span>Envío:</span>
              <span class="price">{{ formatPrice(shipping) }}</span>
            </div>
            <div class="summary-line summary-total">
              <span>Total:</span>
              <span class="price total-price">{{ formatPrice(totalWithShipping) }}</span>
            </div>
          </div>
          
          <div class="cart-actions">
            <BaseButton 
              variant="outline-primary" 
              size="small"
              @click="goToCart"
            >
              Ver carrito
            </BaseButton>
            <BaseButton 
              variant="primary" 
              size="small"
              @click="goToCheckout"
              :disabled="!canCheckout"
            >
              Finalizar compra
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Overlay para cerrar el dropdown -->
    <div 
      v-if="isOpen" 
      class="cart-overlay"
      @click="closeCart"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'
import BaseButton from '@/components/Account/BaseButton.vue'
import CartItem from '@/components/CartItem.vue'

// Composables
const router = useRouter()
const { 
  items, 
  totalItems, 
  totalPrice, 
  isEmpty, 
  isLoading,
  formatPrice,
  updateQuantity,
  removeFromCart,
  refreshCart
} = useCart()

// Estado local
const isOpen = ref(false)
const justAdded = ref(false)
const shipping = ref(0) // Esto podría venir de otro store

// Computed properties
const totalWithShipping = computed(() => {
  return totalPrice.value + shipping.value
})

const canCheckout = computed(() => {
  return !isEmpty.value && totalItems.value > 0
})

// Métodos
const toggleCart = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    refreshCart()
  }
}

const closeCart = () => {
  isOpen.value = false
}

const goToCart = () => {
  router.push('/cart')
  closeCart()
}

const goToCheckout = () => {
  router.push('/checkout')
  closeCart()
}

const goToProducts = () => {
  router.push('/productos')
  closeCart()
}

const handleUpdateQuantity = async (itemId, newQuantity) => {
  await updateQuantity(itemId, newQuantity, false) // Sin notificación
}

const handleRemoveItem = async (itemId) => {
  await removeFromCart(itemId, false) // Sin notificación
}

// Efecto de animación cuando se agrega un producto
const animateAddition = () => {
  justAdded.value = true
  setTimeout(() => {
    justAdded.value = false
  }, 1000)
}

// Cerrar dropdown al hacer clic fuera
const handleClickOutside = (event) => {
  if (isOpen.value && !event.target.closest('.cart-widget')) {
    closeCart()
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  refreshCart() // Cargar carrito al montar
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Watchers
watch(totalItems, (newCount, oldCount) => {
  if (newCount > oldCount && oldCount >= 0) {
    animateAddition()
  }
})
</script>

<style scoped>
.cart-widget {
  position: relative;
}

.cart-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: inherit;
}

.cart-button:hover,
.cart-button-active {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.3);
}

.cart-icon {
  position: relative;
  font-size: 1.25rem;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #e53e3e;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #7d1c2b;
}

.cart-badge-pulse {
  animation: pulse-badge 0.6s ease-in-out;
}

.cart-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
}

.cart-label {
  font-size: 0.875rem;
  font-weight: 500;
}

.cart-total {
  font-size: 0.75rem;
  opacity: 0.9;
  font-weight: 600;
}

/* Dropdown */
.cart-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 380px;
  max-width: 90vw;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f0f0f0;
  background: #f8f9fa;
}

.cart-header h3 {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #2d3748;
}

.close-cart {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #718096;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.close-cart:hover {
  color: #2d3748;
  background: rgba(0, 0, 0, 0.05);
}

.cart-body {
  max-height: 400px;
  overflow-y: auto;
}

/* Loading state */
.cart-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: #718096;
}

.loading-spinner {
  font-size: 1.5rem;
  color: #7d1c2b;
  margin-bottom: 0.5rem;
}

/* Empty state */
.cart-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

.empty-icon {
  font-size: 3rem;
  color: #e2e8f0;
  margin-bottom: 1rem;
}

.cart-empty h4 {
  margin: 0 0 0.5rem 0;
  color: #2d3748;
  font-weight: 600;
}

.cart-empty p {
  margin: 0 0 1.5rem 0;
  color: #718096;
  font-size: 0.875rem;
}

/* Cart items */
.cart-items {
  padding: 0.5rem 0;
}

/* Footer */
.cart-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid #f0f0f0;
  background: #f8f9fa;
}

.cart-summary {
  margin-bottom: 1rem;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.summary-total {
  font-weight: 700;
  font-size: 1rem;
  border-top: 1px solid #e2e8f0;
  padding-top: 0.5rem;
  margin-top: 0.5rem;
}

.total-price {
  color: #7d1c2b;
  font-size: 1.125rem;
}

.cart-actions {
  display: flex;
  gap: 0.75rem;
}

.cart-actions > * {
  flex: 1;
}

/* Overlay */
.cart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 999;
}

/* Transiciones */
.cart-dropdown-enter-active,
.cart-dropdown-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cart-dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.cart-dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

/* Animaciones */
@keyframes pulse-badge {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .cart-dropdown {
    width: 340px;
    right: -20px;
  }
  
  .cart-text {
    display: none;
  }
  
  .cart-button {
    padding: 0.75rem;
  }
}

@media (max-width: 480px) {
  .cart-dropdown {
    width: calc(100vw - 20px);
    right: -10px;
  }
  
  .cart-footer {
    padding: 0.75rem 1rem;
  }
  
  .cart-actions {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>
