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
        <div class="filter-group" :class="{ collapsed: !expanded.category }">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.category.toString()"
              aria-controls="fg-category"
              @click="expanded.category = !expanded.category"
            >
              <span class="title"><i class="fas fa-tags"></i> Categoría</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.category }" aria-hidden="true"></i>
            </button>
          </h3>
          <transition name="collapse">
            <div class="filter-options collapsible" v-show="expanded.category" id="fg-category">
              <label 
                v-for="category in visibleCategories" 
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
              <button v-if="categories.length > (categoryVisibleCount?.val || 0)" class="show-more" type="button" @click="categoryVisibleCount.val += 5">
                Mostrar más +
              </button>
            </div>
          </transition>
        </div>

        <!-- Filtro por Temáticas -->
        <div class="filter-group" :class="{ collapsed: !expanded.themes }" v-if="themes && themes.length">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.themes.toString()"
              aria-controls="fg-themes"
              @click="expanded.themes = !expanded.themes"
            >
              <span class="title"><i class="fas fa-star"></i> Temáticas</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.themes }" aria-hidden="true"></i>
            </button>
          </h3>
          <transition name="collapse">
            <div class="filter-options collapsible" v-show="expanded.themes" id="fg-themes">
              <label 
                v-for="theme in themes" 
                :key="theme.id"
                class="filter-option"
                :class="{ active: selectedThemes.includes(theme.id) }"
              >
                <input 
                  type="checkbox" 
                  :value="theme.id"
                  :checked="selectedThemes.includes(theme.id)"
                  @change="toggleTheme(theme.id)"
                >
                <span class="checkmark"></span>
                {{ theme.name }}
              </label>
            </div>
          </transition>
        </div>

        <!-- Filtro por Estado/Disponibilidad (dinámico desde API) -->
        <div class="filter-group" :class="{ collapsed: !expanded.status }" v-if="availableStatuses.length">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.status?.toString()"
              aria-controls="fg-status"
              @click="expanded.status = !expanded.status"
            >
              <span class="title"><i class="fas fa-clipboard-check"></i> Disponibilidad</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.status }" aria-hidden="true"></i>
            </button>
          </h3>
          <transition name="collapse">
            <div class="filter-options collapsible" v-show="expanded.status" id="fg-status">
              <label 
                v-for="s in availableStatuses" 
                :key="s.value"
                class="filter-option"
                :class="{ active: selectedStatus.includes(s.value) }"
              >
                <input 
                  type="checkbox" 
                  :value="s.value"
                  :checked="selectedStatus.includes(s.value)"
                  @change="toggleStatus(s.value)"
                >
                <span class="checkmark"></span>
                {{ s.label }}
                <span class="product-count">({{ products.filter(p => (p.status||'').toString().trim().toLowerCase() === s.value).length }})</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Filtro por Complejidad -->
        <div class="filter-group" :class="{ collapsed: !expanded.complexity }">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.complexity.toString()"
              aria-controls="fg-complexity"
              @click="expanded.complexity = !expanded.complexity"
            >
              <span class="title"><i class="fas fa-star"></i> Nivel de Detalle</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.complexity }" aria-hidden="true"></i>
            </button>
          </h3>
          <transition name="collapse">
            <div class="filter-options collapsible" v-show="expanded.complexity" id="fg-complexity">
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
          </transition>
        </div>

        <!-- Filtro por Precio -->
        <div class="filter-group price-filter-compact" :class="{ collapsed: !expanded.price }">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.price.toString()"
              aria-controls="fg-price"
              @click="expanded.price = !expanded.price"
            >
              <span class="title"><i class="fas fa-dollar-sign"></i> Precio</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.price }" aria-hidden="true"></i>
            </button>
          </h3>
          <transition name="collapse">
            <div class="price-range-compact collapsible" v-show="expanded.price" id="fg-price">
              <div class="price-slider" role="group" aria-label="Rango de precio">
                <div class="slider">
                  <div class="slider-track" :style="sliderTrackStyle"></div>
                  <input
                    class="range range-min"
                    type="range"
                    :min="availablePriceMin"
                    :max="availablePriceMax"
                    :step="priceStep"
                    :value="currentMin"
                    :aria-valuemin="availablePriceMin"
                    :aria-valuemax="availablePriceMax"
                    :aria-valuenow="currentMin"
                    aria-label="Precio mínimo"
                    @input="onMinRangeInput($event.target.value)"
                  />
                  <input
                    class="range range-max"
                    type="range"
                    :min="availablePriceMin"
                    :max="availablePriceMax"
                    :step="priceStep"
                    :value="currentMax"
                    :aria-valuemin="availablePriceMin"
                    :aria-valuemax="availablePriceMax"
                    :aria-valuenow="currentMax"
                    aria-label="Precio máximo"
                    @input="onMaxRangeInput($event.target.value)"
                  />
                </div>
                <div class="price-values">
                  <div class="value"><span>Mín</span><strong>{{ currency(currentMin) }}</strong></div>
                  <span class="price-separator">-</span>
                  <div class="value"><span>Máx</span><strong>{{ currency(currentMax) }}</strong></div>
                </div>
              </div>
              <div class="price-inputs-compact">
                <div class="price-input-group">
                  <input 
                    type="number" 
                    :value="currentMin"
                    @input="updatePriceMin($event.target.value)"
                    placeholder="Mín"
                    class="price-input-small"
                  >
                </div>
                <span class="price-separator">-</span>
                <div class="price-input-group">
                  <input 
                    type="number" 
                    :value="currentMax"
                    @input="updatePriceMax($event.target.value)"
                    placeholder="Máx"
                    class="price-input-small"
                  >
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>

      <!-- Filtros rápidos -->
      <div class="quick-filters-improved" :class="{ collapsed: !expanded.quick }">
        <div class="quick-filters-header">
          <h3>
            <button
              class="filter-group-toggle"
              type="button"
              :aria-expanded="expanded.quick.toString()"
              aria-controls="fg-quick-price"
              @click="expanded.quick = !expanded.quick"
            >
              <span class="title"><i class="fas fa-bolt"></i> Filtros Rápidos de Precio</span>
              <i class="fas fa-chevron-down arrow" :class="{ rotated: expanded.quick }" aria-hidden="true"></i>
            </button>
          </h3>
        </div>
        <transition name="collapse">
          <div class="quick-filter-buttons-improved collapsible" v-show="expanded.quick" id="fg-quick-price">
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
        </transition>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, reactive, onMounted } from 'vue'

