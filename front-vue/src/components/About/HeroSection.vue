<template>
  <section class="hero-section">
    <div class="hero-background">
      <div class="hero-content">
        <div class="hero-text">
          <h1 class="hero-title">
            <span class="title-gradient">SolStore</span>
            <span class="title-subtitle">Donde la Creatividad Cobra Vida en 3D</span>
          </h1>
          <p class="hero-description">
            Impresión 3D profesional para makers, diseñadores y cosplayers desde 2018
          </p>
        </div>
        <div class="hero-badge">
          <div class="badge-gradient">
            <svg class="hero-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4"/>
              <path d="M21 12c.552 0 1-.448 1-1V5c0-.552-.448-1-1-1H3c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1h18z"/>
              <path d="M21 12v7c0 .552-.448 1-1 1H4c-.552 0-1-.448-1-1v-7"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// Componente Hero para la vista About
</script>

<style scoped>
.hero-section {
  margin-bottom: 3rem;
  position: relative;
  overflow: hidden;
}

.hero-background {
  background: linear-gradient(135deg, #fdf8f0 0%, #f4ead6 50%, #f0e4d0 100%);
  border-radius: 20px;
  padding: 3rem 2rem;
  position: relative;
}

.hero-background::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(125, 28, 43, 0.05) 0%, rgba(162, 115, 69, 0.05) 100%);
  border-radius: 20px;
  pointer-events: none;
}

.hero-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  position: relative;
  z-index: 1;
}

.hero-text {
  flex: 1;
}

.hero-title {
  margin: 0 0 1rem 0;
  line-height: 1.2;
}

.title-gradient {
  display: block;
  font-size: 3rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
}

.title-subtitle {
  display: block;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--brand-strong);
}

.hero-description {
  font-size: 1.1rem;
  color: var(--ink-3);
  margin: 0;
  max-width: 500px;
}

.hero-badge {
  flex-shrink: 0;
}

.badge-gradient {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 30px rgba(125, 28, 43, 0.3);
}

.hero-icon {
  width: 40px;
  height: 40px;
  color: white;
  stroke-width: 2;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-content {
    flex-direction: column;
    text-align: center;
  }
  
  .title-gradient {
    font-size: 2.5rem;
  }
  
  .title-subtitle {
    font-size: 1.2rem;
  }
  
  .hero-background {
    padding: 2rem 1.5rem;
  }
}

@media (max-width: 480px) {
  .title-gradient {
    font-size: 2rem;
  }
  
  .title-subtitle {
    font-size: 1rem;
  }
  
  .hero-description {
    font-size: 1rem;
  }
  
  .badge-gradient {
    width: 60px;
    height: 60px;
  }
  
  .hero-icon {
    width: 30px;
    height: 30px;
  }
}
</style>