<template>
  <section class="commitment-section">
    <div class="section-background">
      <div class="section-content">
        <h2 class="section-title">
          <div class="title-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11H5a2 2 0 0 0-2 2v3c0 .55.45 1 1 1h4m0-6v6m0-6V9a2 2 0 0 1 2-2h2m8 0h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2m0 0V9a2 2 0 0 0-2-2H9"/>
            </svg>
          </div>
          Nuestro Compromiso
        </h2>
        
        <div class="commitment-grid">
          <div 
            v-for="(commitment, index) in commitments" 
            :key="index"
            class="commitment-card"
          >
            <div class="card-header">
              <div class="commitment-icon">
                <span v-html="commitment.icon"></span>
              </div>
              <h3 class="commitment-title">{{ commitment.title }}</h3>
            </div>
            <p class="commitment-description">{{ commitment.description }}</p>
            <div class="card-accent"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const commitments = ref([
  {
    icon: '✓',
    title: 'Precisión en Cada Capa',
    description: 'Impresiones con tolerancias de ±0.1mm para piezas perfectas'
  },
  {
    icon: '🎭',
    title: 'Especialistas en Cosplay',
    description: 'Asesoría técnica para armaduras, props y accesorios'
  },
  {
    icon: '⚡',
    title: 'Rápido Turnaround',
    description: 'Entregas express para proyectos urgentes'
  }
])
</script>

<style scoped>
.commitment-section {
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

.commitment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.commitment-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid rgba(122, 0, 25, 0.1);
}

.commitment-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.commitment-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(125, 28, 43, 0.3);
}

.commitment-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--brand-strong);
  margin: 0;
  line-height: 1.3;
}

.commitment-description {
  font-size: 1rem;
  color: var(--ink-3);
  line-height: 1.6;
  margin: 0;
}

.card-accent {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .commitment-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }
  
  .section-background {
    padding: 2.5rem 1.5rem;
  }
}

@media (max-width: 768px) {
  .commitment-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .section-title {
    font-size: 1.75rem;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .commitment-card {
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
  
  .title-icon,
  .commitment-icon {
    width: 40px;
    height: 40px;
  }
  
  .title-icon svg {
    width: 24px;
    height: 24px;
  }
  
  .commitment-icon {
    font-size: 1.2rem;
  }
  
  .commitment-title {
    font-size: 1.1rem;
  }
  
  .card-header {
    gap: 0.75rem;
  }
  
  .commitment-card {
    padding: 1.25rem;
  }
}
</style>