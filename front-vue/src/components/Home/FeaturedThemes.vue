<template>
  <section class="featured-themes">
    <h2 class="section-title">Temáticas</h2>
    <hr class="section-divider" />
    <div class="slider-container">
      <button class="arrow left" @click="scrollLeft">❮</button>
      <div class="themes-wrapper" ref="themeGrid">
        <div
          v-for="(theme, index) in themes"
          :key="index"
          class="card-overlay"
          @click="goToSearch(theme.id, 'tematica')"    
              >
          <img :src="theme.imagen" class="card-bg-img" />
          <div class="overlay"></div>
          <p class="card-text">{{ theme.name }}</p>
        </div>
      </div>
      <button class="arrow right" @click="scrollRight">❯</button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const themes = ref([])
const themeGrid = ref(null)
const router = useRouter()

const goToSearch = (queryId, type) => {
  router.push({ 
    path: '/search', 
    query: { [type]: queryId }  
  })
}
const scrollLeft = () => themeGrid.value.scrollLeft -= 300
const scrollRight = () => themeGrid.value.scrollRight += 300

onMounted(async () => {
  const res = await axios.get('/api/tematicas')
  themes.value = res.data.slice(0, 6)
})
</script>

<style scoped>
@import '@/assets/css/SharedSliderStyles.css';

.themes-wrapper {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 20px;
}
</style>
