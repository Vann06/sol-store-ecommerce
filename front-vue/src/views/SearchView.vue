<template>
    <div class="search-container">
  <!-- Barra de búsqueda -->
  <div class="search-bar">
    <input 
      type="text" 
      v-model="searchQuery" 
      placeholder="Buscar productos de anime..." 
      @keyup.enter="performSearch"
    >
    <button @click="performSearch">
      <img src="/img/search-icon.svg" alt="Buscar" class="search-icon" />
    </button>
  </div>

  <!-- Contenido principal dividido -->
  <div class="search-main">
    <!-- Aside lateral -->
    <aside class="sidebar-filters">
      <h3>Categoría</h3>
      <div v-for="cat in categoriesList" :key="cat.id">
        <label><input type="checkbox" :value="cat.id" v-model="selectedCategories" @change="performSearch" /> {{ cat.name }}</label>
      </div>

      <h3>Temática</h3>
      <div v-for="theme in themesList" :key="theme.id">
        <label><input type="checkbox" :value="theme.id" v-model="selectedThemes" @change="performSearch" /> {{ theme.name }}</label>
      </div>
    </aside>

    <!-- Resultados y filtros -->
    <div class="search-results">
      <div class="top-filters">
        <select v-model="selectedAvailability" @change="performSearch">
          <option value="">Disponibilidad</option>
          <option value="activo">En Stock</option>
          <option value="inactivo">Agotado</option>
        </select>

        <select v-model="selectedOrder" @change="performSearch">
          <option value="">Ordenar por</option>
          <option value="price_asc">Precio: Menor a mayor</option>
          <option value="price_desc">Precio: Mayor a menor</option>
        </select>
      </div>

      <div v-if="isLoading" class="loading">
        <p>Cargando resultados...</p>
      </div>

      <div v-if="searchResults.length > 0">
        <div class="results-list">
          <div class="result-item" v-for="(item, index) in paginatedResults" :key="index">
            <div class="product-image-container">
              <img :src="item.imageUrl" :alt="item.name" class="product-image" @error="item.imageUrl = '/img/default-product.png'" />
            </div>
            <h3>{{ item.name }}</h3>
            <p class="category">{{ item.category }}</p>
            <p class="price">Q{{ item.price.toFixed(2) }}</p>
            <button class="Details" @click="detailsItem(item.id)">
              <img src="/img/plus.svg" alt="Detalles" class="icon-svg" /> Detalles
            </button>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button @click="prevPage" :disabled="currentPage === 1">Anterior</button>
        <span 
          v-for="page in totalPages" 
          :key="page"
          @click="goToPage(page)"
          :class="{ active: currentPage === page }"
        >
          {{ page }}
        </span>
        <button @click="nextPage" :disabled="currentPage === totalPages">Siguiente</button>
      </div>

      <div class="no-results" v-if="hasSearched && !isLoading && searchResults.length === 0">
        <p>No se encontraron resultados para "{{ searchQuery }}"</p>
        <img src="/img/no-results.svg" alt="Sin resultados" class="no-results-img">
      </div>
    </div>
  </div>
</div>
  </template>

<script setup>
import router from '@/router'
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import http from '@/http'

const route = useRoute()
const hasSearched = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('all')
const currentPage = ref(1)
const itemsPerPage = 3 * 3 
const isLoading = ref(false)
const allProducts = ref([])
const categoriesList = ref([])
const themesList = ref([])
const selectedCategories = ref([])
const selectedThemes = ref([])
const selectedAvailability = ref('')
const selectedOrder = ref('')

const filteredResults = computed(() => {
  let results = allProducts.value.filter(product =>
    product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    product.category.toLowerCase().includes(searchQuery.value.toLowerCase())
  )

  switch (selectedFilter.value) {
    case 'available':
      results = results.filter((_, index) => index % 2 === 0)
      break
    case 'price_asc':
      results.sort((a, b) => a.price - b.price)
      break
    case 'price_desc':
      results.sort((a, b) => b.price - a.price)
      break
  }

  return results
})

const totalPages = computed(() => Math.ceil(filteredResults.value.length / itemsPerPage))
const paginatedResults = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredResults.value.slice(start, start + itemsPerPage)
})

const searchResults = computed(() => {
  return filteredResults.value
})

async function performSearch() {
  isLoading.value = true
  try {
    const params = new URLSearchParams()

    params.append('search', searchQuery.value) 


    selectedCategories.value.forEach(catId => {
      params.append('categoria[]', catId)
    })


    selectedThemes.value.forEach(themeId => {
      params.append('tematica[]', themeId)
    })

    if (selectedAvailability.value) {
      params.append('estado', selectedAvailability.value)
    }

    if (selectedOrder.value) {
      params.append('orden', selectedOrder.value)
    }

    //const response = await fetch(`http://localhost:8000/api/productos?${params.toString()}`)
    const { data } = await http.get(`/productos?${params.toString()}`)
    //const data = await response.json()

    allProducts.value = data.map(product => ({
      name: product.nombre,
      category: product.category?.name || 'Sin categoría',
      theme: product.theme?.name || 'Sin temática',
      price: parseFloat(product.precio_base),
      id: product.id,
      imageUrl: product.imagen
    }))
  } catch (error) {
    console.error('Error al buscar productos:', error)
    allProducts.value = []
  } finally {
    hasSearched.value = true
    isLoading.value = false
    currentPage.value = 1
  }
}