// Props
const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  themes: {
    type: Array,
    default: () => []
  },
  selectedCategories: {
    type: Array,
    default: () => []
  },
  selectedThemes: {
    type: Array,
    default: () => []
  },
  selectedStatus: {
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
  'update:selected-themes',
  'update:selected-status',
  'update:selected-complexity',
  'update:price-range'
])

// Computed
const activeFiltersCount = computed(() => {
  let count = 0
  count += props.selectedCategories.length
  count += props.selectedStatus.length
  count += props.selectedThemes.length
  count += props.selectedComplexity ? 1 : 0
  count += (props.priceRange.min !== null || props.priceRange.max !== null) ? 1 : 0
  return count
})

// Price slider helpers
const prices = computed(() => props.products.map(p => Number(p.precio_base ?? p.price ?? 0)).filter(n => !Number.isNaN(n)))
const availablePriceMin = computed(() => prices.value.length ? Math.min(...prices.value) : 0)
const availablePriceMax = computed(() => prices.value.length ? Math.max(...prices.value) : 1000)
const priceStep = computed(() => {
  const range = availablePriceMax.value - availablePriceMin.value
  // dynamic step: 1% of range, minimum 1
  const step = Math.max(1, Math.round(range / 100))
  return step
})

const currentMin = computed(() => props.priceRange.min ?? availablePriceMin.value)
const currentMax = computed(() => props.priceRange.max ?? availablePriceMax.value)

const clamp = (val, min, max) => Math.min(Math.max(val, min), max)

const onMinRangeInput = (val) => {
  const n = clamp(Number(val), availablePriceMin.value, currentMax.value)
  updatePriceMin(n)
}
const onMaxRangeInput = (val) => {
  const n = clamp(Number(val), currentMin.value, availablePriceMax.value)
  updatePriceMax(n)
}

const sliderTrackStyle = computed(() => {
  const min = availablePriceMin.value
  const max = availablePriceMax.value
  const left = ((currentMin.value - min) / (max - min)) * 100
  const right = ((currentMax.value - min) / (max - min)) * 100
  return { background: `linear-gradient(90deg, #e9e9e9 ${left}%, var(--accent) ${left}%, var(--accent-2) ${right}%, #e9e9e9 ${right}%)` }
})

const currency = (n) => new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ', maximumFractionDigits: 0 }).format(n)

// Derived statuses from products
const availableStatuses = computed(() => {
  const set = new Set(
    props.products
      .map(p => (p.status || '').toString().trim().toLowerCase())
      .filter(Boolean)
  )
  // Map to label form (capitalize first letter)
  return Array.from(set).map(v => ({ value: v, label: v.charAt(0).toUpperCase() + v.slice(1) }))
})

// UI state: expanded/collapsed per group
const expanded = reactive({ category: true, themes: true, complexity: true, price: true, quick: true, status: true })

// Show more for categories
const categoryVisibleBase = 6
const categoryVisibleCount = reactive({ val: categoryVisibleBase })
const visibleCategories = computed(() => props.categories.slice(0, categoryVisibleCount.val || categoryVisibleBase))

