<template>
  <main class="catalog-page under-fixed-header bg-page-soft">

    <CatalogHero />

    <!-- Shell con dos columnas: filtros izquierda, resultados derecha -->
    <div class="catalog-shell gradient-outline">
      <div class="filters-col">
        <CatalogFilters 
          :categories="categories"
          :selected-categories="selectedCategories"
          :selected-status="selectedStatus"
          :selected-complexity="selectedComplexity"
          :price-range="priceRange"
          :products="products"
          :quick-filters="quickFilters"
          :complexity-levels="complexityLevels"
          @clear-filters="clearFilters"
          @apply-quick-filter="applyQuickFilter"
          @update:selected-categories="selectedCategories = $event"
          @update:selected-status="selectedStatus = $event"
          @update:selected-complexity="selectedComplexity = $event"
          @update:price-range="priceRange = $event"
        />
      </div>

      <div class="results-col">
        <!-- Buscador local del catálogo -->
        <div class="catalog-search">
          <input
            type="text"
            v-model="localSearch"
            @keyup.enter="applySearch"
            placeholder="Buscar en catálogo (nombre, descripción, categoría, precio)"
            aria-label="Buscar en catálogo"
          />
          <button class="search-btn" @click="applySearch" title="Buscar">
            <i class="fas fa-search"></i>
          </button>
        </div>
        <CatalogResults 
          :filtered-products="filteredProducts"
          :loading="loading"
          :error="error"
          :sort-by="sortBy"
          @view-product="viewProductDetail"
          @add-to-cart="addToCart"
          @clear-filters="clearFilters"
          @update:sort-by="sortBy = $event"
        />
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
//import axios from 'axios'
import http from '@/http'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'

// Componentes
import CatalogHero from '@/components/Catalog/CatalogHero.vue'
import CatalogFilters from '@/components/Catalog/CatalogFilters.vue'
import CatalogResults from '@/components/Catalog/CatalogResults.vue'

// Composables
const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()

// State
const selectedCategories = ref([])
const selectedComplexity = ref(null)
const selectedStatus = ref([])
const priceRange = ref({ min: null, max: null })
const sortBy = ref('name')
const products = ref([])
const categories = ref([])
const loading = ref(false)
const error = ref(null)

// Search state
const localSearch = ref('')

// Data constants
const quickFilters = [
  { id: 'under-100', label: 'Menos de Q100', min: 0, max: 100 },
  { id: '100-300', label: 'Q100 - Q300', min: 100, max: 300 },
  { id: '300-500', label: 'Q300 - Q500', min: 300, max: 500 },
  { id: 'over-500', label: 'Más de Q500', min: 500, max: null }
]

const complexityLevels = [
  { value: 'low', label: 'Básico' },
  { value: 'medium', label: 'Intermedio' },
  { value: 'high', label: 'Avanzado' },
  { value: 'expert', label: 'Experto' }
]

// Computed properties
const normalized = (s) => (s || '').toString().normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase()
const parsePriceQuery = (q) => {
  const qClean = q.replace(/gtq|q/gi, '').trim()
  const rangeMatch = qClean.match(/^(\d+)\s*-\s*(\d+)$/)
  if (rangeMatch) {
    const min = Number(rangeMatch[1])
    const max = Number(rangeMatch[2])
    if (!isNaN(min) && !isNaN(max)) return { min, max }
  }
  const gte = qClean.match(/^[>≥]\s*(\d+)$/)
  if (gte) {
    const min = Number(gte[1])
    if (!isNaN(min)) return { min, max: null }
  }
  const lte = qClean.match(/^[<≤]\s*(\d+)$/)
  if (lte) {
    const max = Number(lte[1])
    if (!isNaN(max)) return { min: null, max }
  }
  const exact = qClean.match(/^(\d+)$/)
  if (exact) {
    const center = Number(exact[1])
    if (!isNaN(center)) {
      const delta = Math.max(20, Math.round(center * 0.2))
      return { min: Math.max(0, center - delta), max: center + delta }
    }
  }
  return null
}

