<template>
  <main class="product-page under-fixed-header bg-page-soft">
    <div v-if="product" class="container">
      <PageHeader
        :title="product.name"
        :subtitle="product.category ? `Categoría: ${product.category}` : ''"
        icon="fas fa-tag"
      />

      <section class="product-grid">
        <div class="gallery gradient-card">
          <img class="main-image" :src="product.imagen_url || fallbackImg" :alt="product.name" @error="onImgError" />
        </div>

        <div class="details gradient-card">
          <h1 class="title">{{ product.name }}</h1>
          <div class="price">{{ formatCurrency(product.price) }}</div>

          <div class="qty">
            <span class="label">Cantidad: <strong>{{ cantidad }}</strong></span>
            <input
              class="qty-slider"
              type="range"
              min="1"
              :max="Math.max(1, Number(product.stock) || 10)"
              v-model.number="cantidad"
            />
          </div>

          <BaseButton variant="primary" size="large" block @click="agregarAlCarrito">
            <i class="fas fa-cart-plus"></i>
            Añadir al carrito
          </BaseButton>
          <p class="shipping">ENVÍO GRATIS EN PEDIDOS SUPERIORES A Q100.*</p>
          <div class="quick-details">
            <ul>
              <li><strong>Categoría:</strong> {{ product.category }}</li>
              <li><strong>Temática:</strong> {{ product.theme }}</li>
              <li><strong>Stock:</strong> {{ product.stock }}</li>
            </ul>
          </div>
        </div>
      </section>

      <section class="desc gradient-card">
        <h2>Descripción</h2>
        <p>{{ product.descripcion }}</p>
      </section>
    </div>

    <div v-else class="container">
      <PageHeader title="Cargando producto" subtitle="Por favor espera" icon="fas fa-spinner fa-spin" />
    </div>

    <!-- Sticky actions on mobile (no duplicate add-to-cart) -->
    <div v-if="product" class="mobile-sticky">
      <div class="qty-compact">
        <span class="label">Cantidad: <strong>{{ cantidad }}</strong></span>
        <input
          class="qty-slider"
          type="range"
          min="1"
          :max="Math.max(1, Number(product.stock) || 10)"
          v-model.number="cantidad"
        />
      </div>
    </div>
  </main>
</template>
  
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '../stores/cart'
import http from '@/http'
import PageHeader from '@/components/PageHeader.vue'
import BaseButton from '@/components/BaseButton.vue'
const fallbackImg = 'https://via.placeholder.com/800x600?text=Producto'
const route = useRoute()
const product = ref(null)
const cantidad = ref(1)
const cart = useCartStore()


onMounted(async () => {
  const id = route.params.id
    try {
        const { data: productData } = await http.get(`/productos/${id}`)

	product.value = {
	  ...productData,
          name: productData.nombre ?? productData.name ?? 'Producto',
	  price: productData.precio_base != null
	    ? Number(productData.precio_base)
	    : (productData.price ?? 0),
	  category: productData.category?.name
	    ?? productData.categoria?.name
	    ?? 'Sin categoría',
          imagen_url: productData.imagen	
	    ?? productData.image
	    ?? productData.image_url
	    ?? 'https://via.placeholder.com/500x600',
      }						
    } catch (error) {													    
      console.error('Error al cargar producto:', error)													      
    }
})

//onMounted(async () => {
//  const id = route.params.id
//  try {
    //ec const response = await fetch(`http://localhost:8000/api/productos/${id}`)
//    const { data: productData } = await http.get(`/productos/${id}`)
 //   const data = await response.json()
//    product.value = {
//      ...productData,
//      name: productData.nombre,
//      price: parseFloat(productData.precio_base),
//      category: productData.category?.name || 'Sin categoría',
//      imagen_url: productData.imagen || 'https://via.placeholder.com/500x600'
//    }
//    } catch (error) {
//        console.error('Error al cargar producto:', error)
//    }
//})

function aumentarCantidad() {
  cantidad.value++
}

function disminuirCantidad() {
  if (cantidad.value > 1) cantidad.value--
}

function formatCurrency(n){
  const num = Number(n || 0)
  return num.toLocaleString('es-GT',{style:'currency',currency:'GTQ',minimumFractionDigits:2,maximumFractionDigits:2})
}

async function agregarAlCarrito() {
  if (!product.value) return
  await cart.addToCart(product.value.id, cantidad.value)
  // Opcional: notificación o feedback
  alert('Producto agregado al carrito')
}

function onImgError(e){ e.target.src = fallbackImg }
function increase(){ cantidad.value++ }
function decrease(){ if(cantidad.value>1) cantidad.value-- }
</script>
  
<style scoped>
  .product-page { min-height: 100vh; }
  .container { max-width: 1200px; margin: 0 auto; padding: calc(var(--header-height) + 24px) 16px 24px; }
  .product-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 24px; }
  .gradient-card { background: linear-gradient(#fff, #fff) padding-box, linear-gradient(135deg, rgba(240,192,64,0.35), rgba(122,0,25,0.3)) border-box; border: 1px solid transparent; border-radius: 16px; box-shadow: 0 10px 30px rgba(31,41,55,0.06); }
  .gallery { padding: 16px; display: grid; place-items: center; }
  .gallery img { width: 66%; height: auto; border-radius: 12px; }
  .gallery img.main-image { width: 66%; }
  .details { padding: 16px; display: grid; gap: 16px; align-content: start; }
  .title { margin: 0; color: var(--ink-1); font-size: 28px; }
  .price { color: var(--brand); font-weight: 800; font-size: 22px; }
  .qty { display: grid; gap: 8px; }
  .qty .label { color: var(--ink-2); font-weight: 700; }
  .qty-controls { display: inline-flex; align-items: center; border: 1px solid var(--ink-5); border-radius: 999px; padding: 2px; background: #fafafa; }
  .qty-btn { appearance: none; background: #fff; border: 0; padding: 8px 14px; border-radius: 999px; cursor: pointer; font-size: 18px; line-height: 1; }
  .qty-btn:hover { background: #f3f4f6; }
  .qty-btn:disabled { opacity: .5; }
  .qty .n { display: inline-block; min-width: 24px; text-align: center; font-weight: 700; }
  .qty-slider { width: 100%; accent-color: var(--brand-strong); }
  .shipping { color: var(--ink-3); font-size: 14px; }
  .desc { margin-top: 16px; padding: 16px; }
  .desc h2 { margin: 0 0 8px; color: var(--ink-1); }
  .desc .meta { margin: 12px 0 0; padding-left: 18px; color: var(--ink-2); }
  .desc .meta li { margin: 6px 0; }
  @media (max-width: 900px){ .product-grid { grid-template-columns: 1fr; } }

  /* Sticky mobile action bar */
  @media (max-width: 720px) {
    .product-page { padding-bottom: 76px; }
    .details { padding-bottom: 90px; }
    .mobile-sticky {
      position: fixed; bottom: 0; left: 0; right: 0; z-index: 40;
      background: #fff; border-top: 1px solid var(--ink-5);
      padding: 10px 12px; display: grid; grid-template-columns: 1fr; gap: 10px;
      box-shadow: 0 -8px 20px rgba(0,0,0,0.08);
    }
  .mobile-sticky .qty-compact { display: grid; gap: 6px; align-items: center; }
  }

  .quick-details ul { list-style: none; padding: 0; margin: 8px 0 0; color: var(--ink-2); }
  .quick-details li { margin: 4px 0; }
</style>