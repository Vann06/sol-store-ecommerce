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
        <div 
          v-for="product in filteredProducts" 
          :key="product.id"
          class="product-card"
          @click="$emit('view-product', product)"
        >
          <div class="product-image-container">
            <img 
              :src="getImageUrl(product.imagen)" 
              :alt="product.nombre"
              class="product-img" 
              @error="handleImageError($event)"
            />
          </div>
          <div class="product-details">
            <h3 class="product-name">{{ product.nombre }}</h3>
            <p class="product-price">Q{{ Number(product.precio_base || 0).toFixed(2) }}</p>
            <p class="product-description">{{ product.descripcion }}</p>
            <button class="add-to-cart" @click.stop="$emit('add-to-cart', product)">
              Añadir al carrito
            </button>
          </div>
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

// Methods
const getImageUrl = (imagen) => {
  if (!imagen) return 'https://via.placeholder.com/220x200'
  if (imagen.startsWith('http')) return imagen
  return `http://localhost:8000/${imagen}`
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/220x200/cccccc/666666?text=Sin+Imagen'
}
</script>

<style scoped>
/* Resultados */
.catalog-results {
  padding: 40px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
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
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 30px;
  padding: 20px 0;
  justify-items: center;
}

.product-card {
  width: 100%;
  max-width: 300px;
  background: white;
  border: 2px solid #8B0000;
  border-radius: 15px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(139, 0, 0, 0.2);
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  background: #f5f5f5;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  padding: 15px;
  text-align: center;
}

.product-name {
  font-weight: 700;
  font-size: 18px;
  color: #8B0000;
  margin-bottom: 8px;
}

.product-price {
  color: #8B0000;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
}

.product-description {
  color: #666;
  font-size: 14px;
  margin-bottom: 15px;
  line-height: 1.4;
}

.add-to-cart {
  width: 100%;
  padding: 12px;
  background-color: #8B0000;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.add-to-cart:hover {
  background-color: #a30000;
}

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