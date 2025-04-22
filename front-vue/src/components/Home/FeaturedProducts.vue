<template>
    <section class="featured-products">
      <h2 class="section-title">Productos destacados</h2>
      <div class="products-grid">
        <div
          v-for="(product, index) in products"
          :key="index"
          class="product-card"
        >
          <img :src="product.image" :alt="product.name" class="product-img" />
          <div class="product-info">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-price">Q{{ product.price }}</p>
            <button class="btn-view">Ver más</button>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  const products = [
    { name: 'Figura de Luffy', price: 250, image: '/img/prod-luffy.jpg' },
    { name: 'Varita de Harry', price: 180, image: '/img/prod-harry.jpg' },
    { name: 'Escudo del Capitán', price: 320, image: '/img/prod-cap.jpg' },
    { name: 'Camiseta Naruto', price: 150, image: '/img/prod-naruto.jpg' },
    { name: 'Taza Star Wars', price: 90, image: '/img/prod-starwars.jpg' },
    { name: 'Llavero Batman', price: 70, image: '/img/prod-batman.jpg' }
  ]
  </script>
  
  <style scoped>
  .featured-products {
    padding: 40px 20px;
    background-color: #fff;
    text-align: center;
  }
  
  .section-title {
    font-size: 24px;
    margin-bottom: 30px;
    color: #8B0000;
    font-family: 'Playfair Display', serif;
  }
  
  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .product-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
  }
  
  .product-card:hover {
    transform: translateY(-6px);
  }
  
  .product-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  
  .product-info {
    padding: 10px 15px 20px;
  }
  
  .product-name {
    font-size: 16px;
    margin: 10px 0 5px;
    font-weight: 600;
    color: #333;
    font-family: 'Poppins', sans-serif;
  }
  
  .product-price {
    color: #8B0000;
    font-size: 14px;
    margin-bottom: 10px;
  }
  
  .btn-view {
    padding: 8px 14px;
    background-color: #8B0000;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
  }
  
  .btn-view:hover {
    background-color: #a70000;
  }
  </style>