<template>
    <section class="featured-themes">
      <h2 class="section-title">Temáticas destacadas</h2>
      <div class="theme-slider" ref="slider">
        <div
          v-for="(theme, index) in themes"
          :key="index"
          class="theme-card"
        >
          <img :src="theme.image" :alt="theme.name" class="theme-img" />
          <p class="theme-title">{{ theme.name }}</p>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const slider = ref(null)
  
  const themes = [
    { name: 'Harry Potter', image: '/img/theme-harrypotter.jpg' },
    { name: 'Star Wars', image: '/img/theme-starwars.jpg' },
    { name: 'Marvel', image: '/img/theme-marvel.jpg' },
    { name: 'DC Comics', image: '/img/theme-dc.jpg' },
    { name: 'Naruto', image: '/img/theme-naruto.jpg' },
    { name: 'One Piece', image: '/img/theme-onepiece.jpg' }
  ]
  
  onMounted(() => {
    const el = slider.value
    el.addEventListener('wheel', (evt) => {
      evt.preventDefault()
      el.scrollLeft += evt.deltaY
    })
  })
  </script>
  
  <style scoped>
  .featured-themes {
    padding: 40px 20px;
    background-color: #f9f9f9;
    text-align: center;
  }
  
  .section-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #8B0000;
    font-family: 'Playfair Display', serif;
  }
  
  .theme-slider {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    scroll-behavior: smooth;
    padding: 10px;
    -webkit-overflow-scrolling: touch;
  }
  
  .theme-slider::-webkit-scrollbar {
    height: 8px;
  }
  
  .theme-slider::-webkit-scrollbar-thumb {
    background: #8B0000;
    border-radius: 10px;
  }
  
  .theme-card {
    flex: 0 0 auto;
    width: 160px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    cursor: pointer;
  }
  
  .theme-card:hover {
    transform: translateY(-5px);
  }
  
  .theme-img {
    width: 100%;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
  
  .theme-title {
    padding: 10px;
    font-size: 14px;
    font-weight: bold;
    color: #333;
    font-family: 'Poppins', sans-serif;
  }
  </style>
  