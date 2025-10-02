<template>
  <section class="contact-hero">
    <div class="hero-content">
      <h1>Contáctanos</h1>
      <p>Estamos aquí para hacer realidad tus proyectos en 3D</p>
    </div>
  </section>
</template>

<script setup>
// No necesita lógica adicional por ahora
</script>

<style scoped>
/* Hero Section */
.contact-hero {
  background: linear-gradient(135deg, #8B0000 0%, #B71C1C 100%);
  height: 50vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
}

.hero-content p {
  font-size: 1.5rem;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.2rem;
  }
  
  .hero-content p {
    font-size: 1.2rem;
  }
}
</style>