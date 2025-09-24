<template>
  <section class="filters-section">
    <div class="container">
      <div class="filters-header">
        <h2 class="section-title">Filtrar Productos</h2>
        <div class="filter-controls">
          <button 
            @click="$emit('clear-filters')" 
            class="clear-filters-btn"
            :disabled="activeFiltersCount === 0"
          >
            <i class="fas fa-times"></i> Limpiar Filtros ({{ activeFiltersCount }})
          </button>
        </div>
      </div>

      <div class="filters-grid">
        <!-- Filtro por Categoría -->
        <div class="filter-group">
          <h3><i class="fas fa-tags"></i> Categoría</h3>
          <div class="filter-options">
            <label 
              v-for="category in categories" 
              :key="category.id"
              class="filter-option"
              :class="{ active: selectedCategories.includes(category.id) }"
            >
              <input 
                type="checkbox" 
                :value="category.id"
                :checked="selectedCategories.includes(category.id)"
                @change="toggleCategory(category.id)"
              >
              <span class="checkmark"></span>
              {{ category.name }}
              <span class="product-count">({{ getCategoryCount(category.id) }})</span>
            </label>
          </div>
        </div>

        <!-- Filtro por Complejidad -->
        <div class="filter-group">
          <h3><i class="fas fa-star"></i> Nivel de Detalle</h3>
          <div class="filter-options">
            <label 
              v-for="complexity in complexityLevels" 
              :key="complexity.value"
              class="filter-option"
              :class="{ active: selectedComplexity === complexity.value }"
            >
              <input 
                type="radio" 
                name="complexity"
                :value="complexity.value"
                :checked="selectedComplexity === complexity.value"
                @change="$emit('update:selected-complexity', complexity.value)"
              >
              <span class="radiomark"></span>
              {{ complexity.label }}
            </label>
          </div>
        </div>

        <!-- Filtro por Precio -->
        <div class="filter-group price-filter-compact">
          <h3><i class="fas fa-dollar-sign"></i> Precio</h3>
          <div class="price-range-compact">
            <div class="price-inputs-compact">
              <div class="price-input-group">
                <input 
                  type="number" 
                  :value="priceRange.min"
                  @input="updatePriceMin($event.target.value)"
                  placeholder="Mín"
                  class="price-input-small"
                >
              </div>
              <span class="price-separator">-</span>
              <div class="price-input-group">
                <input 
                  type="number" 
                  :value="priceRange.max"
                  @input="updatePriceMax($event.target.value)"
                  placeholder="Máx"
                  class="price-input-small"
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros rápidos -->
      <div class="quick-filters-improved">
        <div class="quick-filters-header">
          <h3><i class="fas fa-bolt"></i> Filtros Rápidos de Precio</h3>
        </div>
        <div class="quick-filter-buttons-improved">
          <button 
            v-for="filter in quickFilters" 
            :key="filter.id"
            @click="$emit('apply-quick-filter', filter)"
            class="quick-filter-btn-improved"
            :class="{ active: isQuickFilterActive(filter) }"
          >
            <span class="filter-label">{{ filter.label }}</span>
            <span class="filter-count">({{ getPriceRangeCount(filter) }})</span>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  selectedCategories: {
    type: Array,
    default: () => []
  },
  selectedComplexity: {
    type: [String, null],
    default: null
  },
  priceRange: {
    type: Object,
    default: () => ({ min: null, max: null })
  },
  products: {
    type: Array,
    default: () => []
  },
  quickFilters: {
    type: Array,
    default: () => []
  },
  complexityLevels: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits([
  'clear-filters', 
  'apply-quick-filter', 
  'update:selected-categories',
  'update:selected-complexity',
  'update:price-range'
])

// Computed
const activeFiltersCount = computed(() => {
  let count = 0
  count += props.selectedCategories.length
  count += props.selectedComplexity ? 1 : 0
  count += (props.priceRange.min !== null || props.priceRange.max !== null) ? 1 : 0
  return count
})

// Methods
const getCategoryCount = (categoryId) => {
  return props.products.filter(product => {
    const productCategory = product.categoria?.name || product.categoria?.nombre || product.category || ''
    return productCategory.toLowerCase().includes(categoryId.toLowerCase()) || 
           categoryId.toLowerCase().includes(productCategory.toLowerCase())
  }).length
}

