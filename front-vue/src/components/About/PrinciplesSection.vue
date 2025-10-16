<template>
  <section class="principles-section">
    <div class="section-content gradient-card">
      <h2 class="section-title">
        <div class="title-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        Principios que nos Guían
      </h2>
      
      <div class="principles-list">
        <div 
          v-for="(principle, index) in principles" 
          :key="index"
          class="principle-item"
        >
          <div class="principle-number">
            {{ index + 1 }}
          </div>
          <div class="principle-content">
            <h3 class="principle-title">{{ principle.title }}</h3>
            <p class="principle-description">{{ principle.description }}</p>
          </div>
          <div class="principle-decoration">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 18l6-6-6-6"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const principles = ref([
  {
    title: 'Calidad Primero',
    description: 'Cada pieza pasa por rigurosos controles de calidad antes de llegar a nuestros clientes.'
  },
  {
    title: 'Innovación Constante',
    description: 'Adoptamos las últimas tecnologías en impresión 3D para ofrecer resultados superiores.'
  },
  {
    title: 'Colaboración Creativa',
    description: 'Trabajamos mano a mano con nuestros clientes para materializar sus visiones más ambiciosas.'
  },
  {
    title: 'Compromiso con la Comunidad',
    description: 'Apoyamos activamente la comunidad maker y cosplay a través de talleres y eventos.'
  },
  {
    title: 'Transparencia Total',
    description: 'Comunicación clara en cada etapa del proceso, desde la cotización hasta la entrega.'
  }
])
</script>

<style scoped>
.principles-section {
  margin-bottom: 3rem;
}

.gradient-card {
  background: linear-gradient(var(--surface), var(--surface)) padding-box,
              linear-gradient(135deg, rgba(240,192,64,0.4), rgba(122,0,25,0.3)) border-box;
  border: 2px solid transparent;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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

.principles-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.principle-item {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, var(--surface-2) 0%, #f9f1e6 100%);
  border-radius: 16px;
  border: 1px solid rgba(122, 0, 25, 0.1);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.principle-item:hover {
  transform: translateX(10px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.principle-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
}

.principle-number {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(125, 28, 43, 0.3);
}

.principle-content {
  flex: 1;
}

.principle-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--brand-strong);
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.principle-description {
  font-size: 1rem;
  color: var(--ink-3);
  line-height: 1.6;
  margin: 0;
}

.principle-decoration {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  color: rgba(122, 0, 25, 0.3);
  transition: all 0.3s ease;
}

.principle-item:hover .principle-decoration {
  color: var(--brand);
  transform: translateX(5px);
}

.principle-decoration svg {
  width: 100%;
  height: 100%;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .gradient-card {
    padding: 2rem;
  }
  
  .principles-list {
    gap: 1.25rem;
  }
}

@media (max-width: 768px) {
  .section-title {
    font-size: 1.75rem;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .principle-item {
    gap: 1rem;
    padding: 1.25rem;
  }
  
  .principle-item:hover {
    transform: translateY(-3px);
  }
}

@media (max-width: 480px) {
  .gradient-card {
    padding: 1.5rem;
  }
  
  .section-title {
    font-size: 1.5rem;
  }
  
  .title-icon,
  .principle-number {
    width: 40px;
    height: 40px;
  }
  
  .title-icon svg {
    width: 24px;
    height: 24px;
  }
  
  .principle-number {
    font-size: 1.2rem;
  }
  
  .principle-title {
    font-size: 1.1rem;
  }
  
  .principle-item {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .principle-decoration {
    display: none;
  }
}
</style>