function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}

function goToPage(page) {
  currentPage.value = page
}

function detailsItem(id) {
  router.push({name: 'product-detail', params: {id}})
}

watch(() => route.query, (query) => {
  if (query.q !== undefined) {
    searchQuery.value = query.q
  }

  if (query.categoria) {
    selectedCategories.value = Array.isArray(query.categoria)
      ? query.categoria
      : [query.categoria]
  }

  if (query.tematica) {
    selectedThemes.value = Array.isArray(query.tematica)
      ? query.tematica
      : [query.tematica]
  }

  performSearch()
}, { immediate: true })

onMounted(async () => {
  try {
    const [catRes, themeRes] = await Promise.all([
      http.get('/categorias'),
      http.get('/tematicas')
    ])
    categoriesList.value = await catRes.json()
    themesList.value = await themeRes.json()

    // 👉 Ejecutar búsqueda inicial para cargar todos los productos disponibles
    performSearch()
  } catch (err) {
    console.error('Error cargando filtros:', err)
  }
})

</script>

  
 
<style scoped>
.search-main {
  display: flex;
  gap: 30px;
  align-items: flex-start;
}
.search-results {
  flex: 1;
  width: 100%;
}

.sidebar-filters {
  width: 200px;
  padding: 10px;
  border-right: 2px solid #eee;
}

.sidebar-filters h3 {
  margin-bottom: 10px;
  color: #780116;
  font-weight: bold;
}

.sidebar-filters label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
}

.top-filters {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.no-results-img {
  display: block;
  margin: 20px auto 0 auto;
  max-width: 50px;
  height: 20px;
  opacity: 0.85;
  filter: grayscale(10%) contrast(110%);
}

.search-container {
  width: 100%;
  margin: 100px auto 40px auto; 
  padding: 100px 20px 40px 20px; 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: white;
  min-height: 80vh; 
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
  border-radius: 10px;
  box-sizing: border-box;
}
.search-bar {
  display: flex;
  margin-bottom: 30px;
  border: 2px solid #780116;
  border-radius: 30px;
  overflow: hidden;
  max-width: 600px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.search-bar input {
  flex: 1;
  padding: 12px 20px;
  border: none;
  outline: none;
  font-size: 16px;
  background: #f9f9f9;
}
.search-bar button {
  padding: 0 20px;
  background: #780116;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.search-bar button:hover {
  background: #5c0114;
}
.search-icon {
  width: 20px;
  height: 20px;
  filter: invert(1);
}
.search-results h2 {
  color: #333;
  margin-bottom: 15px;
  font-size: 24px;
  border-bottom: 2px solid #780116;
  padding-bottom: 10px;
  flex: 1;
}
.search-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
}
.filters {
  display: flex;
  align-items: center;
  gap: 20px;
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 10px;
}
.filter-group label {
  font-weight: 500;
  color: #780116;
}
.filter-group select {
  padding: 8px 15px;
  border-radius: 20px;
  border: 1px solid #780116;
  background: white;
  cursor: pointer;
}
.results-count {
  color: #666;
  font-weight: 500;
}
.results-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
 width: 100%
}


.result-item {
  background-color: #fff;
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 16px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.result-item img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

.result-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(120, 1, 22, 0.1);
  border-color: #780116;
}
.result-item h3 {
  color: #333;
  margin-bottom: 8px;
  font-size: 18px;
  min-height: 50px;
}
.result-item .category {
  color: #780116;
  font-size: 14px;
  margin-bottom: 10px;
  font-weight: 500;
}
.result-item .price {
  color: #333;
  font-weight: bold;
  font-size: 22px;
  margin: 15px 0;
}
.add-to-cart {
  width: 100%;
  padding: 10px;
  background: #780116;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.add-to-cart:hover {
  background: #5c0114;
}

.icon-svg {
  width: 20px;
  height: 20px;
  filter: invert(1);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-top: 40px;
}
.pagination button {
  padding: 10px 20px;
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s;
}
.pagination button:hover:not(:disabled) {
  background: #780116;
  color: white;
  border-color: #780116;
}
.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.pagination span {
  padding: 8px 15px;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.3s;
}
.pagination span:hover {
  background: #f0f0f0;
}
.pagination span.active {
  background: #780116;
  color: white;
}
.no-results {
  text-align: center;
  padding: 40px;
  color: #666;
  margin-top: 30px;
}
.no-results-img {
  max-width: 100%;
  margin-top: 20px;
  border-radius: 8px;
  opacity: 0.8;
}

.product-image-container {
  width: 100%;
  height: 180px;
  overflow: hidden;
  margin-bottom: 15px;
  border-radius: 4px;
  background-color: #f8f8f8;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-image:hover {
  transform: scale(1.05);
}
@media (max-width: 768px) {
  .search-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .filters {
    width: 100%;
    justify-content: space-between;
  }

  .results-list {
    grid-template-columns: 1fr;
  }

  .pagination {
    flex-wrap: wrap;
  }
}
.loading {
  text-align: center;
  margin: 40px 0;
  color: #780116;
}
button.Details {
  width: 100%;
  background-color: #8B0000;
  color: white;
  border: none;
  border-radius: 999px; /* bien redondo */
  padding: 10px 18px;
  font-weight: bold;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
}

button.Details:hover {
  background-color: #a70000;
}

button.Details .icon-svg {
  width: 16px;
  height: 16px;
  filter: invert(1);
}

</style>