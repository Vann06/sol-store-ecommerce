<template>
  <section class="audience-section">
    <div class="section-content gradient-card">
      <h2 class="section-title">
        <div class="title-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        Nuestra Comunidad
      </h2>
      
      <div class="audience-grid">
        <div class="audience-card creative-card">
          <div class="card-header">
            <h3 class="card-title">Colaboraciones Creativas</h3>
            <div class="card-icon">🎨</div>
          </div>
          
          <ul class="audience-list">
            <li v-for="(item, index) in creativeAudience" :key="index" class="audience-item">
              <span class="item-icon">✓</span>
              {{ item }}
            </li>
          </ul>
          
          <div class="card-footer">
            <p class="footer-text">Participamos activamente en eventos como:</p>
            <div class="event-badges">
              <span 
                v-for="(event, index) in events" 
                :key="index"
                class="event-badge"
              >
                {{ event }}
              </span>
            </div>
          </div>
        </div>
        
        <div class="audience-card corporate-card">
          <div class="card-header">
            <h3 class="card-title">Clientes Corporativos</h3>
            <div class="card-icon">🏢</div>
          </div>
          
          <ul class="audience-list">
            <li v-for="(item, index) in corporateAudience" :key="index" class="audience-item">
              <span class="item-icon">✓</span>
              {{ item }}
            </li>
          </ul>
          
          <div class="card-footer">
            <div class="image-placeholder">
              <div class="placeholder-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                  <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
              </div>
              <span class="placeholder-text">Nuestro stand en convención anime</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const creativeAudience = ref([
  'Cosplayers profesionales',
  'Artistas de convenciones anime',
  'Fabricantes de props para eventos',
  'Equipos de competición cosplay',
  'Desarrolladores de indumentaria fantástica'
])

const corporateAudience = ref([
  'Estudios de diseño de personajes',
  'Productoras de cine indie',
  'Teatros con necesidades escénicas',
  'Desarrolladores de videojuegos',
  'Escuelas de arte digital'
])

const events = ref([
  'Anime Expo',
  'Comic Con',
  'Festivales de Cosplay'
])
</script>

<style scoped>
.audience-section {
  margin-bottom: 3rem;
}

.gradient-card {
  background: linear-gradient(#fff, #fff) padding-box,
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
  color: #7d1c2b;
  margin-bottom: 3rem;
  text-align: center;
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
  box-shadow: 0 4px 15px rgba(125, 28, 43, 0.3);
}

.title-icon svg {
  width: 28px;
  height: 28px;
  color: white;
}

.audience-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.audience-card {
  border-radius: 16px;
  padding: 2rem;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.audience-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.creative-card {
  background: linear-gradient(135deg, rgba(125, 28, 43, 0.05) 0%, rgba(162, 115, 69, 0.05) 100%);
  border: 2px solid rgba(125, 28, 43, 0.1);
}

.creative-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
}

.corporate-card {
  background: linear-gradient(135deg, rgba(162, 115, 69, 0.05) 0%, rgba(125, 28, 43, 0.05) 100%);
  border: 2px solid rgba(162, 115, 69, 0.1);
}

.corporate-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #a27345 0%, #7d1c2b 100%);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.card-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #7d1c2b;
  margin: 0;
}

.card-icon {
  font-size: 2rem;
  opacity: 0.7;
}

.audience-list {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
}

.audience-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
  font-size: 1rem;
  color: #4a5568;
  line-height: 1.5;
}

.item-icon {
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  color: white;
  flex-shrink: 0;
}

.card-footer {
  border-top: 1px solid rgba(125, 28, 43, 0.1);
  padding-top: 1.5rem;
}

.footer-text {
  font-size: 0.9rem;
  color: #6c757d;
  margin: 0 0 1rem 0;
}

.event-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.event-badge {
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.event-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(125, 28, 43, 0.3);
}

.image-placeholder {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  border: 2px dashed rgba(125, 28, 43, 0.3);
  min-height: 120px;
}

.placeholder-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, rgba(125, 28, 43, 0.1) 0%, rgba(162, 115, 69, 0.1) 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
}

.placeholder-icon svg {
  width: 24px;
  height: 24px;
  color: #7d1c2b;
}

.placeholder-text {
  font-size: 0.85rem;
  color: #6c757d;
  font-style: italic;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .gradient-card {
    padding: 2rem;
  }
  
  .audience-grid {
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .audience-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .section-title {
    font-size: 1.75rem;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .audience-card {
    padding: 1.5rem;
  }
}

@media (max-width: 480px) {
  .gradient-card {
    padding: 1.5rem;
  }
  
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
  
  .card-title {
    font-size: 1.25rem;
  }
  
  .audience-card {
    padding: 1.25rem;
  }
  
  .card-header {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
  }
  
  .image-placeholder {
    padding: 1.5rem 1rem;
    min-height: 100px;
  }
  
  .placeholder-icon {
    width: 40px;
    height: 40px;
  }
  
  .placeholder-icon svg {
    width: 20px;
    height: 20px;
  }
}
</style>