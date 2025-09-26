<template>
  <main class="about-page under-fixed-header" style="--header-height: 96px;">
    <div class="about-container">
      <PageHeader
        title="Quiénes Somos"
        subtitle="Conoce nuestra historia, valores y compromiso con la creatividad" 
        icon="fas fa-users"
      />
      
      <HeroSection />
      <PurposeSection />
      <CommitmentSection />
      <PrinciplesSection />
      <ProductsSection />
      <AudienceSection />
    </div>
  </main>
</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue'
import HeroSection from '@/components/About/HeroSection.vue'
import PurposeSection from '@/components/About/PurposeSection.vue'
import CommitmentSection from '@/components/About/CommitmentSection.vue'
import PrinciplesSection from '@/components/About/PrinciplesSection.vue'
import ProductsSection from '@/components/About/ProductsSection.vue'
import AudienceSection from '@/components/About/AudienceSection.vue'
</script>

<style scoped>
.about-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #fdf8f0 0%, #f4ead6 50%, #f0e4d0 100%);
  padding-top: var(--header-height);
}

.about-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.under-fixed-header {
  padding-top: calc(var(--header-height, 96px) + 1rem);
}

@media (max-width: 1024px) {
  .about-container {
    padding: 1.5rem 0.75rem;
  }
}

@media (max-width: 768px) {
  .about-container {
    padding: 1rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .about-page {
    --header-height: 80px;
  }
  
  .about-container {
    padding: 0.75rem 0.25rem;
  }
}
</style>
