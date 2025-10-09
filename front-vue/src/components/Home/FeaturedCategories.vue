<template>
  <section class="featured-categories">
    <h2 class="section-title">Categorías</h2>
    <hr class="section-divider" />
    <div class="slider-container">
      <button class="arrow left" @click="prevPage" :disabled="currentPage === 0">❮</button>
      <div class="category-grid">
        <div
          v-for="(category, index) in paginatedCategories"
          :key="index"
          class="card-overlay"
          @click="goToSearch(category.id, 'categoria')"   
               >
          <img :src="category.imagen" alt="imagen categoría" class="card-bg-img" />
          <div class="overlay"></div>
          <p class="card-text">{{ category.name }}</p>
        </div>
      </div>
      <button class="arrow right" @click="nextPage" :disabled="endIndex >= categories.length">❯</button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import http from '@/http'
import { useRouter } from 'vue-router'

const categories = ref([])
const router = useRouter()

const itemsPerPage = 4 // 2x2
const currentPage = ref(0)

const startIndex = computed(() => currentPage.value * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)

const paginatedCategories = computed(() =>
  categories.value.slice(startIndex.value, endIndex.value)
)

const nextPage = () => {
  if (endIndex.value < categories.value.length) currentPage.value++
}
const prevPage = () => {
  if (currentPage.value > 0) currentPage.value--
}

const goToSearch = (queryId, type) => {
  router.push({ 
    path: '/search', 
    query: { [type]: queryId }  
  })
}

onMounted(async () => {
  const res = await http.get('/categorias')
  categories.value = res.data
})
</script>

<style scoped>
@import '@/assets/css/SharedSliderStyles.css';

.category-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: repeat(2, 200px);
  gap: 24px;
  padding: 20px;
  width: 580px;
  justify-content: center;
}
</style>
