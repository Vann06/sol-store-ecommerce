<template>
  <div class="featured-products" role="region" aria-label="Productos nuevos">
    <div class="grid">
      <div v-for="product in products" :key="product.id" class="grid-item" @click="onItemClick(product.id)">
        <ProductCard :product="normalize(product)" :minimal="true" />
      </div>
    </div>

    <QuickViewModal 
      v-if="quickViewOpen"
      :product="quickViewProduct || {}" 
      :visible="quickViewOpen" 
      @close="closeQuickView" 
      @add-to-cart="addFromQuickView"
    />
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import http from '@/http'
import { useRouter } from 'vue-router'
import ProductCard from '@/components/ProductCard.vue'
import QuickViewModal from '@/components/QuickViewModal.vue'
import { useCartStore } from '@/stores/cart'

const products = ref([])
const quickViewOpen = ref(false)
const quickViewProduct = ref(null)
const track = ref(null)
const router = useRouter()
const cart = useCartStore()

// Dejar scroll handlers definidos pero no usados si cambiamos a grid
const scrollBy = () => {}

// Click handling that respects drag state
const goToDetail = (id) => router.push({ name: 'product-detail', params: { id } })
const onItemClick = (id) => {
  if (drag.moved) return
  goToDetail(id)
}
const addToCart = (product) => console.log('Añadido al carrito:', product)

const openQuickView = (product) => {
  quickViewProduct.value = product
  quickViewOpen.value = true
}
const closeQuickView = () => { quickViewOpen.value = false }
const addFromQuickView = async ({ product, quantity }) => {
  try {
    await cart.addToCart(product.id, quantity, product)
  } finally {
    quickViewOpen.value = false
  }
}

// Normalizador mínimo para ProductCard (nombre, precio/stock/imagen)
const normalize = (p) => ({
  id: p.id,
  nombre: p.nombre ?? p.name ?? 'Producto',
  precio: Number(p.precio_base ?? p.precio ?? 0),
  precio_original: p.precio_original,
  descuento: p.descuento,
  imagen: p.imagen ?? p.image ?? p.image_url ?? '/img/no-image.png',
  stock: p.stock ?? 0,
})

onMounted(async () => {
  const res = await http.get('/productos/recientes')
  products.value = res.data
    .filter(p => p.status === 'activo')
    .slice(0, 12)
})

// Pointer drag/swipe support
const drag = {
  active: false,
  startX: 0,
  startLeft: 0,
  moved: false,
}

const onPointerDown = (e) => {
  if (!track.value) return
  drag.active = true
  drag.moved = false
  drag.startX = e.clientX
  drag.startLeft = track.value.scrollLeft
  track.value.classList.add('dragging')
}

const onPointerMove = (e) => {
  if (!drag.active || !track.value) return
  const dx = e.clientX - drag.startX
  if (Math.abs(dx) > 4) drag.moved = true
  track.value.scrollLeft = drag.startLeft - dx
}

const onPointerUp = () => {
  drag.active = false
  setTimeout(() => { drag.moved = false }, 0)
  if (track.value) track.value.classList.remove('dragging')
}
</script>

<style scoped>
/* Grid layout: 4 per row on desktop */
.grid { display: grid; gap: 12px; grid-template-columns: repeat(4, 1fr); }
.grid-item { width: 100%; }
@media (max-width: 1024px) { .grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .grid { grid-template-columns: 1fr; } }

</style>
