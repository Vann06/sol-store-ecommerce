<template>
  <section class="catalog-results">
    <div class="container">
      <div class="results-header">
        <h2 class="section-title">
          Productos Disponibles
          <span class="results-count">({{ filteredProducts.length }} resultados)</span>
        </h2>
        <div v-if="loading" class="loading-indicator">
          <p>Cargando productos...</p>
        </div>
        <div v-if="error" class="error-message">
          <p>{{ error }}</p>
        </div>
        <div class="sort-options">
          <select 
            :value="sortBy" 
            @change="$emit('update:sort-by', $event.target.value)"
            class="sort-select"
          >
            <option value="name">Ordenar por: Nombre</option>
            <option value="price-asc">Precio: Menor a Mayor</option>
            <option value="price-desc">Precio: Mayor a Menor</option>
            <option value="complexity">Complejidad</option>
          </select>
        </div>
      </div>
      
      <div class="products-grid" v-if="!loading">
        <div v-for="product in filteredProducts" :key="product.id" class="grid-item" @click="$emit('view-product', product)">
          <ProductCard :product="normalize(product)" :minimal="true" />
        </div>
      </div>

      <!-- Mensaje sin resultados -->
      <div v-if="filteredProducts.length === 0 && !loading" class="no-results">
        <i class="fas fa-search"></i>
        <h3>No se encontraron productos</h3>
        <p>Intenta ajustar los filtros para ver más resultados</p>
        <button @click="$emit('clear-filters')" class="cta-button">Mostrar Todos los Productos</button>
      </div>
    </div>
  </section>
</template>

<script setup>
import ProductCard from '@/components/ProductCard.vue'

// Props
const props = defineProps({
  filteredProducts: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: [String, null],
    default: null
  },
  sortBy: {
    type: String,
    default: 'name'
  }
})

// Emits
const emit = defineEmits(['view-product', 'add-to-cart', 'clear-filters', 'update:sort-by'])

// Helpers para normalizar datos hacia ProductCard
const normalize = (p) => ({
  id: p.id,
  nombre: p.nombre || p.name || 'Producto',
  precio: Number(p.precio_base ?? p.precio ?? p.price ?? 0),
  precio_original: p.precio_original,
  descuento: p.descuento,
  imagen: p.imagen || p.image || p.image_url || '/img/no-image.png',
})
</script>

<style scoped>
/* Resultados */
.catalog-results {
  padding: 40px 0;
}

.container {
  max-width: 1140px;
  margin: 0 auto;
  padding: 0 16px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 20px;
}

.section-title {
  color: #8B0000;
  font-size: 2rem;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.results-count {
  font-size: 1rem;
  color: #8B0000;
  opacity: 0.8;
  font-weight: normal;
}

.loading-indicator, .error-message {
  text-align: center;
  padding: 20px;
  margin: 10px 0;
}

.error-message {
  background: #ffebee;
  color: #c62828;
  border-radius: 5px;
}

.sort-select {
  padding: 10px 15px;
  border: 2px solid #8B0000;
  border-radius: 8px;
  background: #FFF8E1;
  color: #8B0000;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
}

/* Grid de Productos - Estilo Simplificado */
.products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.grid-item { width: 100%; }
@media (max-width: 1024px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .products-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .products-grid { grid-template-columns: 1fr; } }

/* Sin resultados */
.no-results {
  text-align: center;
  padding: 60px 20px;
  color: #8B0000;
}

.no-results i {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.5;
}

.no-results h3 {
  font-size: 1.5rem;
  margin-bottom: 10px;
}

.cta-button {
  background: #FBC02D;
  color: #8B0000;
  border: 2px solid #8B0000;
  padding: 12px 30px;
  border-radius: 25px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 20px;
}

.cta-button:hover {
  background: #8B0000;
  color: #FBC02D;
}

/* Responsive */
@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    justify-items: center;
  }
  
  .product-card {
    max-width: 350px;
  }
  
  .results-header {
    flex-direction: column;
    text-align: center;
    gap: 15px;
  }
  
  .section-title {
    flex-direction: column;
    gap: 5px;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>