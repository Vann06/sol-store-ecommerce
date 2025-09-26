<template>
  <section class="purpose-section">
    <div class="section-content gradient-card">
      <div class="content-grid">
        <div class="text-content">
          <h2 class="section-title">
            <div class="title-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            Nuestro Propósito
          </h2>
          <p class="section-text">
            En <strong>SolStore</strong> fusionamos tecnología y arte para ayudar a creadores a materializar sus visiones.
            Especializados en impresión 3D de alta calidad, nos apasiona ser parte del proceso creativo de cada cliente,
            desde prototipos funcionales hasta impresionantes piezas para cosplay.
          </p>
          <div class="stats-grid">
            <div class="stat-item">
              <span class="stat-number">2018</span>
              <span class="stat-label">Desde</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">1000+</span>
              <span class="stat-label">Proyectos</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">99%</span>
              <span class="stat-label">Satisfacción</span>
            </div>
          </div>
        </div>
        <div class="image-content">
          <div class="image-placeholder">
            <div class="placeholder-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
              </svg>
            </div>
            <span class="placeholder-text">Impresora 3D trabajando en pieza para cosplay</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// Componente de Propósito para About
</script>

<style scoped>
.purpose-section {
  margin-bottom: 3rem;
}

.section-content {
  padding: 0;
}

.gradient-card {
  background: linear-gradient(#fff, #fff) padding-box,
              linear-gradient(135deg, rgba(240,192,64,0.5), rgba(122,0,25,0.4)) border-box;
  border: 2px solid transparent;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 2rem;
  font-weight: 700;
  color: #7d1c2b;
  margin-bottom: 1.5rem;
}

.title-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.title-icon svg {
  width: 28px;
  height: 28px;
  color: white;
}

.section-text {
  font-size: 1.1rem;
  line-height: 1.7;
  color: #4a5568;
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
  background: linear-gradient(135deg, #fdf8f0 0%, #f9f1e6 100%);
  border-radius: 12px;
  border: 1px solid rgba(125, 28, 43, 0.1);
}

.stat-number {
  display: block;
  font-size: 1.8rem;
  font-weight: 700;
  color: #7d1c2b;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.image-content {
  display: flex;
  justify-content: center;
}

.image-placeholder {
  width: 100%;
  height: 300px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 2px dashed rgba(125, 28, 43, 0.3);
  text-align: center;
  padding: 2rem;
}

.placeholder-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, rgba(125, 28, 43, 0.1) 0%, rgba(162, 115, 69, 0.1) 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.placeholder-icon svg {
  width: 40px;
  height: 40px;
  color: #7d1c2b;
}

.placeholder-text {
  font-size: 0.9rem;
  color: #6c757d;
  font-style: italic;
  max-width: 200px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .content-grid {
    gap: 2rem;
  }
  
  .gradient-card {
    padding: 2rem;
  }
}

@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .section-title {
    font-size: 1.75rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }
  
  .stat-item {
    padding: 0.75rem;
  }
  
  .stat-number {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .gradient-card {
    padding: 1.5rem;
  }
  
  .section-title {
    font-size: 1.5rem;
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }
  
  .title-icon {
    width: 40px;
    height: 40px;
  }
  
  .title-icon svg {
    width: 24px;
    height: 24px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .image-placeholder {
    height: 200px;
    padding: 1rem;
  }
  
  .placeholder-icon {
    width: 60px;
    height: 60px;
  }
  
  .placeholder-icon svg {
    width: 30px;
    height: 30px;
  }
}
</style>