const filteredProducts = computed(() => {
  let filtered = products.value
  const q = localSearch.value.trim()
  if (q) {
    const qn = normalized(q)
    const priceHint = parsePriceQuery(q)
    filtered = filtered.filter(product => {
      const name = normalized(product.nombre || product.name)
      const desc = normalized(product.descripcion || product.description)
      const cat = normalized(product.categoria?.name || product.categoria?.nombre || product.category)
      const textMatch = name.includes(qn) || desc.includes(qn) || cat.includes(qn)
      if (priceHint) {
        const price = Number(product.precio_base || product.price || 0)
        const minOk = priceHint.min == null || price >= priceHint.min
        const maxOk = priceHint.max == null || price <= priceHint.max
        return textMatch || (minOk && maxOk)
      }
      return textMatch
    })
  }
  if (selectedCategories.value.length > 0) {
    filtered = filtered.filter(product => {
      const productCategory = product.categoria?.name || product.categoria?.nombre || product.category || 'Sin categoría'
      return selectedCategories.value.some(selectedCat => 
        productCategory.toLowerCase().includes(selectedCat.toLowerCase()) ||
        selectedCat.toLowerCase().includes(productCategory.toLowerCase())
      )
    })
  }
  if (selectedComplexity.value) {
    filtered = filtered.filter(product => product.complexity === selectedComplexity.value)
  }
  if (selectedStatus.value.length > 0) {
    filtered = filtered.filter(product => selectedStatus.value.includes((product.status || '').toString().trim().toLowerCase()))
  }
  if (priceRange.value.min !== null) {
    filtered = filtered.filter(product => (product.precio_base || product.price || 0) >= priceRange.value.min)
  }
  if (priceRange.value.max !== null) {
    filtered = filtered.filter(product => (product.precio_base || product.price || 0) <= priceRange.value.max)
  }
  return applySorting(filtered)
})

// Methods
const fetchCategories = async () => {
  try {
    console.log('Cargando categorías desde API...')
    //ec const response = await axios.get('http://localhost:8000/api/categorias')
    const response = await http.get('/categorias') 
    console.log('Respuesta API categorías:', response.data)
    
    if (Array.isArray(response.data) && response.data.length > 0) {
      categories.value = response.data.map(cat => ({
        id: cat.name || cat.id,
        name: cat.name
      }))
      console.log('Categorías cargadas desde API:', categories.value)
    } else {
      console.log('No hay categorías en API, usando predefinidas')
      setDefaultCategories()
    }
  } catch (err) {
    console.error('Error al cargar categorías:', err)
    console.log('Usando categorías predefinidas como fallback')
    setDefaultCategories()
  }
}

const setDefaultCategories = () => {
  categories.value = [
    { id: 'Coleccion', name: 'Colección' },
    { id: 'anime', name: 'Anime' },
    { id: 'cosplay', name: 'Cosplay' },
    { id: 'combos', name: 'Combos' },
    { id: 'mochilas', name: 'Mochilas' },
    { id: 'adornos', name: 'Adornos' }
  ]
}

const fetchProducts = async () => {
  loading.value = true
  try {
    console.log('Iniciando carga de productos...')
    
    //ec const response = await axios.get('http://localhost:8000/api/productos')
    const response = await http.get('/productos')
    console.log('Respuesta de API productos:', response.data)
    
    if (Array.isArray(response.data) && response.data.length > 0) {
      // Mapear productos con estructura consistente incluyendo categoría completa
      products.value = response.data.map(p => ({
        id: p.id,
        nombre: p.nombre || p.name || 'Producto sin nombre',
        precio_base: p.precio_base || p.price || 0,
        imagen: p.imagen || p.image || '',
        descripcion: p.descripcion || p.description || '',
        categoria: p.category || p.categoria || { name: 'Sin categoría' },
        status: p.status || 'activo',
        id_categoria: p.id_categoria
      }))
      console.log('Productos cargados:', products.value.length)
    } else {
      products.value = []
      console.log('No hay productos disponibles')
    }
    
    console.log('Productos finales:', products.value)
    
  } catch (err) {
    console.error('Error al cargar productos:', err)
    error.value = 'Error al cargar productos desde la API'
    products.value = []
  } finally {
    loading.value = false
  }
}

