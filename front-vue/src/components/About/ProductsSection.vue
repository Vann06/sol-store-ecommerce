<template>
  <section class="products-section">
    <div class="section-background">
      <div class="section-content">
        <h2 class="section-title">
          <div class="title-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
              <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
              <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
          </div>
          Lo que Ofrecemos
        </h2>
        
        <div class="products-grid">
          <div 
            v-for="(product, index) in products" 
            :key="index"
            class="product-card"
          >
            <div class="product-image">
              <div class="image-placeholder">
                <div class="placeholder-icon">
                  <span v-html="product.icon"></span>
                </div>
                <span class="placeholder-text">{{ product.imageDescription }}</span>
              </div>
            </div>
            <div class="product-info">
              <h3 class="product-title">{{ product.name }}</h3>
              <p class="product-description">{{ product.description }}</p>
              <div class="product-features">
                <span 
                  v-for="(feature, featureIndex) in product.features" 
                  :key="featureIndex"
                  class="feature-tag"
                >
                  {{ feature }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const products = ref([
  {
    name: 'Impresión de Prototipos',
    description: 'Materializamos tus ideas desde el concepto hasta el producto final.',
    icon: '⚙️',
    imageDescription: 'Prototipo funcional impreso en 3D',
    features: ['PLA', 'ABS', 'PETG', 'TPU']
  },
  {
    name: 'Props y Cosplay',
    description: 'Armaduras, armas y accesorios para cosplayers y coleccionistas.',
    icon: '🎭',
    imageDescription: 'Armadura de cosplay detallada',
    features: ['Pintado', 'Acabados', 'Ensamblaje', 'Asesorías']
  },
  {
    name: 'Piezas Funcionales',
    description: 'Repuestos, herramientas y componentes para uso industrial.',
    icon: '🔧',
    imageDescription: 'Componente mecánico impreso',
    features: ['Alta Precisión', 'Materiales Técnicos', 'Certificaciones']
  }
])
</script>

<style scoped>
.products-section {
  margin-bottom: 3rem;
}

.section-background {
  background: linear-gradient(135deg, #fdf8f0 0%, #f4ead6 50%, #f0e4d0 100%);
  border-radius: 20px;
  padding: 3rem 2rem;
  position: relative;
  overflow: hidden;
}

.section-background::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(122, 0, 25, 0.03) 0%, rgba(162, 115, 69, 0.03) 100%);
  border-radius: 20px;
  pointer-events: none;
}

.section-content {
  position: relative;
  z-index: 1;
}

.section-title {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  font-size: 2rem;
  font-weight: 700;
  color: var(--brand-strong);
  margin-bottom: 3rem;
  text-align: center;
}

.title-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 15px rgba(125, 28, 43, 0.3);
}

.title-icon svg {
  width: 28px;
  height: 28px;
  color: white;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid rgba(122, 0, 25, 0.1);
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--surface-2) 0%, #e9ecef 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid rgba(122, 0, 25, 0.1);
  text-align: center;
  padding: 1rem;
  position: relative;
}

.image-placeholder::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
}

.placeholder-icon {
  font-size: 3rem;
  margin-bottom: 0.5rem;
  opacity: 0.7;
}

.placeholder-text {
  font-size: 0.85rem;
  color: var(--ink-3);
  font-style: italic;
  max-width: 150px;
}

.product-info {
  padding: 2rem;
}

.product-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--brand-strong);
  margin: 0 0 1rem 0;
  line-height: 1.3;
}

.product-description {
  font-size: 1rem;
  color: var(--ink-3);
  line-height: 1.6;
  margin: 0 0 1.5rem 0;
}

.product-features {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.feature-tag {
  background: linear-gradient(135deg, rgba(125, 28, 43, 0.1) 0%, rgba(162, 115, 69, 0.1) 100%);
  color: var(--brand-strong);
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  border: 1px solid rgba(122, 0, 25, 0.2);
  transition: all 0.3s ease;
}

.feature-tag:hover {
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  color: white;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }
  
  .section-background {
    padding: 2.5rem 1.5rem;
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .section-title {
    font-size: 1.75rem;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .product-info {
    padding: 1.5rem;
  }
  
  .section-background {
    padding: 2rem 1rem;
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: 1.5rem;
  }
  
  .title-icon {
    width: 40px;
    height: 40px;
  }
  
  .title-icon svg {
    width: 24px;
    height: 24px;
  }
  
  .product-title {
    font-size: 1.25rem;
  }
  
  .product-info {
    padding: 1.25rem;
  }
  
  .placeholder-icon {
    font-size: 2.5rem;
  }
  
  .image-placeholder {
    height: 150px;
  }
}
</style>