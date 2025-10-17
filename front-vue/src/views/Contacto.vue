<template>
  <main class="contact-page under-fixed-header bg-page-soft">
    <!-- Hero Banner -->
    <ContactHero />

    <!-- Información de Contacto -->
    <section class="contact-info-section">
      <div class="container">
        <div class="contact-grid">
          <!-- Información Principal -->
          <ContactInfo />

          <!-- Tarjeta de Contacto Rápido -->
          <ContactMethods />
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
// Componentes
import ContactHero from '@/components/Contact/ContactHero.vue'
import ContactInfo from '@/components/Contact/ContactInfo.vue'
import ContactMethods from '@/components/Contact/ContactMethods.vue'
</script>

<style scoped>
.contact-page {
  font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
  color: #333333;
}

/* Contact Info Section */
.contact-info-section {
  padding: 60px 0;
  background: #FAFAFA;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 40px;
  align-items: start;
}

/* Responsive */
@media (max-width: 968px) {
  .contact-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}
</style>