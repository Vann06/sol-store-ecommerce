<template>
  <main class="page under-fixed-header" style="--header-height: 96px;">
    <section class="categories">
      <header class="categories__header">
        <h1>Categorías</h1>
        <p>Explora nuestras categorías y encuentra productos por tema.</p>
      </header>

      <div class="categories__grid" v-if="!loading && categories.length">
        <article
          v-for="cat in categories"
          :key="cat.id || cat.name"
          class="category-card"
          @click="goToCategory(cat)"
        >
          <div class="category-card__icon" aria-hidden="true">⚙️</div>
          <div class="category-card__body">
            <h2 class="category-card__title">{{ cat.name }}</h2>
            <p class="category-card__desc">Ver productos de {{ cat.name }}</p>
            <div class="category-card__chips">
              <span class="chip">Ver productos</span>
            </div>
          </div>
        </article>
      </div>

      <div class="state" v-else>
        <p v-if="loading">Cargando categorías...</p>
        <p v-else-if="error">{{ error }}</p>
        <p v-else>No se encontraron categorías.</p>
      </div>
    </section>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const categories = ref([])
const loading = ref(false)
const error = ref(null)

const fetchCategories = async () => {
  loading.value = true
  error.value = null
  try {
    // Referencia a la conexión usada en Catalogo.vue
    const res = await axios.get('http://localhost:8000/api/categorias')
    const arr = Array.isArray(res.data) ? res.data : []
    categories.value = arr.map(c => ({ id: c.id || c.name, name: c.name || c.nombre || 'Sin nombre' }))
  } catch (e) {
    console.error('Error cargando categorías', e)
    error.value = 'No fue posible cargar las categorías'
  } finally {
    loading.value = false
  }
}

function goToCategory(cat) {
  router.push({ name: 'catalogo', query: { category: cat.name } })
}

onMounted(fetchCategories)
</script>

<style scoped>
.page { background: #faf7f2; min-height: 100vh; }
.categories { max-width: 1200px; margin: 0 auto; padding: calc(var(--header-height) + 28px) 20px 48px; }
.categories__header { text-align: left; margin-bottom: 16px; }
.categories__header h1 { margin: 0 0 6px; color: #6f0f18; font-size: 28px; }
.categories__header p { margin: 0; color: #555; }

.categories__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 18px;
}

.category-card {
  cursor: pointer;
  background: linear-gradient(180deg, #ffffff, #fff8f5);
  border-radius: 16px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.08);
  border: 1px solid #f0e7e5;
  padding: 18px 16px 16px;
  transition: transform .15s ease, box-shadow .15s ease;
}
.category-card:hover { transform: translateY(-2px); box-shadow: 0 6px 28px rgba(0,0,0,0.12); }
.category-card__icon { width: 40px; height: 40px; display: grid; place-items: center; background: #f7e6e8; color: #8b1a1a; border-radius: 10px; font-size: 20px; margin-bottom: 12px; }
.category-card__title { margin: 0 0 4px; color: #6f0f18; font-size: 18px; }
.category-card__desc { margin: 0 0 10px; color: #666; font-size: 14px; }
.category-card__chips { display: flex; gap: 8px; flex-wrap: wrap; }
.chip { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; border: 1px solid #e4c7c7; color: #6f0f18; background: #fff; font-size: 12px; }

.state { padding: 40px 0; text-align: center; color: #555; }

@media (max-width: 600px) {
  .categories { padding: calc(var(--header-height) + 20px) 14px 28px; }
}
</style>
