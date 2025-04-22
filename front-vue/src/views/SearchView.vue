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
          <i class="fas fa-search"></i>
        </button>
      </div>

      <div v-if="isLoading" class="loading">
        <p>Cargando resultados...</p>
    </div>
  
      <!-- Resultados de búsqueda -->
      <div class="search-results" v-if="searchResults.length > 0">
        <h2>Resultados de búsqueda</h2>
        <div class="search-header">
          <p><strong>Búsqueda: {{ searchQuery }}</strong></p>
          
          <div class="filters">
            <div class="filter-group">
              <label>Filtrar:</label>
              <select v-model="selectedFilter">
                <option value="all">Todos</option>
                <option value="available">Disponibles</option>
                <option value="price_asc">Precio: Menor a mayor</option>
                <option value="price_desc">Precio: Mayor a menor</option>
              </select>
            </div>
            
            <div class="results-count">
              <p>{{ filteredResults.length }} resultados</p>
            </div>
          </div>
        </div>
  
        <!-- Lista de resultados -->
        <div class="results-list">
          <div 
            class="result-item" 
            v-for="(item, index) in paginatedResults" 
            :key="index"
          >
            <h3>{{ item.name }}</h3>
            <p class="category">{{ item.category }}</p>
            <p class="price">Q{{ item.price.toFixed(2) }}</p>
            <button class="add-to-cart" @click="addToCart(item)">
              <i class="fas fa-cart-plus"></i> Añadir
            </button>
          </div>
        </div>
  
        <!-- Paginación -->
        <div class="pagination">
          <button 
            @click="prevPage" 
            :disabled="currentPage === 1"
          >
            <i class="fas fa-chevron-left"></i> Anterior
          </button>
          
          <span 
            v-for="page in totalPages" 
            :key="page"
            @click="goToPage(page)"
            :class="{ active: currentPage === page }"
          >
            {{ page }}
          </span>
          
          <button 
            @click="nextPage" 
            :disabled="currentPage === totalPages"
          >
            Siguiente <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
  
      <!-- Mensaje cuando no hay resultados -->
      <div class="no-results" v-else-if="searchQuery && !isLoading">
        <p>No se encontraron resultados para "{{ searchQuery }}"</p>
        <img src="https://via.placeholder.com/300x200?text=No+results" alt="Sin resultados" class="no-results-img">
      </div>
    </div>
  </template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const searchQuery = ref('')
const selectedFilter = ref('all')
const currentPage = ref(1)
const itemsPerPage = 20
const isLoading = ref(false)
const allProducts = ref([])

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
  return searchQuery.value ? filteredResults.value : []
})

async function performSearch() {
  isLoading.value = true
  try {
    const query = searchQuery.value
    const response = await fetch(`http://localhost:8000/api/productos?search=${encodeURIComponent(query)}`)
    const data = await response.json()

    allProducts.value = data.map(product => ({
      name: product.nombre,
      category: product.category?.name || 'Sin categoría',
      price: parseFloat(product.precio_base),
      id: product.id
    }))
  } catch (error) {
    console.error('Error al buscar productos:', error)
    allProducts.value = []
  } finally {
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

function addToCart(item) {
  alert(`Añadido al carrito: ${item.name} - Q${item.price.toFixed(2)}`)
}

watch(() => route.query.q, (newQuery) => {
  if (newQuery !== undefined) {
    searchQuery.value = newQuery
    performSearch()
  }
}, { immediate: true })
</script>

  
  
  <style scoped>
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
    padding: 0 25px;
    background: #780116;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
  }
  
  .search-bar button:hover {
    background: #5c0114;
  }
  
  .search-results h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 24px;
    border-bottom: 2px solid #780116;
    padding-bottom: 10px;
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
    margin-bottom: 40px;
  }
  
  .result-item {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
    background: white;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
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

  </style>