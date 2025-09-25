<template>
  <div class="contact-main-info">
    <h2 class="section-title">¿Listo para crear algo increíble?</h2>
    <p class="contact-description">
      En SolStore nos encanta colaborar en proyectos creativos. Ya sea que necesites 
      asesoría técnica, cotización o tengas una idea en mente, estamos aquí para ayudarte.
    </p>
    
    <div class="contact-details">
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="contact-text">
          <h3>Horario de Atención</h3>
          <p>Lunes a Viernes: 9:00 AM - 7:00 PM</p>
          <p>Sábados: 10:00 AM - 2:00 PM</p>
        </div>
      </div>
      
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fas fa-phone"></i>
        </div>
        <div class="contact-text">
          <h3>Teléfono</h3>
          <p>+502 3127 1096</p>
          <small>Llamadas y WhatsApp</small>
        </div>
      </div>
      
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <div class="contact-text">
          <h3>Email</h3>
          <p>contacto@solstore.com</p>
          <small>Respuesta en menos de 24h</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No necesita lógica adicional por ahora
</script>

<style scoped>
/* Section Titles */
.section-title {
  position: relative;
  font-size: 2.2rem;
  margin-bottom: 2rem;
  display: inline-block;
  color: #8B0000;
  padding-bottom: 10px;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 80px;
  height: 4px;
  background: #FBC02D;
}

.contact-main-info {
  padding-right: 20px;
}

.contact-description {
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 30px;
  color: #666;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  border: 2px solid #8B0000;
}

.contact-icon {
  width: 50px;
  height: 50px;
  background: #FBC02D;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #8B0000;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.contact-text h3 {
  color: #8B0000;
  margin-bottom: 5px;
  font-size: 1.2rem;
}

.contact-text p {
  margin: 2px 0;
  font-weight: 600;
}

.contact-text small {
  color: #666;
  font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 968px) {
  .contact-main-info {
    padding-right: 0;
  }
}

@media (max-width: 768px) {
  .section-title {
    font-size: 1.8rem;
  }
  
  .contact-details {
    gap: 15px;
  }
  
  .contact-item {
    padding: 15px;
    gap: 12px;
  }
  
  .contact-icon {
    width: 45px;
    height: 45px;
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .contact-item {
    flex-direction: column;
    text-align: center;
    gap: 12px;
  }
  
  .contact-icon {
    align-self: center;
  }
}
</style>