<template>
  <section>
    <h2 class="section-title">Categorías</h2>
    <div class="slider-container">
      <button class="arrow" @click="scrollLeft">❮</button>
      <div class="slider" ref="slider">
        <div
          v-for="(category, index) in categories"
          :key="index"
          class="card"
          @click="goToSearch(category.name)"
        >
          <div class="glow"></div>
          <img :src="category.imagen" :alt="category.name" class="card-img" />

          <p class="card-title">{{ category.name }}</p>
        </div>
      </div>
      <button class="arrow" @click="scrollRight">❯</button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const categories = ref([])
const slider = ref(null)
const router = useRouter()

const scrollLeft = () => slider.value.scrollLeft -= 200
const scrollRight = () => slider.value.scrollLeft += 200
const goToSearch = (query) => router.push({ path: '/search', query: { q: query } })

onMounted(async () => {
  const res = await axios.get('/api/categorias')
  categories.value = res.data
  slider.value.addEventListener('wheel', e => {
    e.preventDefault()
    slider.value.scrollLeft += e.deltaY
  })
})


</script>

<style scoped>
@import '@/assets/css/SharedSliderStyles.css';
</style>
