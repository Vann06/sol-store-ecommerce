<template>
  <section class="catalog-hero">
    <div class="hero-content">
      <h1>Nuestro Catálogo de Impresiones 3D</h1>
      <p>Descubre piezas únicas para cosplay, colección y proyectos especiales</p>
    </div>
  </section>
</template>

<script setup>
// No necesita lógica adicional por ahora
</script>

<style scoped>
.catalog-hero {
  background: linear-gradient(135deg, #8B0000 0%, #B71C1C 100%);
  height: 40vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.hero-content p {
  font-size: 1.3rem;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 480px) {
  .hero-content h1 {
    font-size: 2rem;
  }
  
  .hero-content p {
    font-size: 1.1rem;
  }
}
</style>