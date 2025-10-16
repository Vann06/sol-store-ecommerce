<template>
  <div class="home-page bg-white">
    <MessageNotification 
      :message="message" 
      :type="messageType"
      :isVisible="isVisible"
      @hide="hideMessage" 
    />

  <main class="home-content under-fixed-header">
      <!-- Hero (solo título) -->
      <!-- Full-bleed so the background gradient reaches all edges -->
      <Section variant="transparent" :contained="false" :padded="false">
        <div class="hero hero-full">
          <div class="hero-inner solo">
            <h1 class="hero-title geek xlarge" aria-live="polite">
              <span class="line line-1">
                <span class="word-gradient">Tu universo</span> geek
              </span>
              <span class="line line-2">a un click</span>
              <span class="rotator-row">
                <span class="rotator" aria-label="temáticas destacadas">
                  <span class="rotator-inner">
                    <span class="rotator-item">anime</span>
                    <span class="rotator-item">cosplay</span>
                    <span class="rotator-item">figuras</span>
                    <span class="rotator-item">camisetas</span>
                    <span class="rotator-item">coleccionables</span>
                    <span class="rotator-item">stickers</span>
                    <span class="rotator-item">accesorios</span>
                    <span class="rotator-item">posters</span>
                  </span>
                </span>
              </span>
            </h1>
            <p class="hero-tag">Piezas únicas, hechas con pasión. Compra fácil y recibe en casa.</p>
          </div>
        </div>
      </Section>

      <!-- Wave -->
  <WaveDivider :colorTop="'#efe5d8'" :colorBottom="'#ffffff'" :overlap="120" />

      <!-- Incentivos / Information -->
      <Section variant="surface" :contained="true">
        <Information />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#ffffff'" :colorBottom="'#fff7e6'" :flip="true" />

      <!-- Sobre nosotros preview (moved just below Information) -->
      <Section variant="surface" :contained="true">
        <SobreNosotros />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#fff7e6'" :colorBottom="'#ffffff'" />

      <!-- Slider destacado (oculto en móvil) -->
      <Section variant="transparent" :contained="true" class="hide-on-mobile">
        <HeroSlider />
      </Section>

      <!-- Intro Video -->
      <Section variant="surface" :contained="true">
        <SectionHeader title="Presentación" subtitle="Conoce en 30 segundos de qué se trata" icon="fas fa-play" />
        <IntroVideo source="/vid/video_solstore.mp4" />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#ffffff'" :colorBottom="'#fff0d1'" :flip="true" />

      <!-- Categorías destacadas -->
      <Section variant="transparent" :contained="true">
        <SectionHeader title="Categorías populares" subtitle="Explora por tipo de producto" icon="fas fa-layer-group" />
        <FeaturedCategories />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#fff0d1'" :colorBottom="'#ffffff'" />

      <!-- Temáticas -->
      <Section variant="transparent" :contained="true">
        <SectionHeader title="Temáticas destacadas" subtitle="Encuentra tu estilo" icon="fas fa-star" />
        <FeaturedThemes />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#ffffff'" :colorBottom="'#fff7e6'" :flip="true" />

      <!-- Productos destacados -->
      <Section variant="transparent" :contained="true">
        <SectionHeader title="Productos que te encantarán" subtitle="Seleccionados para ti" icon="fas fa-fire" />
        <FeaturedProducts />
      </Section>

      <!-- Wave -->
      <WaveDivider :colorTop="'#fff7e6'" :colorBottom="'#ffffff'" />

      <!-- Newsletter CTA -->
      <Section variant="surface" :contained="true">
        <NewsletterCTA />
      </Section>

      <WhatsappButton />
    </main>
  </div>
  
</template>
  
<script setup>
import { computed } from 'vue'
import HeroSlider from '@/components/Home/HeroSlider.vue'
import IntroVideo from '@/components/Home/IntroVideo.vue'
import FeaturedCategories from '@/components/Home/FeaturedCategories.vue'
import FeaturedThemes from '@/components/Home/FeaturedThemes.vue'
import FeaturedProducts from '@/components/Home/FeaturedProducts.vue'
import NewsletterCTA from '@/components/Home/NewsletterCTA.vue'
import WhatsappButton from '@/components/icons/WhatsappButton.vue'
import Information from '@/components/Home/Information.vue'
import SobreNosotros from '@/components/Home/sobreNosotros.vue'
import MessageNotification from '@/components/MessageNotification.vue'
import CartDebug from '@/components/CartDebug.vue'
import { useMessages } from '@/composables/useMessages.js'
import Section from '@/components/ui/Section.vue'
import SectionHeader from '@/components/ui/SectionHeader.vue'
import WaveDivider from '@/components/ui/WaveDivider.vue'
// BaseButton ya no se usa en el héroe simplificado

const { message, messageType, isVisible, hideMessage } = useMessages()

</script>

