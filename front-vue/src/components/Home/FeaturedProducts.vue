<template>
  <div class="featured-products" role="region" aria-label="Productos nuevos">
    <div class="carousel">
      <button class="nav prev" @click="scrollBy(-1)" aria-label="Ver anteriores">❮</button>
      <div
        class="track"
        ref="track"
        tabindex="0"
        @pointerdown="onPointerDown"
        @pointermove="onPointerMove"
        @pointerup="onPointerUp"
        @pointercancel="onPointerUp"
        @pointerleave="onPointerUp"
        @keydown.left.prevent="scrollBy(-1)"
        @keydown.right.prevent="scrollBy(1)"
      >
        <div v-for="product in products" :key="product.id" class="item" @click="onItemClick(product.id)">
          <ProductCard :product="normalize(product)" @quick-view="openQuickView" />
        </div>
      </div>
      <button class="nav next" @click="scrollBy(1)" aria-label="Ver siguientes">❯</button>
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

const CARD_WIDTH = 200 // includes gap approx, reduced ~23%
const scrollBy = (dir) => {
  if (!track.value) return
  track.value.scrollBy({ left: dir * CARD_WIDTH * 2, behavior: 'smooth' })
}

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
/* Carousel layout with snapping */
.carousel { position: relative; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 8px; }
.track { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(200px, 1fr); gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; padding: 8px; touch-action: pan-x; cursor: grab; }
.track.dragging { cursor: grabbing; }
.item { scroll-snap-align: start; width: 100%; }
.nav { appearance: none; background: linear-gradient(135deg, var(--brand-strong), var(--brand)); color: #fff; border: 0; width: 36px; height: 36px; border-radius: 999px; cursor: pointer; box-shadow: 0 8px 16px rgba(122,0,25,0.25); }
.nav:disabled { opacity: .4; cursor: not-allowed; }

@media (max-width: 640px) {
  .nav { width: 44px; height: 44px; }
  .track { grid-auto-columns: 80%; padding: 8px 4px; }
}

</style>
