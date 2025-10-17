<template>
  <section class="hero-slider" role="region" aria-label="Destacados">
    <div
      class="slider"
      :style="{ backgroundImage: `url(${slides[currentSlide]})` }"
      tabindex="0"
      @pointerdown="onPointerDown"
      @pointermove="onPointerMove"
      @pointerup="onPointerUp"
      @pointercancel="onPointerUp"
      @pointerleave="onPointerUp"
      @keydown.left.prevent="prevSlide()"
      @keydown.right.prevent="nextSlide()"
    >
      <div class="overlay"></div>
      <div class="slider-overlay">
        <BaseButton class="cta" variant="primary" size="large" :to="{ path: '/catalogo' }">Ver colección</BaseButton>
        <div class="dots" role="tablist" aria-label="Selector de diapositivas">
          <button
            v-for="(slide, index) in slides"
            :key="index"
            class="dot"
            :class="{ active: index === currentSlide }"
            @click="goToSlide(index)"
            :aria-selected="index === currentSlide"
            role="tab"
            :aria-label="`Ir a la diapositiva ${index+1}`"
          ></button>
        </div>
      </div>
      <button class="arrow left" @click="prevSlide" aria-label="Anterior">❮</button>
      <button class="arrow right" @click="nextSlide" aria-label="Siguiente">❯</button>
    </div>
  </section>
</template>
  
  <script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BaseButton from '@/components/BaseButton.vue'

// Use available images
const slides = [
  '/img/hero-banner-2.png',
  '/img/hero-banner.png',
  '/img/header-bg.png'
]

const currentSlide = ref(0)
let timer = null
const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches

const nextSlide = () => { currentSlide.value = (currentSlide.value + 1) % slides.length }
const prevSlide = () => { currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length }
const goToSlide = (index) => { currentSlide.value = index }

const startAuto = () => { timer = setInterval(nextSlide, 6000) }
const stopAuto = () => { if (timer) { clearInterval(timer); timer = null } }

// Basic swipe/drag to change slides
const drag = { active: false, startX: 0, dx: 0, moved: false }
const onPointerDown = (e) => { drag.active = true; drag.moved = false; drag.startX = e.clientX; drag.dx = 0; stopAuto() }
const onPointerMove = (e) => { if (!drag.active) return; drag.dx = e.clientX - drag.startX; if (Math.abs(drag.dx) > 4) drag.moved = true }
const onPointerUp = () => {
  if (!drag.active) return
  drag.active = false
  if (Math.abs(drag.dx) > 60) { drag.dx < 0 ? nextSlide() : prevSlide() }
  drag.dx = 0; drag.moved = false; startAuto()
}

onMounted(() => { if (!prefersReducedMotion) startAuto() })
onUnmounted(() => { stopAuto() })
  </script>
  
  <style scoped>
  .hero-slider { margin-top: clamp(16px, 4vw, 48px); width: 100%; }
  .slider {
    width: 100%;
    min-height: clamp(260px, 55svh, 680px);
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    outline: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(31,41,55,0.12);
  }
  .overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(240,192,64,0.38), rgba(122,0,25,0.20)); z-index: 1; }
  .slider-overlay { width: 100%; padding-bottom: clamp(16px, 4vw, 40px); display: flex; flex-direction: column; align-items: center; z-index: 2; }
  .cta { z-index: 2; }
  .arrow { position: absolute; top: 50%; transform: translateY(-50%); background: linear-gradient(135deg, var(--accent), var(--accent-2)); border: 2px solid var(--accent-2); font-size: 36px; color: var(--brand-strong); cursor: pointer; padding: 8px 12px; z-index: 2; border-radius: 12px; box-shadow: 0 8px 18px rgba(240,192,64,0.35); transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease; touch-action: manipulation; }
  .arrow:hover { transform: translateY(-50%) scale(1.04); box-shadow: 0 10px 22px rgba(240,192,64,0.45); }
  .arrow:focus { outline: none; box-shadow: 0 0 0 3px rgba(240,192,64,0.5); }
  .left { left: 20px; }
  .right { right: 20px; }
  .dots { display: flex; gap: 12px; justify-content: center; }
  .dot { width: 14px; height: 14px; background-color: #fff; border-radius: 50%; border: 2px solid var(--accent-2); cursor: pointer; box-shadow: 0 2px 6px rgba(240,192,64,0.3); display: inline-block; }
  .dot.active { background: var(--accent-2); }
  .dot:focus-visible { outline: 3px solid rgba(240,192,64,0.4); outline-offset: 2px; }

  @media (max-width: 900px) {
    .arrow { font-size: 30px; padding: 10px; }
    .left { left: 12px; }
    .right { right: 12px; }
    .slider { min-height: clamp(220px, 48svh, 560px); }
  }

  @media (max-width: 640px) {
    .arrow { font-size: 28px; padding: 10px 12px; border-radius: 10px; }
    .slider { min-height: clamp(200px, 44svh, 520px); }
  }
  </style>
  