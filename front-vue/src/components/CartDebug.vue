<template>
  <div class="cart-debug">
    <h2>🛒 Debug del Carrito</h2>
    
    <div class="debug-section">
      <h3>Estado de Autenticación</h3>
      <div class="debug-info">
        <p><strong>Usuario autenticado:</strong> {{ isAuthenticated ? 'Sí' : 'No' }}</p>
        <p><strong>Token:</strong> {{ userStore.token ? 'Existe' : 'No existe' }}</p>
        <p><strong>Usuario:</strong> {{ userStore.user ? userStore.user.first_name || userStore.user.name : 'No hay usuario' }}</p>
      </div>
    </div>

    <div class="debug-section">
      <h3>Estado del Carrito</h3>
      <div class="debug-info">
        <p><strong>Items en carrito:</strong> {{ cartStore.items.length }}</p>
        <p><strong>Total items:</strong> {{ cartStore.totalItems }}</p>
        <p><strong>Total precio:</strong> Q{{ cartStore.totalPrice || cartStore.total }}</p>
        <p><strong>Cargando:</strong> {{ cartStore.loading ? 'Sí' : 'No' }}</p>
        <p><strong>Error:</strong> {{ cartStore.error || 'Ninguno' }}</p>
      </div>
      
      <div class="debug-actions">
        <button @click="fetchCart" :disabled="cartStore.loading">
          {{ cartStore.loading ? 'Cargando...' : 'Cargar Carrito' }}
        </button>
        <button @click="testAddProduct">Agregar Producto Test</button>
        <button @click="clearCartDebug">Limpiar Carrito</button>
      </div>
    </div>

    <div class="debug-section">
      <h3>Items del Carrito</h3>
      <div v-if="cartStore.items.length === 0" class="empty-cart">
        <p>No hay items en el carrito</p>
      </div>
      <div v-else class="cart-items-list">
        <div v-for="item in cartStore.items" :key="item.id" class="cart-item-debug">
          <p><strong>ID:</strong> {{ item.id }}</p>
          <p><strong>Producto ID:</strong> {{ item.producto_id }}</p>
          <p><strong>Nombre:</strong> {{ item.nombre }}</p>
          <p><strong>Cantidad:</strong> {{ item.cantidad }}</p>
          <p><strong>Precio:</strong> Q{{ item.precio_unitario }}</p>
          <p><strong>Subtotal:</strong> Q{{ item.subtotal }}</p>
        </div>
      </div>
    </div>

    <div class="debug-section">
      <h3>Test de Producto</h3>
      <div class="test-product">
        <AddToCartButton
          :product="testProduct"
          @added="onProductAdded"
          @error="onProductError"
        />
        
        <div v-if="lastMessage" class="debug-message" :class="`message-${lastMessage.type}`">
          {{ lastMessage.text }}
        </div>
      </div>
    </div>

    <div class="debug-section">
      <h3>Headers de Axios</h3>
      <div class="debug-info">
        <pre>{{ JSON.stringify(axiosHeaders, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useUserStore } from '@/stores/userStore'
import AddToCartButton from '@/components/AddToCartButton.vue'
import axios from 'axios'

const cartStore = useCartStore()
const userStore = useUserStore()

const lastMessage = ref(null)

const isAuthenticated = computed(() => userStore.isAuthenticated)

const axiosHeaders = computed(() => {
  return axios.defaults.headers.common
})

const testProduct = ref({
  id: 1,
  nombre: 'Producto Test',
  precio: 25.99,
  stock: 10,
  imagen: '/img/hero-banner.png'
})

const fetchCart = async () => {
  console.log('🔄 Fetching cart manually...')
  const result = await cartStore.fetchCart()
  console.log('Cart result:', result)
  
  lastMessage.value = {
    type: result.success ? 'success' : 'error',
    text: result.success ? 'Carrito cargado correctamente' : result.error
  }
}

const testAddProduct = async () => {
  console.log('🧪 Testing add product...')
  const result = await cartStore.addToCart(testProduct.value.id, 1, {
    id: testProduct.value.id,
    nombre: testProduct.value.nombre,
    precio: testProduct.value.precio,
    imagen: testProduct.value.imagen,
    stock: testProduct.value.stock
  })
  
  console.log('Add product result:', result)
  
  lastMessage.value = {
    type: result.success ? 'success' : 'error',
    text: result.success ? result.message : result.error
  }
}

const clearCartDebug = async () => {
  cartStore.items = []
  cartStore.total = 0
  cartStore.itemCount = 0
  
  lastMessage.value = {
    type: 'info',
    text: 'Carrito limpiado localmente'
  }
}

const onProductAdded = (data) => {
  console.log('Product added:', data)
  lastMessage.value = {
    type: 'success',
    text: `Producto agregado: ${data.product.nombre} (${data.quantity})`
  }
}

const onProductError = (error) => {
  console.error('Product error:', error)
  lastMessage.value = {
    type: 'error',
    text: error.message
  }
}

onMounted(() => {
  console.log('🚀 Cart Debug Component mounted')
  fetchCart()
})
</script>

<style scoped>
.cart-debug {
  max-width: 800px;
  margin: 20px auto;
  padding: 20px;
  font-family: monospace;
}

.debug-section {
  margin-bottom: 24px;
  padding: 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
}

.debug-section h3 {
  margin-top: 0;
  color: #333;
}

.debug-info p {
  margin: 4px 0;
  color: #555;
}

.debug-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
}

.debug-actions button {
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: white;
  cursor: pointer;
}

.debug-actions button:hover {
  background: #f0f0f0;
}

.debug-actions button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cart-item-debug {
  padding: 12px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: white;
}

.cart-item-debug p {
  margin: 2px 0;
  font-size: 12px;
}

.empty-cart {
  padding: 20px;
  text-align: center;
  color: #888;
  background: white;
  border-radius: 4px;
}

.test-product {
  padding: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: white;
}

.debug-message {
  margin-top: 12px;
  padding: 8px;
  border-radius: 4px;
  font-size: 14px;
}

.message-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.message-info {
  background: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

pre {
  background: white;
  padding: 12px;
  border-radius: 4px;
  font-size: 12px;
  overflow-x: auto;
}
</style>