const getPriceRangeCount = (filter) => {
  return props.products.filter(product => {
    const price = product.precio_base || product.price || 0
    const meetsMin = filter.min === null || price >= filter.min
    const meetsMax = filter.max === null || price <= filter.max
    return meetsMin && meetsMax
  }).length
}

const isQuickFilterActive = (filter) => {
  return props.priceRange.min === filter.min && props.priceRange.max === filter.max
}

const toggleCategory = (categoryId) => {
  const newCategories = [...props.selectedCategories]
  const index = newCategories.indexOf(categoryId)
  if (index > -1) {
    newCategories.splice(index, 1)
  } else {
    newCategories.push(categoryId)
  }
  emit('update:selected-categories', newCategories)
}

const updatePriceMin = (value) => {
  const newPriceRange = { ...props.priceRange }
  newPriceRange.min = value ? Number(value) : null
  emit('update:price-range', newPriceRange)
}

const updatePriceMax = (value) => {
  const newPriceRange = { ...props.priceRange }
  newPriceRange.max = value ? Number(value) : null
  emit('update:price-range', newPriceRange)
}
</script>

<style scoped>
/* Sección de Filtros */
.filters-section {
  background: #FAFAFA;
  padding: 30px 0;
  border-bottom: 2px solid #8B0000;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.section-title {
  color: #8B0000;
  font-size: 2rem;
  margin: 0;
}

.clear-filters-btn {
  background: #FBC02D;
  color: #8B0000;
  border: 2px solid #8B0000;
  padding: 10px 20px;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
  font-size: 0.9rem;
}

.clear-filters-btn:hover:not(:disabled) {
  background: #8B0000;
  color: #FBC02D;
}

.clear-filters-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 25px;
}

.filter-group {
  background: #FFF8E1;
  border: 2px solid #8B0000;
  border-radius: 15px;
  padding: 20px;
  min-height: 220px;
}

.filter-group h3 {
  color: #8B0000;
  margin-bottom: 15px;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-option {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 8px 10px;
  border-radius: 8px;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.filter-option:hover {
  background: rgba(139, 0, 0, 0.1);
}

.filter-option.active {
  background: #8B0000;
  color: white;
}

.filter-option input {
  margin-right: 12px;
}

.product-count {
  margin-left: auto;
  font-size: 0.85rem;
  opacity: 0.8;
  font-weight: 600;
}

/* Filtro de precio */
.price-filter-compact {
  min-height: 180px;
}

.price-range-compact {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.price-inputs-compact {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: center;
}

.price-input-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.price-input-small {
  width: 100px;
  padding: 8px 10px;
  border: 2px solid #8B0000;
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
  text-align: center;
  font-weight: 600;
}

.price-input-small:focus {
  outline: none;
  border-color: #FBC02D;
  box-shadow: 0 0 0 3px rgba(251, 192, 45, 0.3);
}

.price-separator {
  color: #8B0000;
  font-weight: bold;
  font-size: 1.2rem;
  margin: 0 5px;
}

/* Filtros rápidos */
.quick-filters-improved {
  background: #FFF8E1;
  border: 2px solid #8B0000;
  border-radius: 15px;
  padding: 20px;
  margin-top: 10px;
}

.quick-filters-header {
  text-align: center;
  margin-bottom: 20px;
}

.quick-filters-header h3 {
  color: #8B0000;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin: 0;
}

.quick-filter-buttons-improved {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
}

.quick-filter-btn-improved {
  background: white;
  border: 2px solid #8B0000;
  color: #8B0000;
  padding: 12px 15px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
}

.quick-filter-btn-improved:hover {
  background: #8B0000;
  color: white;
  transform: translateY(-2px);
}

.quick-filter-btn-improved.active {
  background: #8B0000;
  color: white;
  border-color: #FBC02D;
}

.filter-label {
  font-size: 0.95rem;
}

.filter-count {
  font-size: 0.85rem;
  opacity: 0.8;
  background: rgba(251, 192, 45, 0.2);
  padding: 2px 8px;
  border-radius: 10px;
}

.quick-filter-btn-improved.active .filter-count {
  background: rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .filters-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-filter-buttons-improved {
    grid-template-columns: 1fr;
  }
  
  .price-inputs-compact {
    flex-direction: column;
    gap: 10px;
  }
  
  .price-separator {
    margin: 0;
  }
}

@media (max-width: 480px) {
  .filter-group {
    padding: 15px;
    min-height: auto;
  }
  
  .price-input-small {
    width: 80px;
  }
}
</style>