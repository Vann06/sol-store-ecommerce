<template>
  <div class="featured-categories" role="region" aria-label="Categorías">
    <div class="carousel">
  <button class="nav prev" @click="prevPage" :disabled="currentPage === 0" aria-label="Ver anteriores">❮</button>
      <div class="grid">
        <div
          v-for="(category, index) in paginatedCategories"
          :key="index"
          class="tile"
          @click="goToCatalog(category)"
        >
          <Card type="outlined" class="tile-card">
            <div class="media"><img :src="category.imagen" :alt="category.name" /></div>
            <div class="label">{{ category.name }}</div>
          </Card>
        </div>
      </div>
  <button class="nav next" @click="nextPage" :disabled="endIndex >= categories.length" aria-label="Ver siguientes">❯</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import http from '@/http'
import { useRouter } from 'vue-router'
import Card from '@/components/ui/Card.vue'

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

const goToCatalog = (category) => {
  const q = category?.name || ''
  router.push({ name: 'catalogo', query: q ? { q } : {} })
}

onMounted(async () => {
  const res = await http.get('/categorias')
  categories.value = res.data
})
</script>

<style scoped>
/* Grid with navigation */
.carousel { display: grid; grid-template-columns: auto 1fr auto; gap: 12px; align-items: center; }
.grid { display: grid; grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, 133px); gap: 16px; padding: 8px; max-width: 640px; margin: 0 auto; }
.nav { appearance: none; background: linear-gradient(135deg, var(--brand-strong), var(--brand)); color: #fff; border: 0; width: 36px; height: 36px; border-radius: 999px; cursor: pointer; box-shadow: 0 8px 16px rgba(122,0,25,0.25); }
.nav:disabled { opacity: .4; cursor: not-allowed; }

.tile-card { height: 100%; display: grid; grid-template-rows: 1fr auto; }
.media { display: grid; place-items: center; overflow: hidden; border-radius: 12px; background: #fff; }
.media img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
.label { text-align: center; font-weight: 800; padding: 10px; color: var(--ink-1); }

@media (max-width: 640px) {
  .grid { grid-template-columns: 1fr 1fr; grid-template-rows: repeat(2, 107px); gap: 12px; }
  .nav { width: 44px; height: 44px; }
}
</style>
