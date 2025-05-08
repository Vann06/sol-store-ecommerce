<template>
  <section>
    <h2 class="section-title">Productos Nuevos</h2>
    <div class="slider-container">
      <button class="arrow" @click="scrollLeft">❮</button>
      <div class="slider" ref="slider">
        <div
          v-for="(product, index) in products"
          :key="index"
          class="product-card"
        >
          <div class="glow"></div>
          <img :src="`/${product.imagen}`" :alt="product.nombre" class="card-img" />
          <p class="card-title">{{ product.nombre }}</p>
        </div>
      </div>
      <button class="arrow" @click="scrollRight">❯</button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const slider = ref(null)

const scrollLeft = () => slider.value.scrollLeft -= 200
const scrollRight = () => slider.value.scrollLeft += 200

onMounted(async () => {
  const res = await axios.get('/api/productos/recientes')
  products.value = res.data
  slider.value.addEventListener('wheel', e => {
    e.preventDefault()
    slider.value.scrollLeft += e.deltaY
  })
})
</script>

<style scoped>
@import '@/assets/css/SharedSliderStyles.css';
</style>