onMounted(() => {
  // On small screens, start collapsed to save space
  if (window.matchMedia && window.matchMedia('(max-width: 768px)').matches) {
    expanded.category = false
    expanded.complexity = false
    expanded.price = false
    expanded.quick = false
    expanded.status = false
  }
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

const toggleStatus = (value) => {
  const newStatuses = [...props.selectedStatus]
  const index = newStatuses.indexOf(value)
  if (index > -1) {
    newStatuses.splice(index, 1)
  } else {
    newStatuses.push(value)
  }
  emit('update:selected-status', newStatuses)
}

// Themes filter helpers
const toggleTheme = (themeId) => {
  const newThemes = [...props.selectedThemes]
  const idx = newThemes.indexOf(themeId)
  if (idx > -1) newThemes.splice(idx, 1)
  else newThemes.push(themeId)
  emit('update:selected-themes', newThemes)
}

const updatePriceMin = (value) => {
  const n = value === '' || value === null ? null : Number(value)
  const bounded = n === null ? null : clamp(n, availablePriceMin.value, currentMax.value)
  const newPriceRange = { ...props.priceRange, min: bounded }
  emit('update:price-range', newPriceRange)
}

const updatePriceMax = (value) => {
  const n = value === '' || value === null ? null : Number(value)
  const bounded = n === null ? null : clamp(n, currentMin.value, availablePriceMax.value)
  const newPriceRange = { ...props.priceRange, max: bounded }
  emit('update:price-range', newPriceRange)
}
</script>

<style scoped>
/* Sección de Filtros */
.filters-section { background: #FAFAFA; padding: 30px 0; border-bottom: 2px solid var(--accent-2); }

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.filters-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }

.section-title { color: var(--brand-strong); font-size: 2rem; margin: 0; }

.clear-filters-btn {
  background: linear-gradient(135deg, var(--accent), var(--accent-2));
  color: var(--brand-strong);
  border: 2px solid var(--accent-2);
  padding: 10px 20px; border-radius: 25px; cursor: pointer;
  font-weight: 700; transition: all 0.25s ease; font-size: 0.95rem;
  box-shadow: 0 6px 14px rgba(240,192,64,0.25);
}
.clear-filters-btn:hover:not(:disabled) { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 18px rgba(240,192,64,0.35); }
.clear-filters-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px; }

.filter-group {
  border: 2px solid transparent;
  background: linear-gradient(var(--surface), var(--surface)) padding-box,
             linear-gradient(135deg, rgba(240,192,64,0.6), rgba(255,217,90,0.5)) border-box;
  border-radius: 15px; padding: 20px; min-height: 220px;
}

.filter-group.collapsed { min-height: auto; }
.filter-group h3 { margin: 0 0 10px 0; }
.filter-group-toggle {
  width: 100%; display: flex; align-items: center; justify-content: space-between;
  gap: 10px; background: transparent; border: none; padding: 0; cursor: pointer;
  color: var(--brand-strong); font-size: 1.2rem; font-weight: 700;
}
.filter-group-toggle .title { display: inline-flex; align-items: center; gap: 10px; }
.filter-group-toggle .arrow { transition: transform 0.2s ease; }
.filter-group-toggle .arrow.rotated { transform: rotate(180deg); }

.collapsible { overflow: hidden; }
.collapse-enter-from, .collapse-leave-to { max-height: 0; opacity: 0; }
.collapse-enter-to, .collapse-leave-from { max-height: 500px; opacity: 1; }
.collapse-enter-active, .collapse-leave-active { transition: all 0.25s ease; }

.filter-group h3 { color: var(--brand-strong); margin-bottom: 15px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }

.filter-options { display: flex; flex-direction: column; gap: 12px; }
.show-more { align-self: center; margin-top: 6px; background: transparent; border: 0; color: var(--brand-strong); font-weight: 700; cursor: pointer; padding: 6px 10px; border-radius: 8px; }
.show-more:hover { background: rgba(240,192,64,0.15); }

.filter-option {
  display: flex; align-items: center; cursor: pointer;
  padding: 10px 12px; border-radius: 10px; transition: all 0.2s ease; font-size: 0.95rem;
  border: 1px solid rgba(240,192,64,0.6);
  background: linear-gradient(135deg, rgba(255,248,225,0.95), rgba(255,237,179,0.9));
  color: var(--ink-1);
}
.filter-option:hover { background: linear-gradient(135deg, rgba(255,241,173,0.95), rgba(255,225,130,0.9)); box-shadow: 0 4px 12px rgba(240,192,64,0.25); }
.filter-option.active { background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: var(--brand-strong); border-color: var(--accent-2); box-shadow: 0 6px 16px rgba(240,192,64,0.35); }
.filter-option input { margin-right: 12px; }

.product-count { margin-left: auto; font-size: 0.85rem; opacity: 0.9; font-weight: 700; color: var(--brand-strong); }

/* Filtro de precio */
.price-filter-compact { min-height: 180px; }
.price-range-compact { display: flex; flex-direction: column; gap: 15px; }
.price-slider { display: flex; flex-direction: column; gap: 10px; }
.price-slider .price-values { display: flex; align-items: center; justify-content: center; gap: 10px; }
.price-slider .price-values .value { display: flex; flex-direction: column; align-items: center; font-weight: 700; color: var(--brand-strong); }
.price-slider .price-values .value span { font-size: 0.8rem; opacity: 0.8; }
.slider { position: relative; height: 36px; }
.slider-track { position: absolute; left: 0; right: 0; top: 50%; transform: translateY(-50%); height: 6px; border-radius: 999px; background: #e9e9e9; }
.range { position: absolute; left: 0; top: 0; width: 100%; pointer-events: none; -webkit-appearance: none; appearance: none; background: none; height: 36px; margin: 0; }
.range::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; pointer-events: auto; width: 22px; height: 22px; border-radius: 50%; background: radial-gradient(circle at 30% 30%, #fff, #fff 40%, rgba(255,255,255,0.9) 70%); border: 3px solid var(--accent-2); box-shadow: 0 2px 6px rgba(0,0,0,0.2); cursor: pointer; }
.range::-moz-range-thumb { pointer-events: auto; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 3px solid var(--accent-2); box-shadow: 0 2px 6px rgba(0,0,0,0.2); cursor: pointer; }
.range::-webkit-slider-runnable-track { height: 36px; background: transparent; }
.range::-moz-range-track { height: 36px; background: transparent; }
.range.range-min { z-index: 3; }
.range.range-max { z-index: 2; }
.price-inputs-compact { display: flex; align-items: center; gap: 10px; justify-content: center; }
.price-input-group { display: flex; flex-direction: column; align-items: center; gap: 5px; }

.price-input-small {
  width: 100px; padding: 8px 10px; border: 2px solid var(--accent-2); border-radius: 8px; font-size: 0.9rem;
  background: #fffef7; text-align: center; font-weight: 700;
}
.price-input-small:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(240, 192, 64, 0.35); }

.price-separator { color: var(--brand-strong); font-weight: bold; font-size: 1.2rem; margin: 0 5px; }

/* Filtros rápidos */
.quick-filters-improved {
  border: 2px solid transparent;
  background: linear-gradient(var(--surface), var(--surface)) padding-box,
             linear-gradient(135deg, rgba(240,192,64,0.6), rgba(255,217,90,0.5)) border-box;
  border-radius: 15px; padding: 20px; margin-top: 10px;
}

.quick-filters-header { text-align: center; margin-bottom: 20px; }
.quick-filters-header h3 { color: var(--brand-strong); font-size: 1.2rem; display: flex; align-items: center; justify-content: center; gap: 10px; margin: 0; }

.quick-filter-buttons-improved { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }

.quick-filter-btn-improved {
  background: linear-gradient(135deg, rgba(255,248,225,0.98), rgba(255,237,179,0.95));
  border: 2px solid var(--accent-2); color: var(--brand-strong);
  padding: 12px 15px; border-radius: 12px; cursor: pointer;
  transition: all 0.2s ease; display: flex; justify-content: space-between; align-items: center; font-weight: 700;
  box-shadow: 0 4px 12px rgba(240,192,64,0.20);
}
.quick-filter-btn-improved:hover { background: linear-gradient(135deg, rgba(255,241,173,0.98), rgba(255,225,130,0.95)); transform: translateY(-2px); box-shadow: 0 8px 18px rgba(240,192,64,0.30); }
.quick-filter-btn-improved.active { background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: var(--brand-strong); border-color: var(--accent-2); box-shadow: 0 10px 22px rgba(240,192,64,0.35); }

.filter-label { font-size: 0.95rem; }
.filter-count { font-size: 0.85rem; opacity: 0.9; background: rgba(240, 192, 64, 0.25); padding: 2px 8px; border-radius: 10px; }
.quick-filter-btn-improved.active .filter-count { background: rgba(255, 255, 255, 0.35); }

/* Responsive */
@media (max-width: 768px) {
  .filters-header { flex-direction: column; gap: 15px; text-align: center; }
  .filters-grid { grid-template-columns: 1fr; }
  .quick-filter-buttons-improved { grid-template-columns: 1fr; }
  .price-inputs-compact { flex-direction: column; gap: 10px; }
  .price-separator { margin: 0; }
  .filter-group { padding: 16px; }
}

@media (max-width: 480px) {
  .filter-group { padding: 15px; min-height: auto; }
  .price-input-small { width: 80px; }
}
</style>