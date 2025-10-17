<template>
    <div class="pagina-ecommerce" v-if="product">
      <ProductDetailStyles />
      <ProductPromoBanner />
      <div class="migas-pan">Inicio &gt; {{ product.name }}</div>
      <div class="contenedor-producto">
        <ProductGallery :image="product.imagen_url || 'https://via.placeholder.com/500x600'" :alt="product.name" />
        <ProductInfo :product="product" :cantidad="cantidad" @increase="cantidad++" @decrease="cantidad > 1 && cantidad--" @add="agregarAlCarrito" />
      </div>
      <ProductDescription :product="product" />
    </div>
  
    <div v-else class="loading">
      <p>Cargando producto...</p>
    </div>
</template>
  
<script setup>

import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '../stores/cart'
import ProductPromoBanner from '../components/ProductDetail/ProductPromoBanner.vue'
import ProductGallery from '../components/ProductDetail/ProductGallery.vue'
import ProductInfo from '../components/ProductDetail/ProductInfo.vue'
import ProductDescription from '../components/ProductDetail/ProductDescription.vue'
import ProductDetailStyles from '../components/ProductDetail/ProductDetailStyles.vue'

const route = useRoute()
const product = ref(null)
const cantidad = ref(1)
const cart = useCartStore()

onMounted(async () => {
  const id = route.params.id
  try {
    const response = await fetch(`http://localhost:8000/api/productos/${id}`)
    const data = await response.json()
    product.value = {
      ...data,
      name: data.nombre,
      price: Number.parseFloat(data.precio_base) || 0,
      category: data.category?.name || data.categoria?.name || 'Sin categoría',
      theme: data.theme?.name || data.tematica?.name || null,
      stock: data.stock ?? data.inventario?.stock ?? 0,
      imagen_url: data.imagen || 'https://via.placeholder.com/500x600'
    }
    } catch (error) {
        console.error('Error al cargar producto:', error)
    }
})
function aumentarCantidad() {
  cantidad.value++
}
function disminuirCantidad() {
  if (cantidad.value > 1) cantidad.value--
}
async function agregarAlCarrito() {
  if (!product.value) return
  await cart.addToCart(product.value.id, cantidad.value)
  alert('Producto agregado al carrito')
}
function formatPrice(value) {
  const n = Number(value) || 0
  return `Q${n.toFixed(2)}`
}
</script>