const applySorting = (productsList) => {
  const sorted = productsList ? [...productsList] : [...filteredProducts.value]
  switch (sortBy.value) {
    case 'name': 
      return sorted.sort((a, b) => (a.nombre || a.name || '').localeCompare(b.nombre || b.name || ''))
    case 'price-asc': 
      return sorted.sort((a, b) => (a.precio_base || a.price || 0) - (b.precio_base || b.price || 0))
    case 'price-desc': 
      return sorted.sort((a, b) => (b.precio_base || b.price || 0) - (a.precio_base || a.price || 0))
    case 'complexity':
      const order = { 'low': 1, 'medium': 2, 'high': 3, 'expert': 4 }
      return sorted.sort((a, b) => (order[a.complexity] || 0) - (order[b.complexity] || 0))
    default: return sorted
  }
}

const clearFilters = () => {
  selectedCategories.value = []
  selectedComplexity.value = null
  priceRange.value = { min: null, max: null }
  sortBy.value = 'name'
}

const applyQuickFilter = (filter) => {
  priceRange.value.min = filter.min
  priceRange.value.max = filter.max
}

const viewProductDetail = (product) => {
  router.push({ name: 'product-detail', params: { id: product.id } })
}

const addToCart = async (product) => {
  try {
    await cartStore.addToCart(product.id, 1)
    console.log('Producto añadido al carrito:', product.nombre)
  } catch (error) {
    console.error('Error al añadir al carrito:', error)
  }
}

// Lifecycle
onMounted(async () => {
  // Cargar categorías primero
  await fetchCategories()
  // Luego cargar productos
  await fetchProducts()
  // Inicializar búsqueda desde query
  const q = (route.query.q || '').toString()
  if (q) localSearch.value = q
})

// Mantener sincronizada la query con el input local
const applySearch = () => {
  const q = localSearch.value.trim()
  router.push({ name: 'catalogo', query: q ? { q } : {} })
}

watch(() => route.query.q, (newQ) => {
  const q = (newQ || '').toString()
  if (q !== localSearch.value) localSearch.value = q
})
</script>

<style scoped>

.catalog-shell {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 24px;
  padding: 24px;
  border-radius: 16px;
  background-color: var(--surface);
  box-shadow: 0 10px 30px rgba(31,41,55,0.06);
  max-width: 1200px;
  margin: 0 auto 48px;
}

/* Buscador del catálogo */
.catalog-search {
  display: flex;
  gap: 8px;
  margin: 0 0 12px;
}
.catalog-search input {
  flex: 1;
  border: 1px solid #ddd;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 14px;
}
.catalog-search .search-btn {
  border: 2px solid var(--brand-strong);
  background: var(--brand-yellow-soft, #FFF8E1);
  color: var(--brand-strong);
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}

.filters-col {
  position: sticky;
  top: calc(var(--header-height, 96px) + 16px);
  align-self: start;
}

/* Reducir el ruido visual de los componentes internos para integrarlos al panel */
:deep(.filters-section) {
  background: transparent;
  border: 0;
  padding: 0;
}
:deep(.filters-section .container) {
  padding: 0;
}

:deep(.catalog-results) {
  padding: 0;
}
:deep(.catalog-results .container) {
  padding: 0;
}

@media (max-width: 1024px) {
  .catalog-shell {
    grid-template-columns: 1fr;
    padding: 16px;
  }
  .filters-col {
    position: static;
  }
}
</style>