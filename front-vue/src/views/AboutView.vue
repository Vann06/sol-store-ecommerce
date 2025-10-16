<template>
  <main class="about-page under-fixed-header bg-page-soft">
    <PageHero title="Quiénes Somos" subtitle="Conoce nuestra historia, valores y compromiso con la creatividad" variant="red" />

    <Section variant="surface" :contained="true">
      <HeroSection />
    </Section>

    <Section variant="surface" :contained="true">
      <PurposeSection />
    </Section>

    <Section variant="surface" :contained="true">
      <CommitmentSection />
    </Section>

    <Section variant="surface" :contained="true">
      <PrinciplesSection />
    </Section>

    <Section variant="surface" :contained="true">
      <ProductsSection />
    </Section>

    <Section variant="surface" :contained="true">
      <AudienceSection />
    </Section>
  </main>
</template>

<script setup>
import PageHero from '@/components/PageHero.vue'
import Section from '@/components/ui/Section.vue'
import HeroSection from '@/components/About/HeroSection.vue'
import PurposeSection from '@/components/About/PurposeSection.vue'
import CommitmentSection from '@/components/About/CommitmentSection.vue'
import PrinciplesSection from '@/components/About/PrinciplesSection.vue'
import ProductsSection from '@/components/About/ProductsSection.vue'
import AudienceSection from '@/components/About/AudienceSection.vue'
</script>

<style scoped>
.about-page { min-height: 100vh; }
</style>
