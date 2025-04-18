<template>
    <section class="hero-slider">
      <div class="slider" :style="{ backgroundImage: `url(${slides[currentSlide]})` }">
        <div class="slider-overlay">
          <button class="cta">Ver Colección</button>
          <div class="dots">
            <span
              v-for="(slide, index) in slides"
              :key="index"
              :class="{ active: index === currentSlide }"
              @click="goToSlide(index)"
            ></span>
          </div>
        </div>
        <button class="arrow left" @click="prevSlide">❮</button>
        <button class="arrow right" @click="nextSlide">❯</button>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const slides = [
    '/img/hero-banner.png',
    '/img/slider2.jpg',
    '/img/slider3.jpg'
  ]
  
  const currentSlide = ref(0)
  
  const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length
  }
  
  const prevSlide = () => {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
  }
  
  const goToSlide = (index) => {
    currentSlide.value = index
  }
  
  onMounted(() => {
    setInterval(() => {
      nextSlide()
    }, 6000)
  })
  </script>
  
  <style scoped>
  .hero-slider {
    margin-top: 90px;
    width: 100%;
  }
  
  .slider {
    width: 100vw;
    height: 100vh;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: center;
  }
  
  .slider-overlay {
    width: 100%;
    padding-bottom: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
  }
  
  .cta {
    margin-bottom: 20px;
    padding: 12px 30px;
    background-color: #8B0000;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    z-index: 2;
  }
  
  .cta:hover {
    background-color: #a70000;
  }
  
  .arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.7);
    border: none;
    font-size: 36px;
    color: #8B0000;
    cursor: pointer;
    padding: 5px 10px;
    z-index: 1;
    border-radius: 4px;
  }
  
  .left {
    left: 20px;
  }
  
  .right {
    right: 20px;
  }
  
  .dots {
    display: flex;
    gap: 10px;
    justify-content: center;
  }
  
  .dots span {
    width: 12px;
    height: 12px;
    background-color: white;
    border-radius: 50%;
    border: 2px solid #8B0000;
    cursor: pointer;
  }
  
  .dots span.active {
    background-color: #8B0000;
  }
  </style>
  