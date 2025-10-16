<template>
  <section class="page-hero" :class="variant">
    <div class="hero-content">
      <h1>{{ title }}</h1>
      <p v-if="subtitle">{{ subtitle }}</p>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  variant: { type: String, default: 'red' } // red | gold | dark
})
</script>

<style scoped>
.page-hero {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  min-height: 40vh;
  padding: 32px 16px;
}

.page-hero.red {
  background: linear-gradient(135deg, #8B0000 0%, #B71C1C 100%);
}

.page-hero.gold {
  background: linear-gradient(135deg, #c79a2b 0%, #f0c040 100%);
}

.page-hero.dark {
  background: linear-gradient(135deg, #121212 0%, #1f1f1f 100%);
}

.hero-content h1 {
  font-size: 2.6rem;
  margin-bottom: 0.75rem;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
}

.hero-content p {
  font-size: 1.2rem;
  opacity: 0.92;
}

@media (max-width: 768px) {
  .hero-content h1 { font-size: 2rem; }
  .hero-content p { font-size: 1rem; }
}
</style>