<style scoped>
.home-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.hero { 
  position: relative; 
  overflow: hidden; 
  /* Full-bleed background so the gradient covers left/right edges */
  width: 100vw; 
  margin-left: calc(50% - 50vw); 
  margin-right: calc(50% - 50vw);
}
.hero-full {
  /* Fallback for older browsers */
  min-height: calc(100vh - var(--header-height, 96px));
  /* Modern mobile browsers: use small viewport units to avoid URL bar jumps */
  height: calc(100svh - var(--header-height, 96px));
  display: grid; place-items: center;
  /* Soft, natural beige gradient mesh */
  background:
    radial-gradient(1100px 520px at -10% -10%, rgba(255, 236, 207, 0.70), rgba(255, 236, 207, 0.10) 60%),
    radial-gradient(900px 480px at 110% 0%, rgba(245, 229, 202, 0.55), rgba(245, 229, 202, 0.10) 60%),
    radial-gradient(800px 520px at 50% 120%, rgba(233, 210, 184, 0.40), rgba(233, 210, 184, 0.10) 60%),
    /* Base layer to ensure full coverage to every edge */
  linear-gradient(135deg, #ffffff, #ffffff);
}
.hero-inner { display: grid; place-items: center; max-width: 1200px; margin: 0 auto; padding: 40px 16px; width: 100%; }
.hero-inner.solo { grid-template-columns: 1fr; }
.hero-title { margin: 0; font-size: clamp(32px, 7vw, 72px); font-weight: 900; color: var(--ink-1); letter-spacing: -0.015em; text-align: center; line-height: 1.06; }
.hero-title.xlarge { font-size: clamp(36px, 8.5vw, 86px); }
.hero-title.geek { font-family: 'Oxanium', 'Bebas Neue', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; font-weight: 700; letter-spacing: 0.01em; }
.hero-title.red { color: var(--brand-strong); text-shadow: 0 2px 0 rgba(0,0,0,0.08); }

.line { display: block; }
.line-1 { margin-bottom: 8px; }
.line-1 { color: var(--brand-strong); }
.line-2 { color: #5b463c; }
.word-gradient {
  /* Warm subtle gradient that still stands out over beige */
  background: linear-gradient(135deg, var(--brand) 0%, #8d4a2b 45%, var(--accent-2) 100%);
  -webkit-background-clip: text; background-clip: text; color: transparent;
  /* soft lift for readability on light backgrounds */
  text-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.line-2 { display: block; }
.rotator-row { display: flex; justify-content: center; margin-top: 10px; }
.rotator {
  position: relative; display: inline-flex; align-items: center; justify-content: center;
  --rotator-item-h: 1.7em;
  --rotator-initial-shift: 0.9em;
  height: var(--rotator-item-h); overflow: hidden; padding: 0 12px; border-radius: 999px; line-height: var(--rotator-item-h);
  background: linear-gradient(135deg, rgba(255,221,75,0.22), rgba(255,192,16,0.18));
  border: 1px solid rgba(113,18,18,0.22); color: var(--brand); font-weight: 800; font-size: 0.58em;
}
.rotator-inner { display: grid; grid-auto-rows: var(--rotator-item-h); will-change: transform; }
.rotator-item { display: block; text-transform: capitalize; }

/* 8 items: slide vertically; percentages are of inner height (8 rows) */
@keyframes rotateWordsY {
  0%, 8% { transform: translateY(calc(var(--rotator-initial-shift) - 0%)); }
  12.5%, 20.5% { transform: translateY(calc(var(--rotator-initial-shift) - 12.5%)); }
  25%, 33% { transform: translateY(calc(var(--rotator-initial-shift) - 25%)); }
  37.5%, 45.5% { transform: translateY(calc(var(--rotator-initial-shift) - 37.5%)); }
  50%, 58% { transform: translateY(calc(var(--rotator-initial-shift) - 50%)); }
  62.5%, 70.5% { transform: translateY(calc(var(--rotator-initial-shift) - 62.5%)); }
  75%, 83% { transform: translateY(calc(var(--rotator-initial-shift) - 75%)); }
  87.5%, 95.5% { transform: translateY(calc(var(--rotator-initial-shift) - 87.5%)); }
  100% { transform: translateY(calc(var(--rotator-initial-shift) - 0%)); }
}
.rotator-inner { animation: rotateWordsY 16s ease-in-out infinite; }

.hero-tag { margin-top: 16px; font-size: clamp(14px, 2.6vw, 18px); color: #6a5c52; text-align: center; font-weight: 600; }

/* Optional title style variants (choose one by adding the class to the h1) */
.title-variant-minimal { color: var(--brand-strong); text-shadow: none; }
.title-variant-gradient {
  background: linear-gradient(135deg, var(--brand-strong), #d12f45 45%, var(--accent-2));
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.title-variant-neon { color: #fff; text-shadow: 0 2px 8px rgba(240,192,64,0.6), 0 0 2px rgba(240,192,64,0.4); }
.title-variant-outline { color: transparent; -webkit-text-stroke: 2px var(--brand-strong); /* text-stroke not widely supported */ }
.title-variant-caps { text-transform: uppercase; letter-spacing: 0.05em; }

/* Background logo watermark, centered and subtle */
/* removed background watermark for a cleaner hero */

/* Notification styles are handled by MessageNotification.vue */

/* Mobile adjustments */
@media (max-width: 900px) {
  .hero-title { font-size: clamp(28px, 9vw, 56px); }
  .hero-full::after { background-position: center 12px; }
  .rotator { --rotator-item-h: 1.9em; --rotator-initial-shift: 1.15em; padding: 0 10px; }
  /* Ocultar slider en móvil */
  .hide-on-mobile { display: none !important; }
}

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
  .rotator-inner { animation: none; }
}
  
</style>
