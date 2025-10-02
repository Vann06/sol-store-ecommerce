<template>
  <div class="catalog-page">
    <Header />
    
    <!-- Hero Banner del Catálogo -->
    <CatalogHero />

    <!-- Sistema de Filtros -->
    <CatalogFilters 
      :categories="categories"
      :selected-categories="selectedCategories"
      :selected-complexity="selectedComplexity"
      :price-range="priceRange"
      :products="products"
      :quick-filters="quickFilters"
      :complexity-levels="complexityLevels"
      @clear-filters="clearFilters"
      @apply-quick-filter="applyQuickFilter"
      @update:selected-categories="selectedCategories = $event"
      @update:selected-complexity="selectedComplexity = $event"
      @update:price-range="priceRange = $event"
    />

    <!-- Resultados del Catálogo -->
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'

// Componentes
import Header from '@/components/Header.vue'
import CatalogHero from '@/components/Catalog/CatalogHero.vue'
import CatalogFilters from '@/components/Catalog/CatalogFilters.vue'
import CatalogResults from '@/components/Catalog/CatalogResults.vue'

// Composables
const router = useRouter()
const cartStore = useCartStore()

// State
const selectedCategories = ref([])
const selectedComplexity = ref(null)
const priceRange = ref({ min: null, max: null })
const sortBy = ref('name')
const products = ref([])
const categories = ref([])
const loading = ref(false)
const error = ref(null)

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
const filteredProducts = computed(() => {
  let filtered = products.value
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
    const response = await axios.get('http://localhost:8000/api/categorias')
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
    
    const response = await axios.get('http://localhost:8000/api/productos')
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
})
</script>

<style scoped>
.catalog-page {
  font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
  color: #333333;
}
</style>