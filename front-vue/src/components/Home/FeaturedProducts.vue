<template>
  <section class="featured-products">
    <h2 class="section-title">Productos Nuevos</h2>
    <hr class="section-divider" />
    <div class="slider-container">
      <button class="arrow left" @click="scrollLeft">❮</button>
      <div class="slider-grid" ref="productGrid">
        <div class="grid-wrapper">
          <div
            v-for="product in products"
            :key="product.id"
            class="product-card"
            @click="goToDetail(product.id)"
          >
            <img :src="product.imagen" class="product-img" />
            <div class="product-details">
              <p class="product-name">{{ product.nombre }}</p>
              <p class="product-price">Q{{ Number(product.precio_base).toFixed(2) }}</p>
              <button class="add-to-cart" @click.stop="addToCart(product)">
                <img src="/img/plus.svg" alt="+" class="icon-svg" /> Añadir al carrito
              </button>
            </div>
          </div>
        </div>
      </div>
      <button class="arrow right" @click="scrollRight">❯</button>
    </div>
  </section>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import http from '@/http'
import { useRouter } from 'vue-router'

const products = ref([])
const productGrid = ref(null)
const router = useRouter()

const scrollLeft = () => productGrid.value.scrollLeft -= 300
const scrollRight = () => productGrid.value.scrollLeft += 300

const goToDetail = (id) => router.push({ name: 'product-detail', params: { id } })
const addToCart = (product) => console.log('Añadido al carrito:', product)

onMounted(async () => {
  const res = await http.get('/productos/recientes')
  products.value = res.data
    .filter(p => p.status === 'activo')
    .slice(0, 12)
})
</script>

<style scoped>
@import '@/assets/css/SharedSliderStyles.css';

.product-card {
  width: 220px; /* más angosto */
  background: white;
  border-radius: 20px;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-bottom: 16px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

.product-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.product-details {
  text-align: center;
  padding: 10px;
}

.product-name {
  font-weight: 700;
  font-size: 16px;
  margin-top: 10px;
  color: #333;
}

.product-price {
  color: #8B0000;
  margin-top: 2px;
  font-weight: 500;
}

.add-to-cart {
  margin-top: 10px;
  padding: 10px 12px;
  background-color: #8B0000;
  color: white;
  border: none;
  border-radius: 999px; /* círculo */
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
}

.add-to-cart:hover {
  background-color: #a30000;
  transform: scale(1.05);
}
.slider-grid {
  overflow-x: auto;
  scroll-behavior: smooth;
  max-width: 1000px;
  padding: 10px;
}

.grid-wrapper {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  grid-auto-rows: auto;
  gap: 28px;
  grid-template-rows: repeat(2, auto); /* 2 filas */
  width: max-content;
  min-width: 900px;
}

</style>
