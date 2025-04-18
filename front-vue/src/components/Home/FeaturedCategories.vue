<template>
    <section class="featured-categories">
      <h2 class="section-title">Categorías destacadas</h2>
      <div class="slider" ref="slider">
        <div
          v-for="(category, index) in categories"
          :key="index"
          class="card"
        >
          <img :src="category.image" :alt="category.name" class="card-img" />
          <p class="card-title">{{ category.name }}</p>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const slider = ref(null)
  
  const categories = [
    { name: 'Anime', image: '/img/cat-anime.jpg' },
    { name: 'Películas', image: '/img/cat-peliculas.jpg' },
    { name: 'Videojuegos', image: '/img/cat-videojuegos.jpg' },
    { name: 'Series', image: '/img/cat-series.jpg' },
    { name: 'Comics', image: '/img/cat-comics.jpg' },
    { name: 'Música', image: '/img/cat-musica.jpg' },
    { name: 'Libros', image: '/img/cat-libros.jpg' }
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
  .featured-categories {
    padding: 40px 20px;
    background-color: #fff;
    text-align: center;
  }
  
  .section-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #8B0000;
    font-family: 'Playfair Display', serif;
  }
  
  .slider {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    scroll-behavior: smooth;
    padding: 10px;
    -webkit-overflow-scrolling: touch;
  }
  
  .slider::-webkit-scrollbar {
    height: 8px;
  }
  
  .slider::-webkit-scrollbar-thumb {
    background: #8B0000;
    border-radius: 10px;
  }
  
  .card {
    flex: 0 0 auto;
    width: 160px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    cursor: pointer;
  }
  
  .card:hover {
    transform: translateY(-5px);
  }
  
  .card-img {
    width: 100%;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
  
  .card-title {
    padding: 10px;
    font-size: 14px;
    font-weight: bold;
    color: #333;
    font-family: 'Poppins', sans-serif;
  }
  </style>