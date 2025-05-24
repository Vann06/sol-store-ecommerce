<template>
  <div class="about-us">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1>SolStore: Donde la Creatividad Cobra Vida en 3D</h1>
        <p>Impresión 3D profesional para makers, diseñadores y cosplayers desde 2018</p>
      </div>
    </section>

    <!-- Nuestro Propósito -->
    <section class="purpose-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h2 class="section-title">Nuestro Propósito</h2>
            <p class="section-text">
              En <strong>SolStore</strong> fusionamos tecnología y arte para ayudar a creadores a materializar sus visiones.
              Especializados en impresión 3D de alta calidad, nos apasiona ser parte del proceso creativo de cada cliente,
              desde prototipos funcionales hasta impresionantes piezas para cosplay.
            </p>
          </div>
          <div class="col-md-6">
            <div class="image-placeholder red-bg">
              [IMAGEN: Impresora 3D trabajando en pieza para cosplay]
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Nuestro Compromiso -->
    <section class="commitment-section yellow-bg">
      <div class="container">
        <h2 class="section-title">Nuestro Compromiso</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="commitment-card">
              <div class="icon red-icon">✓</div>
              <h3>Precisión en Cada Capa</h3>
              <p>Impresiones con tolerancias de ±0.1mm para piezas perfectas</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="commitment-card">
              <div class="icon red-icon">🎭</div>
              <h3>Especialistas en Cosplay</h3>
              <p>Asesoría técnica para armaduras, props y accesorios</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="commitment-card">
              <div class="icon red-icon">⚡</div>
              <h3>Rápido Turnaround</h3>
              <p>Entregas express para proyectos urgentes</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Nuestros Principios -->
    <section class="principles-section">
      <div class="container">
        <h2 class="section-title">Principios que nos Guían</h2>
        <ul class="principles-list">
          <li v-for="(principle, index) in principles" :key="index" class="principle-item">
            <span class="principle-number red-text">{{ index + 1 }}</span>
            <div>
              <h3 class="yellow-text">{{ principle.title }}</h3>
              <p>{{ principle.description }}</p>
            </div>
          </li>
        </ul>
      </div>
    </section>

    <!-- Nuestros Productos -->
    <section class="products-section yellow-bg">
      <div class="container">
        <h2 class="section-title">Lo que Ofrecemos</h2>
        <div class="row">
          <div class="col-md-4" v-for="(product, index) in products" :key="index">
            <div class="product-card">
              <div class="product-image-placeholder">
                [IMAGEN: {{ product.imageDescription }}]
              </div>
              <div class="product-icon red-bg">{{ product.icon }}</div>
              <h3 class="product-title">{{ product.name }}</h3>
              <p class="product-description">{{ product.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Público Objetivo -->
    <section class="audience-section">
      <div class="container">
        <h2 class="section-title">Nuestra Comunidad</h2>
        <div class="row">
          <div class="col-md-6">
            <div class="audience-card red-bg">
              <h3>Colaboraciones Creativas</h3>
              <ul>
                <li>Cosplayers profesionales</li>
                <li>Artistas de convenciones anime</li>
                <li>Fabricantes de props para eventos</li>
                <li>Equipos de competición cosplay</li>
                <li>Desarrolladores de indumentaria fantástica</li>
              </ul>
              <p class="mt-3">Participamos activamente en eventos como:</p>
              <div class="event-badges">
                <span class="badge yellow-bg">Anime Expo</span>
                <span class="badge yellow-bg">Comic Con</span>
                <span class="badge yellow-bg">Festivales de Cosplay</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="audience-card yellow-bg">
              <h3>Clientes Corporativos</h3>
              <ul>
                <li>Estudios de diseño de personajes</li>
                <li>Productoras de cine indie</li>
                <li>Teatros con necesidades escénicas</li>
                <li>Desarrolladores de videojuegos</li>
                <li>Escuelas de arte digital</li>
              </ul>
              <div class="image-placeholder red-bg mt-3">
                [IMAGEN: Nuestro stand en convención anime]
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Proceso de Producción -->
    <section class="process-section">
      <div class="container">
        <h2 class="section-title">Cómo Trabajamos</h2>
        <div class="process-steps">
          <div v-for="(step, index) in productionSteps" :key="index" class="process-step">
            <div class="step-number red-bg">{{ index + 1 }}</div>
            <div class="step-content">
              <h3>{{ step.title }}</h3>
              <p>{{ step.description }}</p>
              <div v-if="step.tip" class="process-tip yellow-bg">
                💡 {{ step.tip }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Final -->
    <section class="cta-section red-bg">
      <div class="container">
        <h2>¿Listo para llevar tu cosplay o proyecto al siguiente nivel?</h2>
        <button class="cta-button yellow-bg">¡Cotiza tu pieza especial!</button>
        <p class="mt-3">Síguenos en redes para ver nuestros proyectos en eventos</p>
        <div class="social-links">
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',
  data() {
    return {
      principles: [
        {
          title: 'Precisión Creativa',
          description: 'Cada pieza debe reflejar la visión del artista'
        },
        {
          title: 'Pasión por el Detalle',
          description: 'Nos obsesionamos con los acabados perfectos'
        },
        {
          title: 'Innovación Constante',
          description: 'Siempre explorando nuevos materiales y técnicas'
        },
        {
          title: 'Comunidad First',
          description: 'Crecimos en eventos y seguimos conectados'
        }
      ],
      products: [
        {
          icon: '🛡',
          name: 'Piezas para Cosplay',
          description: 'Armaduras, cascos y accesorios con terminación profesional',
          imageDescription: 'Ejemplo de armadura impresa en 3D'
        },
        {
          icon: '🎭',
          name: 'Props Personalizados',
          description: 'Armas, artefactos y elementos escénicos',
          imageDescription: 'Prop de espada impresa en 3D'
        },
        {
          icon: '🧪',
          name: 'Filamentos Especiales',
          description: 'Flexibles, termocrómicos y efecto metal',
          imageDescription: 'Variedad de filamentos de colores'
        }
      ],
      productionSteps: [
        {
          title: 'Consulta Creativa',
          description: 'Analizamos tu diseño y necesidades específicas',
          tip: 'Para cosplay: Consideramos peso y movilidad'
        },
        {
          title: 'Optimización 3D',
          description: 'Preparamos el modelo para impresión eficiente',
          tip: 'Ahorra costos con nuestros tips de diseño'
        },
        {
          title: 'Selección de Material',
          description: 'Elegimos entre 15+ filamentos especializados'
        },
        {
          title: 'Impresión Supervisada',
          description: 'Proceso monitoreado las 24/7'
        },
        {
          title: 'Acabado Artesanal',
          description: 'Lijado, pintura y efectos especiales',
          tip: 'Opciones desde básico hasta nivel exposición'
        },
        {
          title: 'Prueba de Resistencia',
          description: 'Garantizamos que soporte el uso en eventos'
        }
      ]
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

/* ======= ESTILO GENERAL DE LA PÁGINA ======= */
.about-us {
  font-family: 'Playfair Display', 'Segoe UI', serif;
  color: #333;
  line-height: 1.6;
}

/* ======= PRINCIPIOS: TÍTULOS AMARILLOS CON FONDO ROJO ======= */
.yellow-text {
  background: #7a0019;
  color: #f0c040 !important;
  padding: 10px 28px;
  border-radius: 12px;
  margin-bottom: 18px;
  display: inline-block;
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  letter-spacing: 1px;
  box-shadow: 0 2px 12px rgba(122,0,25,0.08);
}

/* ======= TIPOGRAFÍA PARA TODOS LOS TÍTULOS ======= */
h1, h2, h3, h4, h5, h6, .section-title {
  font-family: 'Playfair Display', serif;
  letter-spacing: 1px;
}

/* ======= TÍTULOS DE SECCIÓN (Nuestro Propósito, Nuestro Compromiso, etc.) ======= */
.section-title {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  margin-top: 2.5rem;
  padding-left: 32px;
  padding-right: 32px;
  padding-top: 12px;
  padding-bottom: 12px;
  display: inline-block;
  background: none;
  color: #7a0019;
  border-radius: 12px;
}

/* Línea decorativa bajo los títulos de sección */
.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 80px;
  height: 4px;
  background: #7a0019;
}

/* ======= HERO SECTION (SolStore: Donde la Creatividad Cobra Vida en 3D) ======= */
.hero-section {
  background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
              url('@/assets/images/3d-printer-hero.jpg');
  background-size: cover;
  background-position: center;
  height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 3.2rem; /* Aumentado de 3rem */
  font-weight: 700;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-content p {
  font-size: 1.8rem; /* Aumentado de 1.5rem */
  opacity: 0.9;
}

/* ======= UTILIDADES DE COLOR ======= */
.red-bg {
  background-color: #E63946;
  color: white;
}

.yellow-bg {
  background-color: #FFD166;
  color: #333;
}

.red-text {
  color: #E63946;
}

/* ======= TARJETAS DE NUESTRO COMPROMISO, LO QUE OFRECEMOS Y NUESTRA COMUNIDAD ======= */
.commitment-card, .product-card, .audience-card {
  padding: 16px 14px;
  border-radius: 8px;
  height: 100%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  background: white;
  font-size: 0.97rem;
  margin-bottom: 18px;
}

.commitment-card:hover, .product-card:hover {
  transform: translateY(-5px);
}

/* ======= TARJETAS DE NUESTRA COMUNIDAD ======= */
.audience-card {
  color: rgb(102, 2, 2);
  padding: 30px;
  height: 100%;
}

/* ======= FONDO Y COLOR DE LA SECCIÓN NUESTRA COMUNIDAD ======= */
.audience-section {
  background: #7a0019;
  color: #f0c040;
  padding-top: 40px;
  padding-bottom: 40px;
}

/* Asegura que los textos dentro de los cuadros también sean amarillos */
.audience-section .audience-card {
  background: transparent;
  color: #f0c040;
  border: 1px solid #f0c040;
}

/* Ajusta los títulos dentro de la sección */
.audience-section .audience-card h3 {
  color: #f0c040;
}

/* Ajusta los bullets y badges */
.audience-section .audience-card ul,
.audience-section .audience-card li,
.audience-section .event-badges .badge {
  color: #f0c040;
}

.audience-section .badge {
  background: #7a0019;
  color: #f0c040;
  border: 1px solid #f0c040;
}

/* ======= ICONOS EN TARJETAS ======= */
.icon {
  font-size: 2.5rem; /* Aumentado de 2rem */
  margin-bottom: 15px;
  display: inline-block;
}

.red-icon {
  color: #E63946;
}

/* ======= TARJETAS DE PRODUCTOS ======= */
.product-card {
  text-align: center;
}

.product-image-placeholder {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f5f5;
  border: 2px dashed #ccc;
  border-radius: 8px;
  margin-bottom: 15px;
  color: #666;
  font-weight: bold;
}

.product-icon {
  width: 70px; /* Aumentado de 60px */
  height: 70px; /* Aumentado de 60px */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem; /* Aumentado de 1.8rem */
  margin: -35px auto 15px auto; /* Centrado sobre la imagen */
  position: relative;
  z-index: 1;
  color: white;
}

.product-title {
  font-size: 1.8rem; /* Aumentado */
  margin: 20px 0 15px 0;
}

.product-description {
  font-size: 1.2rem; /* Aumentado */
  line-height: 1.6;
}

/* ======= PRINCIPIOS QUE NOS GUÍAN ======= */
.principles-list {
  list-style: none;
  padding: 0;
}

.principle-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 30px;
  background: #F7F7F7;
  padding: 20px;
  border-radius: 8px;
}

.principle-number {
  font-size: 2.5rem; /* Aumentado de 2rem */
  font-weight: bold;
  margin-right: 20px;
  min-width: 50px; /* Aumentado de 40px */
}

/* ======= CÓMO TRABAJAMOS (PROCESO DE PRODUCCIÓN) ======= */
.process-steps {
  position: relative;
  padding-left: 40px;
}

.process-step {
  position: relative;
  padding-bottom: 30px;
}

.step-number {
  position: absolute;
  left: -40px;
  top: 0;
  width: 60px; /* Aumentado de 50px */
  height: 60px; /* Aumentado de 50px */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem; /* Aumentado de 1.5rem */
  font-weight: bold;
  color: white;
}

.step-content {
  background: white;
  padding: 25px; /* Aumentado de 20px */
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.step-content h3 {
  font-size: 1.5rem; /* Aumentado */
}

.step-content p {
  font-size: 1.2rem; /* Aumentado */
}

/* ======= CTA FINAL ======= */
.cta-section {
  padding: 60px 0;
  text-align: center;
}

.cta-section h2 {
  font-size: 2.8rem; /* Aumentado de 2.5rem */
  margin-bottom: 30px;
}

.cta-button {
  border: none;
  padding: 15px 40px;
  font-size: 1.3rem; /* Aumentado de 1.2rem */
  font-weight: bold;
  border-radius: 50px;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}

.cta-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* ======= PLACEHOLDER DE IMÁGENES ======= */
.image-placeholder {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ccc;
  background-color: #f5f5f5;
  color: #666;
  font-weight: bold;
  border-radius: 8px;
}

/* ======= BADGES DE EVENTOS ======= */
.event-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.badge {
  padding: 8px 12px; /* Aumentado de 5px 10px */
  border-radius: 20px;
  font-size: 0.9rem; /* Aumentado de 0.8rem */
  font-weight: bold;
}

/* ======= TIP EN EL PROCESO ======= */
.process-tip {
  padding: 12px 18px; /* Aumentado de 10px 15px */
  border-radius: 5px;
  margin-top: 15px; /* Aumentado de 10px */
  display: inline-block;
  font-size: 1.1rem; /* Aumentado de 0.9rem */
}

/* ======= REDES SOCIALES ======= */
.social-links {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 15px;
}

.social-icon {
  color: white;
  font-size: 2rem; /* Aumentado de 1.8rem */
  transition: transform 0.3s;
}

.social-icon:hover {
  transform: scale(1.2);
  color: #FFD166;
}

/* ======= ESTILOS ESPECÍFICOS PARA TARJETAS ======= */
.commitment-card h3 {
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem; /* Aumentado */
}

.commitment-card p {
  font-size: 1.1rem; /* Aumentado */
}

/* ======= LISTA DE NUESTRA COMUNIDAD ======= */
.audience-card ul {
  list-style-type: '➤ ';
  padding-left: 25px;
}

.audience-card li::marker {
  color: rgb(79, 1, 1);
}

.yellow-bg .audience-card li::marker {
  color: #E63946;
}

/* ======= RESPONSIVE ======= */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.2rem; /* Ajustado */
  }
  
  .section-title {
    font-size: 2rem; /* Ajustado de 1.8rem */
  }
  
  .principle-item {
    flex-direction: column;
  }
  
  .principle-number {
    margin-bottom: 10px;
    margin-right: 0;
  }

  .product-title {
    font-size: 1.5rem;
  }

  .product-description {
    font-size: 1rem;
  }
}

@media (max-width: 992px) {
  .commitment-card,
  .product-card,
  .audience-card {
    padding: 12px 8px;
    font-size: 0.93rem;
  }
  .section-title {
    font-size: 2rem;
    padding-left: 16px;
    padding-right: 16px;
  }
}
</style>